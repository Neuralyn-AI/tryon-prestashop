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
{nocache}
<script>
window.TRYON_CONFIG = {
    licenseKey: "{$licenseKey|escape:'javascript':'UTF-8'}",
    customerId: "{$customerId|escape:'javascript':'UTF-8'}",
    customerUUID: "{$customerUUID|escape:'javascript':'UTF-8'}",
    customerType: "{$customerType|escape:'javascript':'UTF-8'}",
    loginUrl: "{$loginUrl|escape:'javascript':'UTF-8'}",
    platform: "prestashop"
};
</script>
{/nocache}
<script>
(function loadTryonSDK() {
    var s = document.createElement("script");
    s.src = "{$cdnUrl}/sdk.min.js";
    s.async = true;
    document.head.appendChild(s);
})();
</script>
