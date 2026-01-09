const currentScript = document.currentScript;
const scriptUrl = new URL(currentScript.src);
const neuralynLicenseKey = scriptUrl.searchParams.get("licenseKey");

window.TRYON_CONFIG = {
    licenseKey: neuralynLicenseKey,
    customerId: window.LS && window.LS.customer ? window.LS.customer : null,
    customerType: window.LS && window.LS.customer ? 'registered' : 'guest',
    loginUrl: '/account/login/',
    platform: 'nuvemshop'
};

(function loadTryonSDK() {
    var s = document.createElement("script");
    s.src = "https://tryon-cdn.neuralyn.ai/tryon.js";
    s.async = true;
    document.head.appendChild(s);
})();