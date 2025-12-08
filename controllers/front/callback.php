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
 * Callback endpoint for Neuralyn.
 */
class NeuralynTryonCallbackModuleFrontController extends ModuleFrontController
{
    /** @var NeuralynTryon */
    public $module;

    /** @var bool */
    public $ssl = true;

    /** @var bool */
    public $display_column_left = false;

    /** @var bool */
    public $display_column_right = false;

    /**
     * @return void
     */
    public function initContent()
    {
        parent::initContent();

        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'METHOD_NOT_ALLOWED', $this->module->l('Method not allowed.', 'callback'), 405);
        }

        // Check if already connected
        if ($this->module->isConnected()) {
            $this->jsonResponse(false, 'ALREADY_CONNECTED', $this->module->l('Store is already connected.', 'callback'), 401);
        }

        // Check if connection_id is set
        $storedConnectionId = Configuration::get(NeuralynTryon::CONFIG_CONNECTION_ID);
        if (empty($storedConnectionId)) {
            $this->jsonResponse(false, 'NO_PENDING_CONNECTION', $this->module->l('No pending connection.', 'callback'));
        }

        // Check if connection has expired
        $expireTime = (int) Configuration::get(NeuralynTryon::CONFIG_CONNECTION_ID_EXPIRE);
        if (time() > $expireTime) {
            $this->clearConnectionId();
            $this->jsonResponse(false, 'CONNECTION_EXPIRED', $this->module->l('Connection has expired.', 'callback'));
        }

        // Read and validate JSON body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['connectionId']) || !isset($data['licenseKey']) || !isset($data['encryptKey'])) {
            $this->jsonResponse(false, 'INVALID_REQUEST_DATA', $this->module->l('Invalid request data.', 'callback'));
        }

        // Validate connection_id matches
        if ($data['connectionId'] !== $storedConnectionId) {
            $this->jsonResponse(false, 'INVALID_CONNECTION_ID', $this->module->l('Invalid connection ID.', 'callback'));
        }

        // Generate and create WebService key
        $webserviceKey = Tools::passwdGen(32);
        if (!$this->module->createWebserviceKey($webserviceKey)) {
            $this->jsonResponse(false, 'WEBSERVICE_KEY_CREATION_FAILED', $this->module->l('Failed to create WebService key.', 'callback'));
        }

        // Save keys
        $this->module->saveKeys($data['licenseKey'], $data['encryptKey']);

        // Return success with platform data
        $this->jsonResponseSuccess([
            'webservice_key' => $webserviceKey,
            'platform' => 'prestashop',
            'platform_version' => _PS_VERSION_,
            'module_version' => $this->module->version,
        ]);
    }

    /**
     * Clear connection ID from configuration.
     *
     * @return void
     */
    protected function clearConnectionId()
    {
        Configuration::deleteByName(NeuralynTryon::CONFIG_CONNECTION_ID);
        Configuration::deleteByName(NeuralynTryon::CONFIG_CONNECTION_ID_EXPIRE);
    }

    /**
     * Send JSON error response.
     *
     * @param bool $success
     * @param string|null $errorCode
     * @param string|null $message
     * @param int $statusCode
     *
     * @return void
     */
    protected function jsonResponse($success, $errorCode = null, $message = null, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = ['success' => $success];

        if (!$success) {
            $response['error_code'] = $errorCode;
            $response['message'] = $message;
        }

        $jsonResponse = json_encode($response);

        // Log the response
        $logLevel = $success ? 1 : 3; // 1 = info, 3 = error
        PrestaShopLogger::addLog(
            'Neuralyn TRYON callback response: ' . $jsonResponse,
            $logLevel,
            null,
            'NeuralynTryon'
        );

        echo $jsonResponse;
        exit;
    }

    /**
     * Send JSON success response with additional data.
     *
     * @param array $data
     *
     * @return void
     */
    protected function jsonResponseSuccess(array $data)
    {
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array_merge(['success' => true], $data);
        $jsonResponse = json_encode($response);

        PrestaShopLogger::addLog(
            'Neuralyn TRYON callback response: ' . $jsonResponse,
            1, // info
            null,
            'NeuralynTryon'
        );

        echo $jsonResponse;
        exit;
    }
}

// Alias to match the name generated from the underscored module key.
if (!class_exists('neuralyn_tryoncallbackModuleFrontController', false)) {
    class neuralyn_tryoncallbackModuleFrontController extends NeuralynTryonCallbackModuleFrontController
    {
    }
}
