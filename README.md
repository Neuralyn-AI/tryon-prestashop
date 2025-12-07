# Neuralyn TRYON - PrestaShop Integration Module

A PrestaShop module that integrates the Neuralyn TRYON virtual try-on widget into your online store, allowing customers to virtually try on products before purchasing.

## Overview

The Neuralyn TRYON module connects your PrestaShop store to the Neuralyn TRYON SaaS platform, enabling virtual try-on capabilities for fashion and apparel products. This integration enhances the online shopping experience and helps reduce product return rates.

## Requirements

- PrestaShop 1.6.0.0 or higher (compatible with 1.6, 1.7, and 8.0+)
- PHP with cURL support (or `file_get_contents` enabled)
- HTTPS/SSL enabled on your store
- Active internet connection

## Installation

1. Upload the `neuralyn_tryon` folder to your PrestaShop `/modules/` directory
2. Navigate to **Modules > Module Manager** in your PrestaShop admin panel
3. Find "Neuralyn TRYON Integration" and click **Install**
4. The module will automatically:
   - Generate API credentials
   - Create WebService access keys
   - Register necessary hooks

## Configuration

1. After installation, click **Configure** on the module
2. Click the **Connect with Neuralyn** button to establish the connection with the Neuralyn platform
3. You will be redirected to the Neuralyn platform to complete the setup
4. Once connected, the virtual try-on widget will be available on your storefront

### Environment Variables

The module supports the following environment variable:

| Variable | Description | Default |
|----------|-------------|---------|
| `NEURALYN_TRYON_API_BASE_URL` | Base URL for the Neuralyn API | `https://api.neuralyn.com` |

## How It Works

### Technical Architecture

The module operates as a bridge between your PrestaShop store and the Neuralyn cloud platform:

1. **Connection Flow**: When you click "Connect with Neuralyn", the module generates a WebService key and sends it securely to the Neuralyn API. You are then redirected to complete the setup on the Neuralyn platform.
2. **Widget Injection**: A lightweight JavaScript widget is loaded from Neuralyn's CDN and injected into your storefront pages
3. **Data Synchronization**: The Neuralyn platform uses read-only API access to retrieve product information for the try-on feature

### Widget Integration

The module hooks into multiple PrestaShop display points to inject the try-on widget and button:

| Hook | Description | Location Options |
|------|-------------|------------------|
| `displayHeader` | Widget script injection (always enabled) | - |
| `displayProductPriceBlock_after_price` | After product price | Product page / Listings / Both |
| `displayProductPriceBlock_before_price` | Before product price | Product page / Listings / Both |
| `displayProductPriceBlock_weight` | Near product weight | Product page / Listings / Both |
| `displayAfterProductThumbs` | After product thumbnails | Product page only |
| `displayReassurance` | In reassurance block | Product page only |
| `displayProductAdditionalInfo` | Additional product info area | Product page only |
| `displayProductExtraContent` | Extra content tabs | Product page only |

The widget is loaded asynchronously from `https://cdn.neuralyn.com/widget.js` to ensure it doesn't affect page load performance.

### Hook Configuration

In the module configuration, you can:

- **Enable/Disable hooks**: Control which hooks display the TRYON button
- **Set location**: For price block hooks, choose where to display (product page only, listings only, or both)
- **Set button size**: Choose button size per hook (Default, Small, or Tiny)

### Button Design

Customize the TRYON button appearance:

- **Enable Color Customization**: Toggle to enable custom colors
  - When enabled: Set background and text colors via color pickers
  - When disabled: Style the button manually via CSS using the `.neuralyn-tryon-app-button` class
- **Button Sizes**: Three sizes available (default, small, tiny) with CSS classes:
  - `.neuralyn-tryon-app-button` (default)
  - `.neuralyn-tryon-app-button.size-small`
  - `.neuralyn-tryon-app-button.size-tiny`

### API Access

The module creates read-only API access with the following permissions:

| Resource | Access Level | Purpose |
|----------|--------------|---------|
| Products | Read (GET) | Retrieve product information for try-on |
| Customers | Read (GET) | Customer context for personalization |
| Orders | Read (GET) | Order data for analytics |
| Images | Read (GET) | Access product images |
| Image Types | Read (GET) | Retrieve image format configurations |
| Languages | Read (GET) | Multi-language support |
| Search | Read (GET) | Product search capabilities |

The module uses WebService key authentication for all PrestaShop versions (1.6, 1.7, and 8.0+).

## Features

- **Multi-Version Support**: Seamless compatibility with PrestaShop 1.6, 1.7, and 8.0+
- **Automatic Credential Management**: No manual API key configuration required
- **Secure Connection**: All communications use HTTPS encryption
- **Multi-Shop Ready**: Supports PrestaShop's multi-shop feature
- **Clean Uninstallation**: Removes all traces when uninstalled
- **Non-Blocking Widget**: Asynchronous loading ensures no impact on page performance
- **Configurable Hooks**: Enable/disable button display per hook with location options
- **Customizable Button**: Three button sizes with optional color customization

## Connection Flow

```
Installation
    │
    ▼
Admin clicks "Connect with Neuralyn"
    │
    ▼
Module generates connection_id and webservice_key
    │
    ▼
Module sends credentials to Neuralyn API
    │
    ▼
API returns redirect_url
    │
    ▼
User is redirected to Neuralyn platform
    │
    ▼
Complete setup on Neuralyn platform
    │
    ▼
Widget becomes active on storefront
```

## Endpoints

The module communicates with the following Neuralyn API endpoints:

- **Connection**: `{API_BASE_URL}/encrypt-webservice-key`
- **Widget CDN**: `https://cdn.neuralyn.com/widget.js`

## Uninstallation

When uninstalling the module:

1. Navigate to **Modules > Module Manager**
2. Find "Neuralyn TRYON Integration" and click **Uninstall**
3. The module will automatically:
   - Delete all stored WebService keys
   - Remove configuration values
   - Unregister all hooks

## Troubleshooting

### Widget Not Appearing

1. Verify the module is installed and enabled
2. Check that you clicked "Connect with Neuralyn" in the configuration
3. Ensure your store has HTTPS enabled
4. Clear your PrestaShop cache

### Connection Failed

1. Verify your server can reach the Neuralyn API (check `NEURALYN_TRYON_API_BASE_URL` environment variable)
2. Check that cURL is enabled in PHP
3. Review PrestaShop logs in **Advanced Parameters > Logs**
4. Ensure WebService is enabled in **Advanced Parameters > Webservice**

## Security

The module implements several security measures:

- **Secure Key Validation**: All AJAX requests are validated with secure keys
- **Admin Verification**: Connection requires authenticated admin/employee session
- **HTTPS Enforcement**: All external communications use SSL
- **Read-Only Access**: API credentials only grant read permissions
- **Credential Encryption**: Uses PrestaShop's secure password generation

## File Structure

```
neuralyn_tryon/
├── neuralyn_tryon.php           # Main module class
├── config.xml                    # Module metadata
├── license.txt                   # License agreement
├── logo.png                      # Module icon (32x32)
├── controllers/
│   └── front/
│       ├── connect.php          # Frontend connection endpoint
│       └── callback.php         # API callback handler
├── views/
│   ├── css/
│   │   ├── admin.css            # Admin panel styling
│   │   └── front.css            # Frontend button styling
│   └── templates/
│       ├── admin/
│       │   └── configure.tpl    # Configuration page template
│       ├── front/
│       │   ├── connect.tpl      # Connection page template
│       │   └── callback.tpl     # Callback page template
│       └── hook/
│           ├── header.tpl       # Widget injection template
│           └── button.tpl       # TRYON button template
└── upgrade/
    └── upgrade-1.0.1.php        # Version upgrade script
```

## Support

For support and inquiries:

- **Email**: support@neuralyn.com.br
- **Website**: https://www.neuralyn.com.br

## License

This module is proprietary software. Each license is valid for a single PrestaShop store installation. See `license.txt` for full license terms.

---

**Version**: 1.0.0
**Author**: Neuralyn
**Compatibility**: PrestaShop 1.6.0.0+
