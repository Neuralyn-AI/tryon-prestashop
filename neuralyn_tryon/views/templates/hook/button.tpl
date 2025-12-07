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
<div class="neuralyn-tryon-hook-container">
    <button type="button" class="neuralyn-tryon-app-button {if $neuralyn_button_size != 'default'}size-{$neuralyn_button_size|escape:'html':'UTF-8'}{/if}"{if $neuralyn_btn_colors_enabled} style="background-color: {$neuralyn_btn_bg_color|escape:'html':'UTF-8'}; color: {$neuralyn_btn_text_color|escape:'html':'UTF-8'};"{/if}>
        {l s='Prove em você' mod='neuralyn_tryon'}
    </button>
</div>
