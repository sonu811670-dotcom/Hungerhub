# HungerHub 🍔🍕
### Industrial Full-Stack Food Ordering & Real-Time Restaurant Management Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%208.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap 5](https://img.shields.io/badge/UI-Bootstrap%205.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Razorpay](https://img.shields.io/badge/Payments-Razorpay%20HMAC%20Verified-0C2340?logo=razorpay&logoColor=blue)](https://razorpay.com/)
[![Leaflet.js](https://img.shields.io/badge/Maps-Leaflet.js%20GPS-199900?logo=leaflet&logoColor=white)](https://leafletjs.com/)
[![License](https://img.shields.io/badge/License-Commercial%20Ready-green)](#)

> **Lead Architect & Developer:** Sonu Kumar  
> **Contact:** [sonu811670@gmail.com](mailto:sonu811670@gmail.com) | +91 8603972526  
> **Repository:** [https://github.com/sonu811670-dotcom/HungerHub](https://github.com/sonu811670-dotcom/HungerHub)  
> **Location:** Ranchi, Jharkhand, India  

---

## 📌 Executive Summary

**HungerHub** is an industrial-grade, commercial full-stack food delivery and restaurant operations ecosystem. Designed specifically to power real restaurant and cafe businesses, HungerHub eliminates mock presentation placeholders in favor of genuine financial reconciliation, live GPS dispatch tracking, automated billing, and an AI-driven culinary concierge.

Whether deployed for a physical cloud kitchen or evaluated in an engineering capstone viva or tech interview, HungerHub showcases clean architectural separation, strict security defenses (SQLi, XSS, CSRF, HMAC), and a modern responsive user experience.

---

## 🏗️ Technical Architecture & System Design

```
                     ┌─────────────────────────────────────────────────────────┐
                     │                     CLIENT INTERFACES                   │
                     │  - Responsive Storefront (Bootstrap 5, Glassmorphism)   │
                     │  - Instant Autocomplete Live Search (api_search_menu)   │
                     │  - Real-Time Leaflet.js GPS Tracker (track_order.php)   │
                     │  - HungerBot AI Culinary Concierge (chatbot.php)        │
                     └────────────────────────────┬────────────────────────────┘
                                                  │ HTTP / AJAX JSON
                                                  ▼
                     ┌─────────────────────────────────────────────────────────┐
                     │                 BACKEND SERVICE LAYER (PHP 8)           │
                     │  - Central Configuration & Security Guard (config.php)  │
                     │  - Session Management & Cart Ledger ($_SESSION)         │
                     │  - Cryptographic Payment Verification (HMAC SHA-256)    │
                     │  - Kitchen Display System & Audio Chime (admin/orders)  │
                     │  - GST-Compliant Tax Invoice Engine (invoice.php)       │
                     └────────────────────────────┬────────────────────────────┘
                                                  │ MySQLi Prepared Queries
                                                  ▼
                     ┌─────────────────────────────────────────────────────────┐
                     │             RELATIONAL DATABASE (MySQL 8.0)             │
                     │  14 Unified Tables & Views: users, menu_items, orders,  │
                     │  order_items, payments, coupons, admins, payment_views  │
                     └─────────────────────────────────────────────────────────┘
```

---

## 🚀 Key Industrial Modules & Innovations

### 1. 💳 Dual Secure Payment Infrastructure (Zero Fake Approvals)
- **Official Razorpay Standard Modal Gateway**:
  - Implements the official Razorpay Checkout SDK.
  - Server-side cryptographic verification via HMAC SHA-256 (`hash_hmac('sha256', $order_id . '|' . $payment_id, RAZORPAY_KEY_SECRET)`).
  - Cryptographically prevents forged payment callbacks.
- **Direct Cafe UPI & Bank Reconciliation Flow**:
  - Displays dynamic merchant UPI QR code (`8603972526@ptyes`) with live payable amount.
  - Customer submits authentic 12-digit bank UTR / Reference number upon completing UPI transfer.
  - Marks transaction as `Processing` until verified by restaurant management against bank SMS or soundbox.
- **Cash on Delivery (COD)** with automatic order lifecycle initialization.

### 2. 🗺️ Real-Time Leaflet.js GPS Tracking & Kitchen Lifecycle
- **Interactive OpenStreetMap Map (`track_order.php`)**:
  - Live animated scooter marker interpolating along road waypoints between restaurant dispatch hub (Ranchi) and customer delivery address.
  - Dynamic route polyline with pulsing HTML markers.
- **Asynchronous Telemetry Polling**:
  - Polls `api_order_status.php` every 3 seconds to reflect live kitchen state changes.
  - 5-step fulfillment pipeline: `Order Placed` $\rightarrow$ `Accepted` $\rightarrow$ `Cooking` $\rightarrow$ `On The Way` $\rightarrow$ `Delivered`.
- **Dynamic Courier Details**:
  - Assigns rider name, phone, and vehicle registration number dynamically upon kitchen dispatch.

### 3. 🤖 HungerBot — Smart AI Culinary Concierge
- Floating assistant embedded across all storefront pages.
- Context-aware intent detection:
  - Food recommendations by diet or budget (e.g., *"veg under 200"*, *"spicy biryani"*, *"best pizza"*).
  - Store policy answers (delivery hours, location, payment methods, contact hotline).
  - One-click active promo discovery (e.g. `SAVE10`).
- Interactive dish cards with direct 1-click `+ Add to Cart` integration without leaving the conversation.

### 4. 🔍 Instant Autocomplete Live Search (`api_search_menu.php`)
- Asynchronous search bar in header navbar.
- Debounced live queries return dish thumbnails, prices, and veg/non-veg tags in real-time.
- Supports instant cart addition straight from the search dropdown.

### 5. 🧑‍🍳 Administrative Kitchen Display System (KDS) (`admin/orders.php`)
- **Web Audio API Kitchen Chime**: Synthesizes a melodic triad chime whenever a new order is received.
- **Automated Order Polling (`admin/api_order_poll.php`)**: Polls every 7 seconds for new orders and triggers toast alerts.
- **Operational Filters**: Instant client-side tab switching (`All`, `Pending`, `Confirmed/Cooking`, `Out for Delivery`, `Needs UTR Verification`, `Delivered`).
- **One-Click Bank UTR Verification**: Easily verify bank UTRs and capture payments with a single click.

### 6. 🧾 Commercial GST Tax Billing Engine (`invoice.php`)
- Standardized tax invoice with restaurant GSTIN (`20AAAAA0000A1Z5`).
- Itemized billing breakdown, discount reconciliation, and automatic A4 print styling (`@media print`).

---

## 🔒 Security & Quality Standards

- **SQL Injection Defense**: 100% of database interactions utilize prepared statements with strict parameter binding (`mysqli::prepare` + `bind_param`).
- **XSS Sanitization**: Dynamic HTML entities sanitized via `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
- **Password Security**: Strong cryptographic password hashing via `PASSWORD_BCRYPT`.
- **BOM Protection**: All files stripped of UTF-8 Byte Order Marks (BOM) to guarantee clean HTTP headers and JSON serialization.
- **Server Guard (`.htaccess`)**: Blocks public access to SQL files, configuration environments, and backup artifacts.

---

## 🗄️ Database Structure (`database.sql`)

Unified, definer-free SQL script compatible with MySQL 5.7+ and 8.0+:
- `users`: Registered customers, hashed credentials, contact, and addresses.
- `admins`: Administrative accounts.
- `menu_items`: Food catalog with categories, prices, descriptions, and images.
- `orders`: Master orders table with rider dispatch details (`rider_name`, `rider_phone`, `rider_vehicle`).
- `order_items`: Relational line-items linked to parent orders.
- `coupons`: Promotional codes and percentage discounts (`SAVE10`).
- `payments`: Financial ledger recording gateway IDs, bank UTRs, and payment methods.
- `payment_analytics`: Real-time SQL view aggregating restaurant revenue and transaction metrics.

---

## ⚙️ Quickstart & Local Setup

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8.0+ and MySQL)
- Git & modern browser

### 2. Installation
```bash
# Clone into XAMPP web root
cd C:\xampp\htdocs\hungerhub
git clone https://github.com/sonu811670-dotcom/HungerHub.git hungerhub
```

### 3. Database Import
1. Open **XAMPP Control Panel** and start **Apache** & **MySQL**.
2. Open `http://localhost/phpmyadmin`.
3. Create a database named `hungerhub`.
4. Click **Import** $\rightarrow$ select `database.sql` $\rightarrow$ click **Go**.

### 4. Credentials & Access URLs
- **Storefront**: `http://localhost/hungerhub/hungerhub/index.php`
- **Menu**: `http://localhost/hungerhub/hungerhub/menu.php`
- **Active Coupon Code**: `SAVE10` (10% flat discount)
- **Admin Control Center**: `http://localhost/hungerhub/hungerhub/admin/admin_login.php`
  - *Email:* `sonu811670@gmail.com`
  - *Password:* `Admin@123`

---

## 👨‍💻 Author & Engineering Credits

**Sonu Kumar**  
Lead Full-Stack Software Engineer  
- 📧 Email: [sonu811670@gmail.com](mailto:sonu811670@gmail.com)  
- 📱 Phone / UPI: +91 8603972526  
- 💼 Focus: High-throughput Web Applications, Secure FinTech Workflows, and Distributed Systems  
- 🎓 Final Year Computer Science Engineering Capstone