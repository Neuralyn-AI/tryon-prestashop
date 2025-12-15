<?php
/**
 * Copyright © 2025 Neuralyn.
 *
 * This module is licensed for commercial use only.
 * You are granted a non-exclusive, non-transferable license to install and use this module
 * exclusively on the domain(s) authorized in your purchase.
 *
 * You may not:
 * - redistribute or share the module or its source code;
 * - modify, decompile, or reverse engineer the module;
 * - resell or sublicense the module;
 * - install the module on additional stores without purchasing additional licenses.
 *
 * All intellectual property rights remain with Neuralyn.
 *
 * @author    Neuralyn <support@neuralyn.com.br>
 * @copyright © 2025 Neuralyn
 * @license   https://www.neuralyn.com.br/files/prestashop/license.txt Commercial license.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * API endpoint for Neuralyn TRYON - Customer UUID management.
 */
class NeuralynTryonApiModuleFrontController extends ModuleFrontController
{
    /** @var NeuralynTryon */
    public $module;

    /** @var bool */
    public $ssl = true;

    /** @var bool */
    public $display_header = false;

    /** @var bool */
    public $display_footer = false;

    /** @var bool */
    public $display_column_left = false;

    /** @var bool */
    public $display_column_right = false;

    /**
     * @return void
     */
    public function initContent()
    {
        // Do not call parent::initContent() to avoid any template rendering
        try {
            $this->processRequest();
        } catch (Exception $e) {
            $this->jsonResponse(false, 'internal_server_error', $e->getMessage(), 500);
        }
    }

    /**
     * Process the API request.
     *
     * @return void
     */
    protected function processRequest()
    {
        // Authenticate using HTTP Basic Auth with WebService key
        if (!$this->authenticateWebserviceKey()) {
            $this->jsonResponse(false, 'unauthorized', 'Unauthorized', 401);
        }

        // Only accept POST requests
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            $this->jsonResponse(false, 'method_not_allowed', 'Method not allowed', 405);
        }

        // Read and validate JSON body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate required parameters
        if (!$data || !isset($data['customerId']) || !isset($data['customerUUID'])) {
            $this->jsonResponse(false, 'missing_parameters', 'Missing parameters', 400);
        }

        $customerId = (int) $data['customerId'];
        $customerUUID = (string) $data['customerUUID'];
        $negativeCacheKey = 'neuralyn_tryon_uuid_missing_' . $customerId;
        $cacheKey = 'neuralyn_tryon_uuid_' . (int) $customerId;

        // Validate customerId is positive
        if ($customerId <= 0) {
            $this->jsonResponse(false, 'missing_parameters', 'Invalid customerId', 400);
        }

        // Check if customer exists
        $customer = new Customer($customerId);
        if (!Validate::isLoadedObject($customer)) {
            $this->jsonResponse(false, 'customer_not_found', 'Customer not found', 404);
        }

        // Check if UUID already exists for this customer
        $existingUUID = $this->getCustomerUUID($customerId);
        if (!empty($existingUUID)) {
            Cache::clean($negativeCacheKey);
            Cache::store($cacheKey, $existingUUID);

            $this->jsonResponse(true, null, null, 200, $existingUUID);
        }

        // Update customer with new UUID
        $result = Db::getInstance()->update(
            'customer',
            ['neuralyn_tryon_uuid' => pSQL($customerUUID)],
            'id_customer = ' . (int) $customerId
        );

        if (!$result) {
            $this->jsonResponse(false, 'internal_server_error', 'Failed to update customer UUID', 500);
        }
        Cache::clean($negativeCacheKey);
        Cache::store($cacheKey, $customerUUID);

        $this->jsonResponse(true, null, null, 200, $customerUUID);
    }

    /**
     * Authenticate request using HTTP Basic Auth with PrestaShop WebService key.
     *
     * @return bool
     */
    protected function authenticateWebserviceKey()
    {
        $wsKey = null;

        // Check for HTTP Basic Auth
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $wsKey = $_SERVER['PHP_AUTH_USER'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Handle Authorization header directly (some servers don't populate PHP_AUTH_USER)
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (strpos($authHeader, 'Basic ') === 0) {
                $encoded = substr($authHeader, 6);
                $decoded = base64_decode($encoded);
                if (false !== $decoded) {
                    $parts = explode(':', $decoded, 2);
                    $wsKey = $parts[0];
                }
            }
        }

        if (empty($wsKey)) {
            return false;
        }

        // Validate the key against ps_webservice_account table
        $sql = new DbQuery();
        $sql->select('id_webservice_account');
        $sql->from('webservice_account');
        $sql->where('`key` = \'' . pSQL($wsKey) . '\'');
        $sql->where('active = 1');

        $result = Db::getInstance()->getValue($sql);

        return !empty($result);
    }

    /**
     * Get the existing UUID for a customer.
     *
     * @param int $customerId
     *
     * @return string|null
     */
    protected function getCustomerUUID($customerId)
    {
        $sql = new DbQuery();
        $sql->select('neuralyn_tryon_uuid');
        $sql->from('customer');
        $sql->where('id_customer = ' . (int) $customerId);

        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? $result : null;
    }

    /**
     * Send JSON response and exit.
     *
     * @param bool $status
     * @param string|null $code
     * @param string|null $message
     * @param int $httpCode
     * @param string|null $uuid
     *
     * @return void
     */
    protected function jsonResponse($status, $code = null, $message = null, $httpCode = 200, $uuid = null)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');

        $response = ['status' => $status];

        if ($status && null !== $uuid) {
            $response['uuid'] = $uuid;
        }

        if (!$status && null !== $code) {
            $response['code'] = $code;
            $response['message'] = $message;
        }

        echo json_encode($response);
        exit;
    }
}

// Alias to match the name generated from the underscored module key.
if (!class_exists('neuralyn_tryonapiModuleFrontController', false)) {
    class neuralyn_tryonapiModuleFrontController extends NeuralynTryonApiModuleFrontController
    {
    }
}
