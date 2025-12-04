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

NeuralynTryon::registerAlias();

class NeuralynTryon extends Module
{
    const CONFIG_WS_ID = 'NEURALYN_TRYON_WS_ID';
    const CONFIG_LICENSE_KEY = 'NEURALYN_TRYON_LICENSE_KEY';
    const CONFIG_ENCRYPT_KEY = 'NEURALYN_TRYON_ENCRYPT_KEY';
    const CONFIG_CONNECTION_ID = 'NEURALYN_TRYON_CONNECTION_ID';
    const CONFIG_CONNECTION_ID_EXPIRE = 'NEURALYN_TRYON_CONNECTION_ID_EXPIRE';
    const CONFIG_HOOKS_ENABLED = 'NEURALYN_TRYON_HOOKS_ENABLED';
    const API_BASE_URL_ENV = 'NEURALYN_TRYON_API_BASE_URL';
    const WEB_BASE_URL_ENV = 'NEURALYN_TRYON_WEB_URL';

    /**
     * Available hooks for widget placement with descriptions.
     *
     * @var array<string, string>
     */
    public static $availableHooks = [
        'displayHeader' => 'Inserido dentro do <head> da página, para carregar CSS/JS de módulos.',
        'displayReassurance' => 'Aparece logo abaixo da breadcrumb, antes da área principal do produto.',
        'displayProductCover' => 'Envolve a imagem principal grande do produto, no lado esquerdo.',
        'displayAfterProductThumbs' => 'Renderizado logo depois da lista de thumbnails da galeria.',
        'displayProductThumbs' => 'Inserido durante o loop das miniaturas, após cada thumbnail.',
        'displayProductPriceBlock_before_price' => 'Mostrado imediatamente antes de qualquer preço aparecer.',
        'displayProductPriceAndShipping' => 'Bloco principal que contém o preço atual e informações de envio.',
        'displayProductPriceBlock_old_price' => 'Mostrado no local do preço antigo riscado (para promoções).',
        'displayProductPriceBlock_unit_price' => 'Exibido onde aparece o preço por unidade (ex: por litro).',
        'displayProductPriceBlock_weight' => 'Exibido onde aparece o preço baseado no peso.',
        'displayProductPriceBlock_after_price' => 'Renderizado imediatamente após o preço final.',
        'displayProductVariants' => 'Posicionado na área de seleção de combinações (cor, tamanho, etc.).',
        'displayProductButtons' => 'Mostrado logo abaixo do botão "Adicionar ao carrinho".',
        'displayProductAdditionalInfo' => 'Inserido logo abaixo da área de botões (informações extras).',
        'displayProductCustomizationForm' => 'Renderizado dentro do bloco de personalização do produto.',
        'displayProductExtraContent' => 'Adiciona novas abas (tabs) ou conteúdo nas abas do produto.',
        'displayProductPackContent' => 'Mostrado na seção de produtos incluídos no pack (quando o produto é um pack).',
        'displayFooter' => 'Inserido no rodapé da página, antes de fechar o <body>.',
    ];

    /**
     * Module secure key.
     *
     * @var string
     */
    public $secure_key;

    /** @var array<string> */
    protected $hooks = [
        'header',
        'displayHeader',
        'backOfficeHeader',
        'displayBackOfficeHeader',
        'displayFooter',
        'displayTop',
        'displayReassurance',
        'displayProductCover',
        'displayAfterProductThumbs',
        'displayProductThumbs',
        'displayProductPriceBlock',
        'displayProductPriceAndShipping',
        'displayProductVariants',
        'displayProductButtons',
        'displayProductAdditionalInfo',
        'displayProductCustomizationForm',
        'displayProductExtraContent',
        'displayProductPackContent',
    ];

    public function __construct()
    {
        $this->name = 'neuralyn_tryon';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Neuralyn';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.6.0.0', 'max' => _PS_VERSION_];

        parent::__construct();

        // Initialize secure_key for AJAX validation
        $this->secure_key = md5($this->name . _COOKIE_KEY_);

        $this->displayName = $this->l('Neuralyn TRYON Integration');
        $this->description = $this->l('Connect your store to Neuralyn TRYON SaaS platform and enable the Neuralyn widget.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Neuralyn TRYON and revoke its access?');
    }

    /**
     * Ensure PrestaShop can load the module by its technical name with underscore.
     */
    public static function registerAlias()
    {
        if (class_exists(__CLASS__, false) && !class_exists('neuralyn_tryon', false)) {
            class_alias(__CLASS__, 'neuralyn_tryon');
        }
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        // Enable all hooks by default
        $defaultEnabledHooks = array_keys(self::$availableHooks);
        Configuration::updateValue(self::CONFIG_HOOKS_ENABLED, json_encode($defaultEnabledHooks));

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->deleteCredentials();

        foreach ($this->hooks as $hook) {
            $this->unregisterHook($hook);
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    protected function deleteCredentials()
    {
        $this->deleteWebserviceKey();

        Configuration::deleteByName(self::CONFIG_WS_ID);
        Configuration::deleteByName(self::CONFIG_LICENSE_KEY);
        Configuration::deleteByName(self::CONFIG_ENCRYPT_KEY);
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID);
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID_EXPIRE);
        Configuration::deleteByName(self::CONFIG_HOOKS_ENABLED);
    }

    /**
     * @return string|void
     */
    public function hookBackOfficeHeader()
    {
        return $this->hookDisplayBackOfficeHeader();
    }

    /**
     * @return string|void
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookHeader($params)
    {
        return $this->renderWidget();
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayHeader($params)
    {
        return $this->renderWidget();
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayTop($params)
    {
        return $this->renderWidget();
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayFooter($params)
    {
        return $this->renderHookButton('displayFooter');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayReassurance($params)
    {
        return $this->renderHookButton('displayReassurance');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductCover($params)
    {
        return $this->renderHookButton('displayProductCover');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAfterProductThumbs($params)
    {
        return $this->renderHookButton('displayAfterProductThumbs');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductThumbs($params)
    {
        return $this->renderHookButton('displayProductThumbs');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductPriceBlock($params)
    {
        $type = isset($params['type']) ? $params['type'] : '';
        $hookKey = 'displayProductPriceBlock_' . $type;

        if (!isset(self::$availableHooks[$hookKey])) {
            return '';
        }

        return $this->renderHookButton($hookKey);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductPriceAndShipping($params)
    {
        return $this->renderHookButton('displayProductPriceAndShipping');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductVariants($params)
    {
        return $this->renderHookButton('displayProductVariants');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductButtons($params)
    {
        return $this->renderHookButton('displayProductButtons');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderHookButton('displayProductAdditionalInfo');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductCustomizationForm($params)
    {
        return $this->renderHookButton('displayProductCustomizationForm');
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function hookDisplayProductExtraContent($params)
    {
        if (!$this->isHookEnabled('displayProductExtraContent')) {
            return [];
        }

        $content = '<div style="padding:20px;"><button style="background:#000;color:#fff;padding:10px 20px;border:none;cursor:pointer;font-size:14px;">displayProductExtraContent</button></div>';

        // PrestaShop 1.7+ expects array of PrestaShop\PrestaShop\Core\Product\ProductExtraContent objects
        if (class_exists('PrestaShop\\PrestaShop\\Core\\Product\\ProductExtraContent')) {
            $extraContent = new \PrestaShop\PrestaShop\Core\Product\ProductExtraContent();
            $extraContent->setTitle('Neuralyn TRYON');
            $extraContent->setContent($content);

            return [$extraContent];
        }

        // Fallback for older PrestaShop versions
        return [
            [
                'attr' => [
                    'id' => 'neuralyn-tryon-tab',
                    'class' => 'neuralyn-tryon-tab',
                ],
                'title' => 'Neuralyn TRYON',
                'content' => $content,
            ],
        ];
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductPackContent($params)
    {
        return $this->renderHookButton('displayProductPackContent');
    }

    /**
     * Get list of enabled hooks.
     *
     * @return array
     */
    public function getEnabledHooks()
    {
        $enabled = Configuration::get(self::CONFIG_HOOKS_ENABLED);
        if (empty($enabled)) {
            return array_keys(self::$availableHooks);
        }

        $decoded = json_decode($enabled, true);

        return is_array($decoded) ? $decoded : array_keys(self::$availableHooks);
    }

    /**
     * Check if a specific hook is enabled.
     *
     * @param string $hookName
     *
     * @return bool
     */
    public function isHookEnabled($hookName)
    {
        $enabled = $this->getEnabledHooks();

        return in_array($hookName, $enabled);
    }

    /**
     * Save enabled hooks configuration.
     *
     * @param array $hooks
     *
     * @return bool
     */
    public function saveEnabledHooks(array $hooks)
    {
        return Configuration::updateValue(self::CONFIG_HOOKS_ENABLED, json_encode($hooks));
    }

    /**
     * Render a hook button for testing.
     *
     * @param string $hookName
     *
     * @return string
     */
    protected function renderHookButton($hookName)
    {
        if (!$this->isHookEnabled($hookName)) {
            return '';
        }

        $this->context->smarty->assign([
            'neuralyn_hook_name' => $hookName,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/button.tpl');
    }

    /**
     * @return string
     */
    protected function renderWidget()
    {
        static $rendered = false;

        if ($rendered) {
            return '';
        }
        $rendered = true;

        $domain = Tools::getShopDomainSsl(true);
        $storeId = (int) $this->context->shop->id;

        $this->context->smarty->assign([
            'neuralyn_tryon_domain' => $domain,
            'neuralyn_tryon_store_id' => $storeId,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $output = '';

        // Handle hooks form submission
        if (Tools::isSubmit('submitNeuralynHooks')) {
            $enabledHooks = [];
            foreach (array_keys(self::$availableHooks) as $hookName) {
                if (Tools::getValue('hook_' . $hookName)) {
                    $enabledHooks[] = $hookName;
                }
            }
            $this->saveEnabledHooks($enabledHooks);
            $output .= $this->displayConfirmation($this->l('Hook settings have been saved.'));
        }

        // Handle success message from callback redirect
        if (Tools::getValue('neuralyn_success')) {
            $output .= $this->displayConfirmation($this->l('Your store has been successfully connected to Neuralyn TRYON.'));
        }

        // Handle error message from callback redirect
        $errorType = Tools::getValue('neuralyn_error');
        if ($errorType === 'connection_expired') {
            $output .= $this->displayError($this->l('The connection time has expired. Please start the connection process again.'));
        }

        $isConnected = $this->isConnected();
        $licenseKey = Configuration::get(self::CONFIG_LICENSE_KEY);
        $manageUrl = $this->getWebBaseUrl() . '/tryon/manage/?licenseKey=' . urlencode($licenseKey);

        // Prepare hooks configuration data
        $enabledHooks = $this->getEnabledHooks();
        $hooksConfig = [];
        foreach (self::$availableHooks as $hookName => $description) {
            $hooksConfig[] = [
                'name' => $hookName,
                'description' => $description,
                'enabled' => in_array($hookName, $enabledHooks),
            ];
        }

        $this->context->smarty->assign([
            'connect_url' => $this->context->link->getModuleLink($this->name, 'connect', [], true),
            'ps_version' => _PS_VERSION_,
            'module_version' => $this->version,
            'neuralyn_secure_key' => $this->getSecureKey(),
            'is_connected' => $isConnected,
            'license_key' => $licenseKey,
            'manage_url' => $manageUrl,
            'hooks_config' => $hooksConfig,
        ]);

        $output .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Get the module secure key.
     *
     * @return string
     */
    public function getSecureKey()
    {
        return $this->secure_key;
    }

    /**
     * Check if the store is connected to Neuralyn.
     *
     * @return bool
     */
    public function isConnected()
    {
        $licenseKey = Configuration::get(self::CONFIG_LICENSE_KEY);

        return !empty($licenseKey);
    }

    /**
     * Validate a connection ID against stored value and expiration.
     *
     * @param string $connectionId
     *
     * @return array
     */
    public function validateConnectionId($connectionId)
    {
        $storedId = Configuration::get(self::CONFIG_CONNECTION_ID);
        $expireTime = (int) Configuration::get(self::CONFIG_CONNECTION_ID_EXPIRE);

        if ($storedId !== $connectionId) {
            return ['valid' => false, 'error' => 'invalid_connection_id'];
        }

        if (time() > $expireTime) {
            return ['valid' => false, 'error' => 'connection_expired'];
        }

        return ['valid' => true];
    }

    /**
     * Save keys from callback.
     *
     * @param string $licenseKey
     * @param string $encryptKey
     *
     * @return bool
     */
    public function saveKeys($licenseKey, $encryptKey)
    {
        Configuration::updateValue(self::CONFIG_LICENSE_KEY, $licenseKey);
        Configuration::updateValue(self::CONFIG_ENCRYPT_KEY, $encryptKey);

        // Clear connection_id after successful use
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID);
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID_EXPIRE);

        return true;
    }

    /**
     * Get the API base URL from environment variable.
     *
     * @return string
     */
    public function getApiBaseUrl()
    {
        $url = getenv(self::API_BASE_URL_ENV);
        if (!$url) {
            $url = 'http://localhost:8787';
        }

        return rtrim($url, '/');
    }

    /**
     * Get the Web base URL from environment variable.
     *
     * @return string
     */
    public function getWebBaseUrl()
    {
        $url = getenv(self::WEB_BASE_URL_ENV);
        if (!$url) {
            $url = 'http://localhost:3000';
        }

        return rtrim($url, '/');
    }

    /**
     * Connect to Neuralyn API and return redirect URL.
     *
     * @return array
     */
    public function connect()
    {
        $connectionId = Tools::passwdGen(64);
        $webserviceKey = Tools::passwdGen(32);
        $connectionIdExpire = time() + (15 * 60); // 15 minutes from now

        // Save connection_id and expiration for later validation
        Configuration::updateValue(self::CONFIG_CONNECTION_ID, $connectionId);
        Configuration::updateValue(self::CONFIG_CONNECTION_ID_EXPIRE, $connectionIdExpire);

        if (!$this->createWebserviceKey($webserviceKey)) {
            return ['success' => false, 'error' => $this->l('Failed to create WebService key.')];
        }

        $domain = Tools::getShopDomainSsl(true);
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        $payload = [
            'connection_id' => $connectionId,
            'webservice_key' => $webserviceKey,
            'domain' => $domain,
            'platform' => 'prestashop',
            'platform_version' => _PS_VERSION_,
            'module_version' => $this->version,
            'service_token' => 'tryon',
            'callback_url' => $this->context->link->getModuleLink($this->name, 'callback', [], true),
        ];

        $apiUrl = $this->getApiBaseUrl() . '/encrypt-webservice-key';
        $response = $this->callExternalApi($apiUrl, $payload);

        if (!$response['success']) {
            return ['success' => false, 'error' => isset($response['error']) ? $response['error'] : $this->l('API request failed.')];
        }

        $data = json_decode($response['response'], true);
        if (!isset($data['redirect_url'])) {
            return ['success' => false, 'error' => $this->l('Invalid API response.')];
        }

        return ['success' => true, 'redirect_url' => $data['redirect_url']];
    }

    /**
     * Create a WebService key in PrestaShop.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function createWebserviceKey($key)
    {
        if (!class_exists('WebserviceKey')) {
            return false;
        }

        $wsKey = new WebserviceKey();
        $wsKey->key = $key;
        $wsKey->description = 'Neuralyn TRYON Integration';
        $wsKey->active = true;

        // In FO context there is no employee, but WebserviceKey::add() logs with Context::getContext()->employee->id.
        $context = Context::getContext();
        $originalEmployee = isset($context->employee) ? $context->employee : null;
        if (!is_object($context->employee) || !isset($context->employee->id)) {
            $placeholderEmployee = new stdClass();
            $placeholderEmployee->id = 0;
            $context->employee = $placeholderEmployee;
        }

        $addResult = $wsKey->add();

        // Restore previous employee to avoid side effects on the context.
        $context->employee = $originalEmployee;

        if (!$addResult) {
            return false;
        }

        $permissions = [
            'customers' => ['GET' => 1],
            'orders' => ['GET' => 1],
            'products' => ['GET' => 1],
        ];

        if (method_exists('WebserviceKey', 'setPermissionForAccount')) {
            WebserviceKey::setPermissionForAccount((int) $wsKey->id, $permissions);
        } else {
            foreach ($permissions as $resource => $methods) {
                foreach ($methods as $method => $value) {
                    Db::getInstance()->insert('webservice_permission', [
                        'resource' => pSQL($resource),
                        'method' => pSQL($method),
                        'id_webservice_account' => (int) $wsKey->id,
                    ]);
                }
            }
        }

        Configuration::updateValue(self::CONFIG_WS_ID, (int) $wsKey->id);

        return true;
    }

    /**
     * @return void
     */
    protected function deleteWebserviceKey()
    {
        $wsId = (int) Configuration::get(self::CONFIG_WS_ID);

        if ($wsId > 0 && class_exists('WebserviceKey')) {
            $ws = new WebserviceKey($wsId);
            if (Validate::isLoadedObject($ws)) {
                $ws->delete();
            }
        }
    }

    /**
     * @return string
     */
    public function getWebserviceBaseUrl()
    {
        return Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . 'api/';
    }

    /**
     * @param string $url
     * @param array $payload
     *
     * @return array
     */
    public function callExternalApi($url, array $payload)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => json_encode($payload),
                    'timeout' => 15,
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            $status = 200;
            $error = '';

            if (isset($http_response_header[0])) {
                if (preg_match('#HTTP/[0-9\\.]+\\s+(\\d+)#', $http_response_header[0], $matches)) {
                    $status = (int) $matches[1];
                }
            }
        }

        if ($response === false || $status >= 400) {
            PrestaShopLogger::addLog('Neuralyn TRYON API call failed: ' . $error . ' status:' . $status, 3, null, 'Neuralyn TRYON');

            return ['success' => false, 'status' => $status, 'error' => $error, 'response' => $response];
        }

        return ['success' => true, 'status' => $status, 'response' => $response];
    }
}
