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
    <h3><i class="icon-key"></i> {l s='License Key' mod='neuralyn_tryon'}</h3>
    <p>{l s='Enter your Neuralyn TRYON license key to activate the integration.' mod='neuralyn_tryon'}</p>

    <form method="post" action="">
        <div class="form-group">
            <label for="neuralyn_license_key">{l s='License Key' mod='neuralyn_tryon'}</label>
            <input type="text" name="neuralyn_license_key" id="neuralyn_license_key" class="form-control" value="{$license_key|escape:'html':'UTF-8'}" placeholder="{l s='Enter your license key' mod='neuralyn_tryon'}" />
            <p class="help-block">{l s='You can obtain your license key from the Neuralyn TRYON platform.' mod='neuralyn_tryon'} - <a href="https://www.neuralyn.com.br/dashboard" target="_blank">https://www.neuralyn.com.br/dashboard</a></p>
        </div>

        <div class="panel-footer">
            <button type="submit" name="submitNeuralynLicenseKey" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='neuralyn_tryon'}
            </button>
        </div>
    </form>
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
</style>

<script type="text/javascript">
function updateButtonPreview() {
    var select = document.getElementById('button_style');
    var preview = document.getElementById('button-preview');
    var notice = document.getElementById('custom-style-notice');
    var floatRightOn = document.getElementById('button_float_right_on');
    var selectedStyle = select.value;

    // Removes all style classes
    var styles = ['animated', 'black', 'pink', 'dark-blue', 'light-blue', 'green', 'white', 'gray', 'red', 'orange', 'purple'];
    styles.forEach(function(style) {
        preview.classList.remove('neuralyn-tryon-' + style + '-button');
    });
    preview.classList.remove('is-visible');

    // Add class to the selected style (except custom)
    if (selectedStyle !== 'custom') {
        preview.classList.add('neuralyn-tryon-' + selectedStyle + '-button');
        // Add is-visible for the animated button only
        if (selectedStyle === 'animated') {
            preview.classList.add('is-visible');
        }
    }

    // Float right
    if (floatRightOn && floatRightOn.checked) {
        preview.classList.add('neuralyn-tryon-app-button-float-right');
    } else {
        preview.classList.remove('neuralyn-tryon-app-button-float-right');
    }

    // show/hide message regarding custom styles
    notice.style.display = selectedStyle === 'custom' ? 'block' : 'none';
}
</script>
