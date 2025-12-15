# Neuralyn TRYON - PrestaShop Integration Module

A PrestaShop module that integrates the Neuralyn TRYON virtual try-on widget into your online store, allowing customers to virtually try on products before purchasing.

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [How It Works](#how-it-works)
- [Widget Integration](#widget-integration)
- [Hook Configuration System](#hook-configuration-system)
- [Button Design System](#button-design-system)
- [API Access](#api-access)
- [Features](#features)
- [Architecture](#architecture)
- [File Structure](#file-structure)
- [Development](#development)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## Overview

The Neuralyn TRYON module connects your PrestaShop store to the Neuralyn TRYON SaaS platform, enabling virtual try-on capabilities for fashion and apparel products. This integration enhances the online shopping experience and helps reduce product return rates.

## Requirements

- PrestaShop 1.7 or higher (compatible with 1.7 and 8.0+)
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

The module hooks into multiple PrestaShop display points to inject the try-on widget and button. The system includes 19+ configurable hooks with flexible placement options:

#### Core Widget Injection
| Hook | Description | Configurable |
|------|-------------|--------------|
| `displayHeader` | Widget script injection (always enabled) | No |
| `displayBackOfficeHeader` | Admin CSS injection | No |
| `displayFooterProduct` | Product page footer widget | No |

#### Product Page Hooks
| Hook | Description | Location Options | Size Options |
|------|-------------|------------------|--------------|
| `displayReassurance` | Below breadcrumb, before main product area | Product page only | Default, Small, Tiny |
| `displayProductCover` | Near product main image | Product page only | Default, Small, Tiny |
| `displayAfterProductThumbs` | After product gallery thumbnails | Product page only | Default, Small, Tiny |
| `displayProductThumbs` | Within product thumbnails area | Product page only | Default, Small, Tiny |
| `displayProductVariants` | Near product variants/combinations | Product page only | Default, Small, Tiny |
| `displayProductButtons` | In product buttons area | Product page only | Default, Small, Tiny |
| `displayProductAdditionalInfo` | Below buttons (additional info area) | Product page only | Default, Small, Tiny |
| `displayProductCustomizationForm` | Near customization options | Product page only | Default, Small, Tiny |
| `displayProductExtraContent` | Creates new product tabs/content | Product page only | Default, Small, Tiny |
| `displayProductPackContent` | For product packs | Product page only | Default, Small, Tiny |

#### Price Block Hooks (Advanced Placement)
| Hook | Description | Location Options | Size Options |
|------|-------------|------------------|--------------|
| `displayProductPriceBlock_after_price` | Immediately after final price | Product/Listings/Both | Default, Small, Tiny |
| `displayProductPriceBlock_before_price` | Before any price appears | Product/Listings/Both | Default, Small, Tiny |
| `displayProductPriceBlock_old_price` | Where crossed-out price shows (promotions) | Product/Listings/Both | Default, Small, Tiny |
| `displayProductPriceBlock_unit_price` | Where unit price displays (per liter, etc.) | Product/Listings/Both | Default, Small, Tiny |
| `displayProductPriceBlock_weight` | Where weight-based pricing shows | Product/Listings/Both | Default, Small, Tiny |

#### Global Hooks
| Hook | Description | Location Options | Size Options |
|------|-------------|------------------|--------------|
| `displayTop` | Top of page | All pages | Default, Small, Tiny |
| `displayFooter` | Page footer | All pages | Default, Small, Tiny |
| `displayProductPriceAndShipping` | Price and shipping area | Product/Listings/Both | Default, Small, Tiny |

### Hook Configuration System

The module provides comprehensive hook management through the admin panel:

#### Enable/Disable Control
- **Individual Hook Control**: Toggle each hook on/off independently
- **Bulk Operations**: Enable/disable multiple hooks at once
- **Smart Defaults**: Common hooks enabled by default

#### Location Settings (Price Block Hooks)
- **Product Page Only**: Button appears only on individual product pages
- **Listings Only**: Button appears only in category/search listings
- **Both**: Button appears in both contexts

#### Size Configuration
- **Default**: Standard button size (optimized for each hook position)
- **Small**: Compact button for tight spaces
- **Tiny**: Minimal button for subtle integration

#### Real-time Preview
- **Visual Preview**: See button appearance in admin panel
- **Style Simulation**: Preview different configurations before applying

### Button Design System

The module includes a comprehensive button styling system with 12 predefined styles plus custom CSS support:

#### Pre-built Button Styles
| Style | Description | CSS Class | Usage |
|-------|-------------|-----------|-------|
| Animated | Interactive animated button with hover effects | `neuralyn-tryon-animated-button` | Default, engaging |
| Black | Classic black button with white text | `neuralyn-tryon-black-button` | Professional, minimal |
| Pink | Vibrant pink button | `neuralyn-tryon-pink-button` | Fashion, feminine brands |
| Dark Blue | Deep blue button | `neuralyn-tryon-dark-blue-button` | Corporate, trustworthy |
| Light Blue | Soft blue button | `neuralyn-tryon-light-blue-button` | Clean, modern |
| Green | Green button | `neuralyn-tryon-green-button` | Natural, eco-friendly |
| White | White button with dark text | `neuralyn-tryon-white-button` | Minimal, subtle |
| Gray | Neutral gray button | `neuralyn-tryon-gray-button` | Understated, professional |
| Red | Bold red button | `neuralyn-tryon-red-button` | Attention-grabbing |
| Orange | Orange button | `neuralyn-tryon-orange-button` | Energetic, fun |
| Purple | Purple button | `neuralyn-tryon-purple-button` | Luxury, creative |
| Custom | Custom CSS styling | `neuralyn-tryon-app-button` | Full customization |

#### Button Configuration Options

**Style Selection**
- Choose from 12 predefined styles
- Real-time preview in admin panel
- Switch between styles instantly

**Float Position**
- **Float Right**: Positions button on the right side of containers
- **Default Position**: Button follows normal document flow
- Applies to all enabled hooks

**Size Options** (Per Hook)
- **Default**: Optimized size for each hook position
- **Small**: 80% of default size for compact areas
- **Tiny**: 60% of default size for minimal integration

#### Custom Styling

For the **Custom** style option, use CSS to style the base button class:

```css
.neuralyn-tryon-app-button {
    background: your-custom-color;
    color: your-text-color;
    border: your-border-style;
    border-radius: your-border-radius;
    padding: your-padding;
    font-size: your-font-size;
    /* Add any custom styles */
}

.neuralyn-tryon-app-button:hover {
    /* Hover effects */
}
```

#### Size Classes (Applied Automatically)

```css
/* Default size - no additional class needed */
.neuralyn-tryon-app-button { }

/* Small size */
.neuralyn-tryon-app-button.neuralyn-tryon-small { }

/* Tiny size */
.neuralyn-tryon-app-button.neuralyn-tryon-tiny { }
```

#### Live Preview

The admin configuration includes a live preview system:
- **Real-time Updates**: See changes immediately
- **Style Testing**: Preview all styles before applying
- **Position Simulation**: See how float-right affects positioning

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

### Customer UUID API

The module exposes a REST API endpoint for managing customer UUIDs used by the Neuralyn platform.

**Endpoint:** `index.php?fc=module&module=neuralyn_tryon&controller=api`

**Authentication:** HTTP Basic Auth using PrestaShop WebService key

**Request:**
```http
POST /module/neuralyn_tryon/api
Content-Type: application/json
Authorization: Basic <base64(webservice_key:)>

{
  "customerId": 123,
  "customerUUID": "uuid-string-here"
}
```

**Success Response:**
```json
{
  "status": true,
  "uuid": "uuid-string-here"
}
```

**Error Response:**
```json
{
  "status": false,
  "code": "error_code",
  "message": "Error description"
}
```

**Error Codes:**

| Code                    | HTTP | Description                          |
|-------------------------|------|--------------------------------------|
| `unauthorized`          | 401  | Invalid or missing WebService key    |
| `method_not_allowed`    | 405  | Request method is not POST           |
| `missing_parameters`    | 400  | Missing required parameters          |
| `customer_not_found`    | 404  | Customer ID does not exist           |
| `internal_server_error` | 500  | Database error or exception          |

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
│       ├── callback.php         # API callback handler
│       └── api.php              # Customer UUID API endpoint
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

**Version**: 1.0.1
**Author**: Neuralyn
**Compatibility**: PrestaShop 1.7+
