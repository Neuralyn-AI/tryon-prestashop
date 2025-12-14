{**
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
 *}
<div class="panel">
    <h3><i class="icon-cogs"></i> {l s='Neuralyn TRYON Integration' mod='neuralyn_tryon'}</h3>
    <p>{l s='Connect your store to Neuralyn TRYON to enable seamless integration.' mod='neuralyn_tryon'}</p>

    <div id="neuralyn-message" class="alert" style="display:none;"></div>

    {if $is_connected}
        <div class="alert alert-success">
            <i class="icon-check"></i> {l s='Your store is connected to Neuralyn TRYON.' mod='neuralyn_tryon'}
        </div>

        <h4>{l s='License Information' mod='neuralyn_tryon'}</h4>
        <p>{l s='License Key:' mod='neuralyn_tryon'} <code>{$license_key|escape:'html':'UTF-8'}</code></p>

        <a href="{$manage_url|escape:'html':'UTF-8'}" class="btn btn-default" target="_blank">
            <i class="icon-external-link"></i> {l s='Manage Subscription' mod='neuralyn_tryon'}
        </a>
    {else}
        <button id="neuralyn-connect-btn" class="btn btn-primary" data-url="{$connect_url|escape:'html':'UTF-8'}" data-secure="{$neuralyn_secure_key|escape:'html':'UTF-8'}">
            {l s='Connect with Neuralyn' mod='neuralyn_tryon'}
        </button>
    {/if}

    <hr />
    <h4>{l s='Store Information' mod='neuralyn_tryon'}</h4>
    <ul>
        <li>{l s='PrestaShop version:' mod='neuralyn_tryon'} <strong>{$ps_version|escape:'html':'UTF-8'}</strong></li>
        <li>{l s='Module version:' mod='neuralyn_tryon'} <strong>{$module_version|escape:'html':'UTF-8'}</strong></li>
    </ul>
</div>

<div class="panel">
    <h3><i class="icon-anchor"></i> {l s='Hook Configuration' mod='neuralyn_tryon'}</h3>
    <p>{l s='Enable or disable hooks to control where the TRYON button appears on product pages.' mod='neuralyn_tryon'}</p>

    <form method="post" action="">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 120px;">{l s='Enabled' mod='neuralyn_tryon'}</th>
                    <th style="width: 250px;">{l s='Hook Name' mod='neuralyn_tryon'}</th>
                    <th>{l s='Description' mod='neuralyn_tryon'}</th>
                    <th style="width: 160px;">{l s='Location' mod='neuralyn_tryon'}</th>
                    <th style="width: 100px;">{l s='Size' mod='neuralyn_tryon'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$hooks_config item=hook}
                <tr>
                    <td>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="hook_{$hook.name|escape:'html':'UTF-8'}" id="hook_{$hook.name|escape:'html':'UTF-8'}_on" value="1" {if $hook.enabled}checked="checked"{/if} />
                            <label for="hook_{$hook.name|escape:'html':'UTF-8'}_on">{l s='Yes' mod='neuralyn_tryon'}</label>
                            <input type="radio" name="hook_{$hook.name|escape:'html':'UTF-8'}" id="hook_{$hook.name|escape:'html':'UTF-8'}_off" value="0" {if !$hook.enabled}checked="checked"{/if} />
                            <label for="hook_{$hook.name|escape:'html':'UTF-8'}_off">{l s='No' mod='neuralyn_tryon'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </td>
                    <td>
                        <code>{$hook.name|escape:'html':'UTF-8'}</code>
                    </td>
                    <td>
                        {$hook.description|escape:'html':'UTF-8'}
                    </td>
                    <td>
                        {if $hook.has_location_option}
                            <select name="hook_location_{$hook.name|escape:'html':'UTF-8'}" class="form-control">
                                <option value="{$location_product|escape:'html':'UTF-8'}" {if $hook.location == $location_product}selected="selected"{/if}>{l s='Product page only' mod='neuralyn_tryon'}</option>
                                <option value="{$location_listing|escape:'html':'UTF-8'}" {if $hook.location == $location_listing}selected="selected"{/if}>{l s='Listings only' mod='neuralyn_tryon'}</option>
                                <option value="{$location_both|escape:'html':'UTF-8'}" {if $hook.location == $location_both}selected="selected"{/if}>{l s='Both' mod='neuralyn_tryon'}</option>
                            </select>
                        {else}
                            <span class="text-muted">-</span>
                        {/if}
                    </td>
                    <td>
                        <select name="hook_size_{$hook.name|escape:'html':'UTF-8'}" class="form-control">
                            <option value="{$size_default|escape:'html':'UTF-8'}" {if $hook.size == $size_default}selected="selected"{/if}>{l s='Default' mod='neuralyn_tryon'}</option>
                            <option value="{$size_small|escape:'html':'UTF-8'}" {if $hook.size == $size_small}selected="selected"{/if}>{l s='Small' mod='neuralyn_tryon'}</option>
                            <option value="{$size_tiny|escape:'html':'UTF-8'}" {if $hook.size == $size_tiny}selected="selected"{/if}>{l s='Tiny' mod='neuralyn_tryon'}</option>
                        </select>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>

        <div class="panel-footer">
            <button type="submit" name="submitNeuralynHooks" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save Hook Settings' mod='neuralyn_tryon'}
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <h3><i class="icon-paint-brush"></i> {l s='Button Design' mod='neuralyn_tryon'}</h3>
    <p>{l s='Customize the appearance of the TRYON button.' mod='neuralyn_tryon'}</p>

    <form method="post" action="">
        <div class="row">
            <div class="col-lg-6">
                <h4>{l s='Button Preview' mod='neuralyn_tryon'}</h4>
                <div id="neuralyn-btn-preview" style="padding: 20px; background: #f5f5f5; border-radius: 4px; margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Default:</label>
                        <button type="button" class="neuralyn-tryon-app-button neuralyn-preview-btn" style="background-color: {$btn_bg_color|escape:'html':'UTF-8'}; color: {$btn_text_color|escape:'html':'UTF-8'};">
                            {l s='Prove em você' mod='neuralyn_tryon'}
                        </button>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Small:</label>
                        <button type="button" class="neuralyn-tryon-app-button size-small neuralyn-preview-btn" style="background-color: {$btn_bg_color|escape:'html':'UTF-8'}; color: {$btn_text_color|escape:'html':'UTF-8'};">
                            {l s='Prove em você' mod='neuralyn_tryon'}
                        </button>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tiny:</label>
                        <button type="button" class="neuralyn-tryon-app-button size-tiny neuralyn-preview-btn" style="background-color: {$btn_bg_color|escape:'html':'UTF-8'}; color: {$btn_text_color|escape:'html':'UTF-8'};">
                            {l s='Prove em você' mod='neuralyn_tryon'}
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label>{l s='Enable Color Customization' mod='neuralyn_tryon'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="btn_colors_enabled" id="btn_colors_enabled_on" value="1" {if $btn_colors_enabled}checked="checked"{/if} />
                        <label for="btn_colors_enabled_on">{l s='Yes' mod='neuralyn_tryon'}</label>
                        <input type="radio" name="btn_colors_enabled" id="btn_colors_enabled_off" value="0" {if !$btn_colors_enabled}checked="checked"{/if} />
                        <label for="btn_colors_enabled_off">{l s='No' mod='neuralyn_tryon'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>

                <div id="color-pickers-container" {if !$btn_colors_enabled}style="display: none;"{/if}>
                    <div class="form-group">
                        <label for="btn_bg_color">{l s='Background Color' mod='neuralyn_tryon'}</label>
                        <input type="color" name="btn_bg_color" id="btn_bg_color" value="{$btn_bg_color|escape:'html':'UTF-8'}" class="form-control" style="width: 80px; height: 40px; padding: 2px;" />
                    </div>

                    <div class="form-group">
                        <label for="btn_text_color">{l s='Text Color' mod='neuralyn_tryon'}</label>
                        <input type="color" name="btn_text_color" id="btn_text_color" value="{$btn_text_color|escape:'html':'UTF-8'}" class="form-control" style="width: 80px; height: 40px; padding: 2px;" />
                    </div>
                </div>

                <div id="manual-css-notice" class="alert alert-warning" {if $btn_colors_enabled}style="display: none;"{/if}>
                    <i class="icon-warning-sign"></i>
                    <strong>{l s='Manual CSS required' mod='neuralyn_tryon'}</strong><br/>
                    {l s='Color customization is disabled. To style the button, add CSS manually to your theme using the class:' mod='neuralyn_tryon'}
                    <code>.neuralyn-tryon-app-button</code>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="submitNeuralynButtonDesign" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save Button Design' mod='neuralyn_tryon'}
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <h3><i class="icon-shopping-cart"></i> {l s='Buyer Order Statuses' mod='neuralyn_tryon'}</h3>
    <p>{l s='Select order statuses that qualify a customer as a "buyer". Customers with at least one order in any of these statuses will be considered buyers.' mod='neuralyn_tryon'}</p>

    <form method="post" action="">
        <div class="form-group">
            <div class="order-statuses-grid">
                {foreach from=$order_states item=state}
                <label class="status-checkbox-item">
                    <input type="checkbox"
                           name="buyer_order_statuses[]"
                           value="{$state.id_order_state|intval}"
                           {if in_array($state.id_order_state, $buyer_order_statuses)}checked="checked"{/if}>
                    <span class="status-badge" style="background-color: {$state.color|escape:'html':'UTF-8'};">
                        {$state.name|escape:'html':'UTF-8'}
                    </span>
                </label>
                {/foreach}
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="submitNeuralynBuyerStatuses" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='neuralyn_tryon'}
            </button>
        </div>
    </form>
</div>

<style>
.order-statuses-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.status-checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    margin: 0;
}
.status-checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
.status-badge {
    padding: 6px 14px;
    border-radius: 4px;
    color: #fff;
    text-shadow: 0 1px 1px rgba(0,0,0,0.3);
    font-size: 14px;
    font-weight: 500;
}
.neuralyn-tryon-app-button {
    padding: 12px 24px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-family: inherit;
    text-decoration: none;
    text-align: center;
    line-height: 1.4;
    border-radius: 4px;
    transition: opacity 0.2s ease;
}
.neuralyn-tryon-app-button.size-small {
    padding: 8px 16px;
    font-size: 12px;
}
.neuralyn-tryon-app-button.size-tiny {
    padding: 4px 10px;
    font-size: 10px;
}
</style>

<script type="text/javascript">
(function() {
    var bgColorInput = document.getElementById('btn_bg_color');
    var textColorInput = document.getElementById('btn_text_color');
    var previewButtons = document.querySelectorAll('.neuralyn-preview-btn');
    var colorsEnabledOn = document.getElementById('btn_colors_enabled_on');
    var colorsEnabledOff = document.getElementById('btn_colors_enabled_off');
    var colorPickersContainer = document.getElementById('color-pickers-container');
    var manualCssNotice = document.getElementById('manual-css-notice');

    function updatePreviewColors() {
        var bgColor = bgColorInput.value;
        var textColor = textColorInput.value;
        previewButtons.forEach(function(btn) {
            btn.style.backgroundColor = bgColor;
            btn.style.color = textColor;
        });
    }

    function toggleColorOptions() {
        var isEnabled = colorsEnabledOn.checked;
        colorPickersContainer.style.display = isEnabled ? 'block' : 'none';
        manualCssNotice.style.display = isEnabled ? 'none' : 'block';

        if (!isEnabled) {
            previewButtons.forEach(function(btn) {
                btn.style.backgroundColor = '';
                btn.style.color = '';
            });
        } else {
            updatePreviewColors();
        }
    }

    if (bgColorInput) {
        bgColorInput.addEventListener('input', updatePreviewColors);
    }
    if (textColorInput) {
        textColorInput.addEventListener('input', updatePreviewColors);
    }
    if (colorsEnabledOn) {
        colorsEnabledOn.addEventListener('change', toggleColorOptions);
    }
    if (colorsEnabledOff) {
        colorsEnabledOff.addEventListener('change', toggleColorOptions);
    }
})();
</script>

{if !$is_connected}
<script type="text/javascript">
(function() {
    var btn = document.getElementById('neuralyn-connect-btn');
    var messageBox = document.getElementById('neuralyn-message');
    if (!btn) {
        return;
    }

    var showMessage = function(text, success) {
        messageBox.style.display = 'block';
        messageBox.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        messageBox.innerHTML = text;
    };

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        var url = btn.getAttribute('data-url');
        var secureKey = btn.getAttribute('data-secure');
        showMessage('{l s='Connecting to Neuralyn...' mod='neuralyn_tryon' js=1}', true);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json.success && json.redirect_url) {
                        window.location.href = json.redirect_url;
                    } else {
                        showMessage(json.message || '{l s='Connection failed.' mod='neuralyn_tryon' js=1}', false);
                    }
                } catch (err) {
                    showMessage('{l s='Unexpected response from server.' mod='neuralyn_tryon' js=1}', false);
                }
            }
        };
        xhr.send('ajax=1&secure_key=' + encodeURIComponent(secureKey));
    });
})();
</script>
{/if}
