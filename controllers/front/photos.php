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
 * API endpoint for Neuralyn TRYON - Customer photos management.
 */
class NeuralynTryonPhotosModuleFrontController extends ModuleFrontController
{
    /** @var NeuralynTryon */
    public $module;

    /** @var int Rate limit window in seconds */
    const RATE_LIMIT_WINDOW = 60;

    /** @var int Maximum requests per window */
    const RATE_LIMIT_MAX_REQUESTS = 10;

    /** @var int Block time multiplier for progressive blocking */
    const RATE_LIMIT_BLOCK_MULTIPLIER = 2;

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
        // Check rate limit first
        $this->checkRateLimit();

        // Only accept POST requests
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            $this->jsonResponse(false, 'method_not_allowed', 'Method not allowed', 405);
        }

        // Read and validate JSON body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['token'])) {
            $this->jsonResponse(false, 'missing_token', 'Missing token', 400);
        }

        // Validate PrestaShop static token
        if (!$this->validateStaticToken($data['token'])) {
            $this->jsonResponse(false, 'invalid_token', 'Invalid token', 401);
        }

        // Validate license key
        if (!isset($data['licenseKey']) || empty($data['licenseKey'])) {
            $this->jsonResponse(false, 'missing_license_key', 'Missing license key', 400);
        }

        if (!$this->validateLicenseKey($data['licenseKey'])) {
            $this->jsonResponse(false, 'invalid_license_key', 'Invalid license key', 401);
        }

        // Check if user is logged in
        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            $this->jsonResponse(false, 'not_logged_in', 'User not logged in', 401);
        }

        $customerId = (int) $this->context->customer->id;

        // Get action from URL parameter
        $action = Tools::getValue('action');

        switch ($action) {
            case 'add':
                $this->handleAdd($customerId, $data);
                break;
            case 'delete':
                $this->handleDelete($customerId, $data);
                break;
            default:
                $this->jsonResponse(false, 'invalid_action', 'Invalid action. Use: add, delete', 400);
        }
    }

    /**
     * Handle add photo action (upsert).
     *
     * @param int $customerId
     * @param array $data
     *
     * @return void
     */
    protected function handleAdd($customerId, $data)
    {
        if (!isset($data['photo']) || empty($data['photo'])) {
            $this->jsonResponse(false, 'missing_photo', 'Missing photo UUID', 400);
        }

        $photoUuid = (string) $data['photo'];

        // Validate UUID format
        if (!$this->isValidUuid($photoUuid)) {
            $this->jsonResponse(false, 'invalid_uuid', 'Invalid UUID format', 400);
        }

        // Determine storage key based on photoType
        $photoType = isset($data['photoType']) ? (string) $data['photoType'] : 'product';

        if ('customer' === $photoType) {
            $storageKey = 'customer_photo';
        } else {
            // For product type, productId is required
            if (!isset($data['productId']) || empty($data['productId'])) {
                $this->jsonResponse(false, 'missing_product_id', 'Missing product ID', 400);
            }
            $storageKey = (string) $data['productId'];
        }

        $photos = $this->getCustomerPhotos($customerId);

        // Upsert: add or update storageKey => uuid
        $photos[$storageKey] = $photoUuid;

        // Save and cache
        if ($this->saveCustomerPhotos($customerId, $photos)) {
            $this->jsonResponse(true, null, null, 200, $photos);
        } else {
            $this->jsonResponse(false, 'save_failed', 'Failed to save photo', 500);
        }
    }

    /**
     * Handle delete photo action.
     *
     * @param int $customerId
     * @param array $data
     *
     * @return void
     */
    protected function handleDelete($customerId, $data)
    {
        // Determine storage key based on photoType
        $photoType = isset($data['photoType']) ? (string) $data['photoType'] : 'product';

        if ('customer' === $photoType) {
            $storageKey = 'customer_photo';
        } else {
            // For product type, productId is required
            if (!isset($data['productId']) || empty($data['productId'])) {
                $this->jsonResponse(false, 'missing_product_id', 'Missing product ID', 400);
            }
            $storageKey = (string) $data['productId'];
        }

        $photos = $this->getCustomerPhotos($customerId);

        // Remove by storageKey
        if (isset($photos[$storageKey])) {
            unset($photos[$storageKey]);
        }

        // Save and cache
        if ($this->saveCustomerPhotos($customerId, $photos)) {
            $this->jsonResponse(true, null, null, 200, $photos);
        } else {
            $this->jsonResponse(false, 'save_failed', 'Failed to delete photo', 500);
        }
    }

    /**
     * Validate UUID format (v4).
     *
     * @param string $uuid
     *
     * @return bool
     */
    protected function isValidUuid($uuid)
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

        return (bool) preg_match($pattern, $uuid);
    }

    /**
     * Validate license key against stored value with caching.
     *
     * @param string $licenseKey
     *
     * @return bool
     */
    protected function validateLicenseKey($licenseKey)
    {
        $cacheKey = 'neuralyn_tryon_license_key';

        // Try cache first
        if (Cache::isStored($cacheKey)) {
            $cachedKey = Cache::retrieve($cacheKey);

            return $cachedKey === $licenseKey;
        }

        // Get from configuration
        $storedKey = Configuration::get(NeuralynTryon::CONFIG_LICENSE_KEY);

        if (empty($storedKey)) {
            return false;
        }

        // Store in cache (1 hour TTL handled by PrestaShop cache system)
        Cache::store($cacheKey, $storedKey);

        return $storedKey === $licenseKey;
    }

    /**
     * Get customer photos from cache or database.
     *
     * @param int $customerId
     *
     * @return array Associative array [productId => uuid]
     */
    protected function getCustomerPhotos($customerId)
    {
        $cacheKey = 'neuralyn_tryon_photos_' . (int) $customerId;

        // Try cache first
        if (Cache::isStored($cacheKey)) {
            $cached = Cache::retrieve($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        }

        // Get from database
        $sql = new DbQuery();
        $sql->select('neuralyn_tryon_photos');
        $sql->from('customer');
        $sql->where('id_customer = ' . (int) $customerId);

        $result = Db::getInstance()->getValue($sql);

        if (empty($result)) {
            $photos = [];
        } else {
            $decoded = json_decode($result, true);
            $photos = is_array($decoded) ? $decoded : [];
        }

        // Store in cache
        Cache::store($cacheKey, $photos);

        return $photos;
    }

    /**
     * Save customer photos to database and update cache.
     *
     * @param int $customerId
     * @param array $photos Associative array [productId => uuid]
     *
     * @return bool
     */
    protected function saveCustomerPhotos($customerId, $photos)
    {
        $cacheKey = 'neuralyn_tryon_photos_' . (int) $customerId;

        // Encode as JSON object (preserves keys)
        $jsonPhotos = empty($photos) ? '{}' : json_encode($photos, JSON_FORCE_OBJECT);

        $result = Db::getInstance()->update(
            'customer',
            ['neuralyn_tryon_photos' => pSQL($jsonPhotos)],
            'id_customer = ' . (int) $customerId
        );

        if ($result) {
            // Update cache
            Cache::clean($cacheKey);
            Cache::store($cacheKey, $photos);
        }

        return $result;
    }

    /**
     * Validate PrestaShop static token.
     *
     * @param string $token
     *
     * @return bool
     */
    protected function validateStaticToken($token)
    {
        return !empty($token) && $token === Tools::getToken(false);
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    protected function getClientIp()
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '0.0.0.0';
        }

        return $ip;
    }

    /**
     * Check rate limit for current IP.
     *
     * @return void
     */
    protected function checkRateLimit()
    {
        $ip = $this->getClientIp();
        $cacheKey = 'neuralyn_tryon_rate_' . md5($ip);
        $now = time();

        // Get current rate limit data
        $rateData = [
            'count' => 0,
            'window_start' => $now,
            'violations' => 0,
            'blocked_until' => 0,
        ];

        if (Cache::isStored($cacheKey)) {
            $cached = Cache::retrieve($cacheKey);
            if (is_array($cached)) {
                $rateData = array_merge($rateData, $cached);
            }
        }

        // Check if currently blocked
        if ($rateData['blocked_until'] > $now) {
            $retryAfter = $rateData['blocked_until'] - $now;
            header('Retry-After: ' . $retryAfter);
            $this->jsonResponse(false, 'rate_limit_exceeded', 'Too many requests. Retry after ' . $retryAfter . ' seconds.', 429);
        }

        // Reset window if expired
        if (($now - $rateData['window_start']) >= self::RATE_LIMIT_WINDOW) {
            $rateData['count'] = 0;
            $rateData['window_start'] = $now;
        }

        // Increment counter
        $rateData['count']++;

        // Check if limit exceeded
        if ($rateData['count'] > self::RATE_LIMIT_MAX_REQUESTS) {
            $rateData['violations']++;
            $blockTime = self::RATE_LIMIT_WINDOW * pow(self::RATE_LIMIT_BLOCK_MULTIPLIER, $rateData['violations'] - 1);
            $rateData['blocked_until'] = $now + $blockTime;
            $rateData['count'] = 0;
            $rateData['window_start'] = $now;

            Cache::clean($cacheKey);
            Cache::store($cacheKey, $rateData);

            header('Retry-After: ' . $blockTime);
            $this->jsonResponse(false, 'rate_limit_exceeded', 'Too many requests. Retry after ' . $blockTime . ' seconds.', 429);
        }

        // Update cache
        Cache::clean($cacheKey);
        Cache::store($cacheKey, $rateData);
    }

    /**
     * Send JSON response and exit.
     *
     * @param bool $status
     * @param string|null $code
     * @param string|null $message
     * @param int $httpCode
     * @param array|null $photos
     *
     * @return void
     */
    protected function jsonResponse($status, $code = null, $message = null, $httpCode = 200, $photos = null)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');

        $response = ['status' => $status];

        if ($status && null !== $photos) {
            $response['photos'] = $photos;
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
if (!class_exists('neuralyn_tryonphotosModuleFrontController', false)) {
    class neuralyn_tryonphotosModuleFrontController extends NeuralynTryonPhotosModuleFrontController
    {
    }
}
