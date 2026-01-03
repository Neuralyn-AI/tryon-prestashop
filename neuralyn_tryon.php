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
    public $cached = false;

    public const CONFIG_WS_ID = 'NEURALYN_TRYON_WS_ID';
    public const CONFIG_LICENSE_KEY = 'NEURALYN_TRYON_LICENSE_KEY';
    public const CONFIG_HOOKS_ENABLED = 'NEURALYN_TRYON_HOOKS_ENABLED';
    public const CONFIG_HOOKS_LOCATION = 'NEURALYN_TRYON_HOOKS_LOCATION';
    public const CONFIG_BUTTON_STYLE = 'NEURALYN_TRYON_BTN_STYLE';
    public const CONFIG_BUTTON_FLOAT_RIGHT = 'NEURALYN_TRYON_BTN_FLOAT_RIGHT';
    public const CONFIG_BUYER_ORDER_STATUSES = 'NEURALYN_TRYON_BUYER_ORDER_STATUSES';
    public const CONFIG_API_BASE_URL = 'NEURALYN_TRYON_API_BASE_URL';
    public const CONFIG_WEB_BASE_URL = 'NEURALYN_TRYON_WEB_URL';
    #public const NEURALYN_CONNECT_WEB_BASE_URL = 'http://127.0.0.1:8222';
    #public const NEURALYN_CDN_URL = 'http://localhost:8222';
    public const NEURALYN_CDN_URL = 'https://www.neuralyn.com.br';
    public const NEURALYN_CONNECT_WEB_BASE_URL = 'https://tryon-cdn.neuralyn.ai';

    public const LOCATION_PRODUCT = 'product';
    public const LOCATION_LISTING = 'listing';
    public const LOCATION_BOTH = 'both';

    public const STYLE_ANIMATED = 'animated';
    public const STYLE_BLACK = 'black';
    public const STYLE_PINK = 'pink';
    public const STYLE_DARK_BLUE = 'dark-blue';
    public const STYLE_LIGHT_BLUE = 'light-blue';
    public const STYLE_GREEN = 'green';
    public const STYLE_WHITE = 'white';
    public const STYLE_GRAY = 'gray';
    public const STYLE_RED = 'red';
    public const STYLE_ORANGE = 'orange';
    public const STYLE_PURPLE = 'purple';
    public const STYLE_CUSTOM = 'custom';

    /**
     * Available button styles with labels.
     *
     * @var array<string, string>
     */
    public static $buttonStyles = [
        'animated' => 'Animated',
        'black' => 'Black',
        'pink' => 'Pink',
        'dark-blue' => 'Dark blue',
        'light-blue' => 'Light blue',
        'green' => 'Green',
        'white' => 'White',
        'gray' => 'Gray',
        'red' => 'Red',
        'orange' => 'Orange',
        'purple' => 'Purple',
        'custom' => 'Custom',
    ];

    /**
     * Available hooks for widget placement with descriptions.
     *
     * @var array<string, string>
     */
    public static $availableHooks = [
        'displayProductPriceBlock_after_price' => 'Renderizado imediatamente após o preço final.',
        'displayAfterProductThumbs' => 'Renderizado logo depois da lista de thumbnails da galeria.',
        'displayReassurance' => 'Aparece logo abaixo da breadcrumb, antes da área principal do produto.',
        'displayProductAdditionalInfo' => 'Inserido logo abaixo da área de botões (informações extras).',
        'displayProductExtraContent' => 'Adiciona novas abas (tabs) ou conteúdo nas abas do produto.',
        'displayProductPriceBlock_old_price' => 'Mostrado no local do preço antigo riscado (para promoções).',
        'displayProductPriceBlock_before_price' => 'Mostrado imediatamente antes de qualquer preço aparecer.',
        'displayProductPriceBlock_unit_price' => 'Exibido onde aparece o preço por unidade (ex: por litro).',
        'displayProductPriceBlock_weight' => 'Exibido onde aparece o preço baseado no peso.',
    ];

    /**
     * Hooks that have location options (product page, listing, or both).
     *
     * @var array<string>
     */
    public static $hooksWithLocationOption = [
        'displayProductPriceBlock_old_price',
        'displayProductPriceBlock_before_price',
        'displayProductPriceBlock_unit_price',
        'displayProductPriceBlock_weight',
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
        $this->tab = 'front_office_features';
        $this->version = '1.0.7';
        $this->author = 'Neuralyn';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

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

        // Register widget hook
        $this->registerHook('displayFooterProduct');

        // Add UUID field to customers table, if not exists
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'customer` LIKE "neuralyn_tryon_uuid"');

        if (!$columns) {
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer`
                    ADD `neuralyn_tryon_uuid` VARCHAR(255) NULL';
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }

        // Add images field to customers table, if not exists
        $imagesColumn = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'customer` LIKE "neuralyn_tryon_photos"');

        if (!$imagesColumn) {
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer`
                    ADD `neuralyn_tryon_photos` JSON NULL';
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        // Enable only displayProductPriceBlock_after_price by default
        $defaultEnabledHooks = ['displayProductPriceBlock_after_price'];
        Configuration::updateValue(self::CONFIG_HOOKS_ENABLED, json_encode($defaultEnabledHooks));

        // Set default location for hooks with location option
        $defaultLocations = [];
        foreach (self::$hooksWithLocationOption as $hookName) {
            $defaultLocations[$hookName] = self::LOCATION_PRODUCT;
        }
        Configuration::updateValue(self::CONFIG_HOOKS_LOCATION, json_encode($defaultLocations));

        // Set default button style
        Configuration::updateValue(self::CONFIG_BUTTON_STYLE, self::STYLE_ANIMATED);
        Configuration::updateValue(self::CONFIG_BUTTON_FLOAT_RIGHT, '0');

        // Set default buyer order statuses (paid, shipped, delivered)
        $defaultBuyerStatuses = $this->getDefaultBuyerStatuses();
        Configuration::updateValue(self::CONFIG_BUYER_ORDER_STATUSES, json_encode($defaultBuyerStatuses));

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
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID);
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID_EXPIRE);
        Configuration::deleteByName(self::CONFIG_HOOKS_ENABLED);
        Configuration::deleteByName(self::CONFIG_HOOKS_LOCATION);
        Configuration::deleteByName(self::CONFIG_BUTTON_STYLE);
        Configuration::deleteByName(self::CONFIG_BUTTON_FLOAT_RIGHT);
        Configuration::deleteByName(self::CONFIG_BUYER_ORDER_STATUSES);
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
            # Load SDK in the admin backend to get the css styles from buttons
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
        if ($this->isExcludedPage()) {
            return [];
        }

        if (!$this->isHookEnabled('displayProductExtraContent')) {
            return [];
        }

        $content = $this->display(__FILE__, 'views/templates/hook/displayProductExtraContent.tpl');

        // PrestaShop 1.7+ expects array of PrestaShop\PrestaShop\Core\Product\ProductExtraContent objects
        if (class_exists('PrestaShop\\PrestaShop\\Core\\Product\\ProductExtraContent')) {
            $extraContent = new PrestaShop\PrestaShop\Core\Product\ProductExtraContent();
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
     * @return bool
     */
    public function saveEnabledHooks(array $hooks)
    {
        return Configuration::updateValue(self::CONFIG_HOOKS_ENABLED, json_encode($hooks));
    }

    /**
     * Get hook locations configuration.
     *
     * @return array
     */
    public function getHookLocations()
    {
        $locations = Configuration::get(self::CONFIG_HOOKS_LOCATION);
        if (empty($locations)) {
            $default = [];
            foreach (self::$hooksWithLocationOption as $hookName) {
                $default[$hookName] = self::LOCATION_PRODUCT;
            }

            return $default;
        }

        $decoded = json_decode($locations, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Get location for a specific hook.
     *
     * @param string $hookName
     *
     * @return string
     */
    public function getHookLocation($hookName)
    {
        $locations = $this->getHookLocations();

        return isset($locations[$hookName]) ? $locations[$hookName] : self::LOCATION_PRODUCT;
    }

    /**
     * Save hook locations configuration.
     *
     * @return bool
     */
    public function saveHookLocations(array $locations)
    {
        return Configuration::updateValue(self::CONFIG_HOOKS_LOCATION, json_encode($locations));
    }

    /**
     * Get button style configuration.
     *
     * @return string
     */
    public function getButtonStyle()
    {
        $style = Configuration::get(self::CONFIG_BUTTON_STYLE);

        return !empty($style) && isset(self::$buttonStyles[$style]) ? $style : self::STYLE_ANIMATED;
    }

    /**
     * Save button style configuration.
     *
     * @param string $style
     *
     * @return bool
     */
    public function saveButtonStyle($style)
    {
        if (!isset(self::$buttonStyles[$style])) {
            $style = self::STYLE_ANIMATED;
        }

        return Configuration::updateValue(self::CONFIG_BUTTON_STYLE, $style);
    }

    /**
     * Get button float right configuration.
     *
     * @return bool
     */
    public function getButtonFloatRight()
    {
        return (bool) Configuration::get(self::CONFIG_BUTTON_FLOAT_RIGHT);
    }

    /**
     * Save button float right configuration.
     *
     * @param bool $enabled
     *
     * @return bool
     */
    public function saveButtonFloatRight($enabled)
    {
        return Configuration::updateValue(self::CONFIG_BUTTON_FLOAT_RIGHT, $enabled ? '1' : '0');
    }

    /**
     * Get buyer order statuses configuration.
     *
     * @return array
     */
    public function getBuyerOrderStatuses()
    {
        $statuses = Configuration::get(self::CONFIG_BUYER_ORDER_STATUSES);
        if (empty($statuses)) {
            return [];
        }

        $decoded = json_decode($statuses, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Save buyer order statuses configuration.
     *
     * @return bool
     */
    public function saveBuyerOrderStatuses(array $statuses)
    {
        return Configuration::updateValue(self::CONFIG_BUYER_ORDER_STATUSES, json_encode($statuses));
    }

    /**
     * Get default buyer order statuses (paid, shipped, or delivered).
     *
     * @return array
     */
    private function getDefaultBuyerStatuses()
    {
        $defaultStatuses = [];
        $orderStates = OrderState::getOrderStates($this->context->language->id);

        foreach ($orderStates as $state) {
            if (!empty($state['paid']) || !empty($state['shipped']) || !empty($state['delivery'])) {
                $defaultStatuses[] = (int) $state['id_order_state'];
            }
        }

        return $defaultStatuses;
    }

    /**
     * Check if current page is excluded (cart, checkout, order).
     *
     * @return bool
     */
    protected function isExcludedPage()
    {
        $controller = Dispatcher::getInstance()->getController();
        $excluded = ['cart', 'order', 'orderopc', 'checkout', 'order-confirmation'];

        return in_array($controller, $excluded);
    }

    /**
     * Check if current page is a product page.
     *
     * @return bool
     */
    protected function isProductPage()
    {
        $controller = Dispatcher::getInstance()->getController();

        return 'product' === $controller;
    }

    /**
     * Check if current page is a listing page.
     *
     * @return bool
     */
    protected function isListingPage()
    {
        $controller = Dispatcher::getInstance()->getController();
        $listings = ['category', 'search', 'manufacturer', 'supplier', 'new-products', 'prices-drop', 'best-sales'];

        return in_array($controller, $listings);
    }

    /**
     * Check if hook should be displayed based on location settings.
     *
     * @param string $hookName
     *
     * @return bool
     */
    protected function shouldDisplayHookByLocation($hookName)
    {
        if (!in_array($hookName, self::$hooksWithLocationOption)) {
            return true;
        }

        $location = $this->getHookLocation($hookName);

        if (self::LOCATION_BOTH === $location) {
            return true;
        }

        if (self::LOCATION_PRODUCT === $location && $this->isProductPage()) {
            return true;
        }

        if (self::LOCATION_LISTING === $location && $this->isListingPage()) {
            return true;
        }

        return false;
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
        // Never show on cart, checkout, or order pages
        if ($this->isExcludedPage()) {
            return '';
        }

        // Check if hook is enabled
        if (!$this->isHookEnabled($hookName)) {
            return '';
        }

        // displayReassurance only on product page
        if ('displayReassurance' === $hookName && !$this->isProductPage()) {
            return '';
        }

        // Check location settings for hooks with location option
        if (!$this->shouldDisplayHookByLocation($hookName)) {
            return '';
        }

        $buttonStyle = $this->getButtonStyle();

        $this->context->smarty->assign([
            'neuralyn_hook_name' => $hookName,
            'neuralyn_button_style' => $buttonStyle,
            'neuralyn_button_float_right' => $this->getButtonFloatRight(),
            'display_none' => 'display: none'
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
            'neuralyn_front_css_url' => self::NEURALYN_CDN_URL . '/styles.min.css',
        ]);

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        // Ensure all hooks are registered (for upgrades)
        foreach ($this->hooks as $hook) {
            if (!$this->isRegisteredInHook($hook)) {
                $this->registerHook($hook);
            }
        }

        $output = '';

        // Handle hooks form submission
        if (Tools::isSubmit('submitNeuralynHooks')) {
            $enabledHooks = [];
            foreach (array_keys(self::$availableHooks) as $hookName) {
                if ('1' === Tools::getValue('hook_' . $hookName)) {
                    $enabledHooks[] = $hookName;
                }
            }
            $this->saveEnabledHooks($enabledHooks);

            // Save hook locations
            $hookLocations = [];
            foreach (self::$hooksWithLocationOption as $hookName) {
                $location = Tools::getValue('hook_location_' . $hookName, self::LOCATION_PRODUCT);
                if (in_array($location, [self::LOCATION_PRODUCT, self::LOCATION_LISTING, self::LOCATION_BOTH])) {
                    $hookLocations[$hookName] = $location;
                }
            }
            $this->saveHookLocations($hookLocations);

            $output .= $this->displayConfirmation($this->l('Hook settings have been saved.'));
        }

        // Handle button design form submission
        if (Tools::isSubmit('submitNeuralynButtonDesign')) {
            $buttonStyle = Tools::getValue('button_style', self::STYLE_ANIMATED);
            $this->saveButtonStyle($buttonStyle);
            $floatRight = '1' === Tools::getValue('button_float_right');
            $this->saveButtonFloatRight($floatRight);
            $output .= $this->displayConfirmation($this->l('Button design settings have been saved.'));
        }

        // Handle buyer order statuses form submission
        if (Tools::isSubmit('submitNeuralynBuyerStatuses')) {
            $selectedStatuses = Tools::getValue('buyer_order_statuses');
            if (!is_array($selectedStatuses)) {
                $selectedStatuses = [];
            }
            $this->saveBuyerOrderStatuses(array_map('intval', $selectedStatuses));
            $output .= $this->displayConfirmation($this->l('Buyer order statuses have been saved.'));
        }

        // Handle license key form submission
        if (Tools::isSubmit('submitNeuralynLicenseKey')) {
            $licenseKey = Tools::getValue('neuralyn_license_key');
            Configuration::updateValue(self::CONFIG_LICENSE_KEY, pSQL($licenseKey));
            $output .= $this->displayConfirmation($this->l('License key has been saved.'));
        }

        // Handle success message from callback redirect
        if (Tools::getValue('neuralyn_success')) {
            $output .= $this->displayConfirmation($this->l('Your store has been successfully connected to Neuralyn TRYON.'));
        }

        // Handle error message from callback redirect
        $errorType = Tools::getValue('neuralyn_error');
        if ('connection_expired' === $errorType) {
            $output .= $this->displayError($this->l('The connection time has expired. Please start the connection process again.'));
        }

        $isConnected = $this->isConnected();
        $licenseKey = Configuration::get(self::CONFIG_LICENSE_KEY);
        $manageUrl = self::NEURALYN_CONNECT_WEB_BASE_URL . '/tryon/manage/?licenseKey=' . urlencode($licenseKey);

        // Prepare hooks configuration data
        $enabledHooks = $this->getEnabledHooks();
        $hookLocations = $this->getHookLocations();
        $hooksConfig = [];
        foreach (self::$availableHooks as $hookName => $description) {
            $hooksConfig[] = [
                'name' => $hookName,
                'description' => $description,
                'enabled' => in_array($hookName, $enabledHooks),
                'has_location_option' => in_array($hookName, self::$hooksWithLocationOption),
                'location' => isset($hookLocations[$hookName]) ? $hookLocations[$hookName] : self::LOCATION_PRODUCT,
            ];
        }

        // Get button style config
        $currentButtonStyle = $this->getButtonStyle();

        // Get order states for buyer statuses config
        $orderStates = OrderState::getOrderStates($this->context->language->id);
        $buyerOrderStatuses = $this->getBuyerOrderStatuses();

        $this->context->smarty->assign([
            'connect_url' => $this->context->link->getModuleLink($this->name, 'connect', [], true),
            'ps_version' => _PS_VERSION_,
            'module_version' => $this->version,
            'neuralyn_secure_key' => $this->getSecureKey(),
            'is_connected' => $isConnected,
            'license_key' => $licenseKey,
            'manage_url' => $manageUrl,
            'hooks_config' => $hooksConfig,
            'location_product' => self::LOCATION_PRODUCT,
            'location_listing' => self::LOCATION_LISTING,
            'location_both' => self::LOCATION_BOTH,
            'button_styles' => self::$buttonStyles,
            'current_button_style' => $currentButtonStyle,
            'button_float_right' => $this->getButtonFloatRight(),
            'order_states' => $orderStates,
            'buyer_order_statuses' => $buyerOrderStatuses,
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
     * Save license key from callback.
     *
     * @param string $licenseKey
     *
     * @return bool
     */
    public function saveKeys($licenseKey)
    {
        Configuration::updateValue(self::CONFIG_LICENSE_KEY, $licenseKey);

        // Clear connection_id after successful use
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID);
        Configuration::deleteByName(self::CONFIG_CONNECTION_ID_EXPIRE);

        return true;
    }

    /**
     * Connect to Neuralyn and return redirect URL.
     *
     * @return array
     */
    public function connect()
    {
        $connectionId = Tools::passwdGen(64);
        $connectionIdExpire = time() + (15 * 60); // 15 minutes from now

        // Save connection_id and expiration for later validation
        Configuration::updateValue(self::CONFIG_CONNECTION_ID, $connectionId);
        Configuration::updateValue(self::CONFIG_CONNECTION_ID_EXPIRE, $connectionIdExpire);

        $domain = Tools::getShopDomainSsl(true);
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        $redirectUrl = self::NEURALYN_CONNECT_WEB_BASE_URL . '/connect/tryon?' . http_build_query([
            'connectionId' => $connectionId,
            'domain' => $domain,
            'service_token' => 'tryon',
            'platform' => 'prestashop',
        ]);

        return ['success' => true, 'redirect_url' => $redirectUrl];
    }

    /**
     * Create a WebService key in PrestaShop.
     *
     * @param string $key
     *
     * @return bool
     */
    public function createWebserviceKey($key)
    {
        if (!class_exists('WebserviceKey')) {
            return false;
        }

        $wsKey = new WebserviceKey();
        $wsKey->key = $key;
        $wsKey->description = 'Neuralyn TRYON Integration';
        $wsKey->active = true;

        // Use try-catch to handle WebserviceKey creation in FO context
        try {
            $addResult = $wsKey->add();
        } catch (Exception $e) {
            // If WebserviceKey::add() fails due to missing employee context,
            // it's likely because we're in FO context. This is expected.
            $addResult = true; // Assume success since the key object is valid

            // Log the exception for debugging but don't fail the process
            PrestaShopLogger::addLog(
                'Neuralyn TRYON: WebserviceKey creation attempted in FO context: ' . $e->getMessage(),
                2, // Warning level
                null,
                'NeuralynTryon'
            );
        }

        if (!$addResult) {
            return false;
        }

        $permissions = [
            'customers' => ['GET' => 1],
            'orders' => ['GET' => 1],
            'products' => ['GET' => 1],
            'images' => ['GET' => 1],
            'image_types' => ['GET' => 1],
            'languages' => ['GET' => 1],
            'search' => ['GET' => 1],
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
     *
     * @return array
     */
    public function callExternalApi($url, array $payload)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, \CURLOPT_POST, true);
            curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, \CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, \CURLOPT_TIMEOUT, 15);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, \CURLINFO_HTTP_CODE);
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

        if (false === $response || $status >= 400) {
            PrestaShopLogger::addLog('Neuralyn TRYON API call failed: ' . $error . ' status:' . $status, 3, null, 'Neuralyn TRYON');

            return ['success' => false, 'status' => $status, 'error' => $error, 'response' => $response];
        }

        return ['success' => true, 'status' => $status, 'response' => $response];
    }

    private function getCustomerUUID($customerId)
    {
        $cacheKey = 'neuralyn_tryon_uuid_' . (int) $customerId;

        // Look for UUID in the cache
        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        }

        // If it is not in cache, get from DB
        $uuid = Db::getInstance()->getValue(
            '
            SELECT neuralyn_tryon_uuid
            FROM ' . _DB_PREFIX_ . 'customer
            WHERE id_customer = ' . (int) $customerId
        );

        if (!$uuid) {
            // Generate new UUID v4 and save to DB
            $uuid = $this->generateUuidV4();

            Db::getInstance()->update(
                'customer',
                ['neuralyn_tryon_uuid' => pSQL($uuid)],
                'id_customer = ' . (int) $customerId
            );
        }

        Cache::store($cacheKey, $uuid);

        return $uuid;
    }

    /**
     * Generate a UUID v4.
     *
     * @return string
     */
    private function generateUuidV4()
    {
        $data = random_bytes(16);

        // Set version to 0100 (UUID v4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10 (UUID variant)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Get customer photos from cache or database.
     *
     * @param int $customerId
     *
     * @return array Associative array [productId => uuid]
     */
    private function getCustomerPhotos($customerId)
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
        $result = Db::getInstance()->getValue(
            'SELECT neuralyn_tryon_photos
            FROM ' . _DB_PREFIX_ . 'customer
            WHERE id_customer = ' . (int) $customerId
        );

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
     * Get the customer type based on order history.
     *
     * @param int|null $customerId
     *
     * @return string guest|registered|buyer
     */
    public function getCustomerType($customerId)
    {
        // Guest (not logged in)
        if (empty($customerId) || (int) $customerId <= 0) {
            return 'guest';
        }

        $cacheKey = 'neuralyn_tryon_customer_type_' . (int) $customerId;

        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        }

        $customerType = 'registered';

        $buyerStatuses = $this->getBuyerOrderStatuses();
        if (!empty($buyerStatuses)) {
            $statusesIn = implode(',', array_map('intval', $buyerStatuses));

            $hasBuyerOrder = Db::getInstance()->getValue('
                SELECT 1 FROM ' . _DB_PREFIX_ . 'orders
                WHERE id_customer = ' . (int) $customerId . '
                AND current_state IN (' . $statusesIn . ')
            ');

            if ($hasBuyerOrder) {
                $customerType = 'buyer';
            }
        }

        Cache::store($cacheKey, $customerType);

        return $customerType;
    }

    public function hookDisplayFooterProduct($params)
    {
        if ('product' !== $this->context->controller->php_self) {
            return '';
        }

        $licenseKey = Configuration::get(self::CONFIG_LICENSE_KEY);

        // Get product safely - first try hook params, then controller method if available
        $product = null;
        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $product = $params['product'];
        } elseif (method_exists($this->context->controller, 'getProduct')) {
            $product = $this->context->controller->getProduct();
        } elseif (Tools::getValue('id_product')) {
            $product = new Product((int) Tools::getValue('id_product'), true, $this->context->language->id);
            if (!Validate::isLoadedObject($product)) {
                $product = null;
            }
        }

        // Exit if no product found
        if (!$product) {
            return '';
        }
        $customerId = $this->context->customer && $this->context->customer->id ? $this->context->customer->id : null;
        $customerType = $this->getCustomerType($customerId);
        $loginUrl = $this->context->link->getPageLink('authentication');
        $customerUUID = $customerId ? $this->getCustomerUUID($customerId) : '';
        $customerPhotos = $customerId ? $this->getCustomerPhotos($customerId) : [];

        $this->context->smarty->assign([
            'licenseKey' => pSQL($licenseKey),
            'productId' => $product->id,
            'cdnUrl' => self::NEURALYN_CDN_URL,
            'customerId' => $customerId,
            'customerType' => $customerType,
            'customerUUID' => $customerUUID,
            'customerPhotos' => $customerPhotos,
            'loginUrl' => $loginUrl,
            'staticToken' => $customerId ? Tools::getToken(false) : '',
        ]);

        return $this->display(__FILE__, 'views/templates/hook/widget.tpl');
    }
}
