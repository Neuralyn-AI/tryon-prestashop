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
 * Front controller for initiating Neuralyn TRYON connection.
 */
class NeuralynTryonConnectModuleFrontController extends ModuleFrontController
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

        if (Tools::getValue('ajax')) {
            $this->ajax = true;
            $this->processAjax();
        } else {
            $this->processPage();
        }
    }

    /**
     * @return void
     */
    protected function processPage()
    {
        $this->setTemplate('module:neuralyn_tryon/views/templates/front/connect.tpl');
    }

    /**
     * @return void
     */
    protected function processAjax()
    {
        header('Content-Type: application/json');

        $secureKey = Tools::getValue('secure_key');

        if ($secureKey !== $this->module->getSecureKey()) {
            $this->ajaxResponse(['success' => false, 'message' => $this->module->l('Invalid secure key.', 'connect')]);
        }

        $result = $this->module->connect();

        if ($result['success']) {
            $this->ajaxResponse([
                'success' => true,
                'redirect_url' => $result['redirect_url'],
            ]);
        }

        $this->ajaxResponse([
            'success' => false,
            'message' => isset($result['error']) ? $result['error'] : $this->module->l('Connection failed.', 'connect'),
        ]);
    }

    /**
     * Output JSON response and terminate.
     *
     * @return void
     */
    protected function ajaxResponse(array $data)
    {
        echo json_encode($data);
        exit;
    }
}

// PrestaShop may attempt to load this controller using the underscored module name.
if (!class_exists('neuralyn_tryonconnectModuleFrontController', false)) {
    class neuralyn_tryonconnectModuleFrontController extends NeuralynTryonConnectModuleFrontController
    {
    }
}
