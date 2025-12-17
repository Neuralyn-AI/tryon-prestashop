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
    <h3><i class="icon-paint-brush"></i> {l s='Button Design' mod='neuralyn_tryon'}</h3>
    <p>{l s='Choose the button style for the TRYON button.' mod='neuralyn_tryon'}</p>

    <form method="post" action="">
        <div class="button-design-container">
            <div class="button-design-select">
                <label for="button_style">{l s='Button Style' mod='neuralyn_tryon'}</label>
                <select name="button_style" id="button_style" class="form-control" onchange="updateButtonPreview()">
                    {foreach from=$button_styles key=style_key item=style_label}
                    <option value="{$style_key|escape:'html':'UTF-8'}" {if $current_button_style == $style_key}selected="selected"{/if}>
                        {l s=$style_label mod='neuralyn_tryon'}
                    </option>
                    {/foreach}
                </select>

                <div class="form-group" style="margin-top: 15px;">
                    <label>{l s='Float Right' mod='neuralyn_tryon'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="button_float_right" id="button_float_right_on" value="1"
                               {if $button_float_right}checked="checked"{/if} onchange="updateButtonPreview()" />
                        <label for="button_float_right_on">{l s='Yes' mod='neuralyn_tryon'}</label>
                        <input type="radio" name="button_float_right" id="button_float_right_off" value="0"
                               {if !$button_float_right}checked="checked"{/if} onchange="updateButtonPreview()" />
                        <label for="button_float_right_off">{l s='No' mod='neuralyn_tryon'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>

                <div id="custom-style-notice" class="alert alert-info" style="margin-top: 15px; {if $current_button_style != 'custom'}display:none;{/if}">
                    <i class="icon-info-circle"></i>
                    {l s='Add CSS to your theme using:' mod='neuralyn_tryon'}
                    <code>.neuralyn-tryon-app-button</code>
                </div>
            </div>

            <div class="button-design-preview">
                <label>{l s='Preview' mod='neuralyn_tryon'}</label>
                <div class="preview-container">
                    <button type="button" id="button-preview" class="neuralyn-tryon-app-button{if $current_button_style != 'custom'} neuralyn-tryon-{$current_button_style|escape:'html':'UTF-8'}-button{/if}{if $current_button_style == 'animated'} is-visible{/if}{if $button_float_right} neuralyn-tryon-app-button-float-right{/if}">
                        {l s='Prove em você' mod='neuralyn_tryon'}
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 95" aria-hidden="true">
                            <g transform="translate(0,95) scale(0.1,-0.1)" fill="currentColor" stroke="none">
                                <path d="M408 863 c-46 -192 -94 -255 -225 -296 -43 -14 -98 -28 -121 -32 -55 -9 -46 -17 46 -38 189 -44 256 -115 301 -319 l18 -83 12 60 c31 152 72 236 138 279 21 14 81 37 133 51 52 15 102 29 110 32 8 2 -27 15 -79 28 -171 44 -224 93 -272 250 -46 149 -43 145 -61 68z"/>
                                <path d="M756 415 c-10 -54 -42 -112 -71 -131 -14 -9 -51 -24 -82 -34 l-58 -18 45 -12 c105 -28 137 -59 164 -161 l14 -54 7 40 c20 99 54 138 153 171 37 13 60 23 51 23 -36 2 -121 33 -145 54 -16 14 -35 49 -49 92 l-22 69 -7 -39z"/>
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="submitNeuralynButtonDesign" class="btn btn-default pull-right">
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
.button-design-container {
    display: flex;
    gap: 40px;
    align-items: flex-start;
    margin-bottom: 15px;
}
.button-design-select {
    flex: 0 0 300px;
}
.button-design-select label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}
.button-design-select select {
    width: 100%;
}
.button-design-preview {
    flex: 1;
}
.button-design-preview > label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
}
.preview-container {
    padding: 30px;
    background: #f5f5f5;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
}
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
