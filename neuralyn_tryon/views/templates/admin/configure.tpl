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
                    <th style="width: 50px;">{l s='Enabled' mod='neuralyn_tryon'}</th>
                    <th style="width: 300px;">{l s='Hook Name' mod='neuralyn_tryon'}</th>
                    <th>{l s='Description' mod='neuralyn_tryon'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$hooks_config item=hook}
                <tr>
                    <td>
                        <input type="checkbox" name="hook_{$hook.name|escape:'html':'UTF-8'}" value="1" {if $hook.enabled}checked="checked"{/if} />
                    </td>
                    <td>
                        <code>{$hook.name|escape:'html':'UTF-8'}</code>
                    </td>
                    <td>
                        {$hook.description|escape:'html':'UTF-8'}
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
