<?php
/**
 * HungerHub - Commercial Restaurant Management & Ordering Configuration
 * Developed by: Sonu Kumar (Lead Full-Stack Engineer)
 * Single source of truth for restaurant metadata, database connectivity, and secure payment gateways.
 */

// Prevent multiple inclusions
if (defined('HUNGERHUB_CONFIG_LOADED')) {
    return;
}
define('HUNGERHUB_CONFIG_LOADED', true);

// -----------------------------------------------------------------------------
// 1. BUSINESS & RESTAURANT PROFILE
// -----------------------------------------------------------------------------
define('REST_NAME', 'HungerHub Cafe & Restaurant');
define('REST_LEGAL_NAME', 'HungerHub Food Services Pvt. Ltd.');
define('REST_TAGLINE', 'Artisan Flavors & Fast Local Delivery');
define('REST_ADDRESS', 'Sai Vihar Colony, Road No. 1, Madhukam, Ranchi, Jharkhand - 834001');
define('REST_PHONE', '+91 8603972526');
define('REST_EMAIL', 'support@hungerhub.com');
define('REST_GSTIN', '20AAACH1234F1Z8');
define('REST_FSSAI', '10020031004567');
define('REST_CURRENCY', 'INR');
define('REST_CURRENCY_SYMBOL', '₹');

// -----------------------------------------------------------------------------
// 2. PAYMENT GATEWAY SETTINGS (RAZORPAY & DIRECT UPI)
// -----------------------------------------------------------------------------
// Razorpay Environment: 'test' for development/interviews, 'live' for real business bank payouts
define('RAZORPAY_ENV', 'test'); 

// Replace these keys with your Razorpay Dashboard API Keys (Settings -> API Keys)
define('RAZORPAY_KEY_ID', 'rzp_test_51a2b3c4d5e6f7');
define('RAZORPAY_KEY_SECRET', 'YOUR_RAZORPAY_KEY_SECRET');

// Direct Restaurant UPI Merchant Details
define('MERCHANT_UPI_VPA', '8603972526@ptyes');
define('MERCHANT_UPI_NAME', 'HungerHub Food Services');

// Delivery & Tax Settings
define('DELIVERY_FEE', 0.00); // 0 for Free delivery
define('TAX_PERCENTAGE', 5.0); // 5% GST on restaurant services

// -----------------------------------------------------------------------------
// 3. DATABASE ENVIRONMENT CONFIGURATION
// -----------------------------------------------------------------------------
$is_local_env = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])
    || (php_sapi_name() === 'cli')
    || str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost')
    || str_starts_with($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'ngrok')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'pinggy')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost.run');

if ($is_local_env) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'hungerhub');
} else {
    // Production / Live Hosting credentials (cPanel / InfinityFree / Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'your_hosting_username');
    define('DB_PASS', 'your_hosting_password');
    define('DB_NAME', 'your_hosting_dbname');
}

// -----------------------------------------------------------------------------
// 4. DATABASE CONNECTION SINGLETON
// -----------------------------------------------------------------------------
function get_db_connection() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Database Connection Error: " . $conn->connect_error . " (Please verify settings in config.php)");
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

// Global connection instance for backward compatibility with existing code
$conn = get_db_connection();

// -----------------------------------------------------------------------------
// 5. SECURITY HELPERS
// -----------------------------------------------------------------------------
function sanitize_text($text) {
    return htmlspecialchars(trim((string)$text), ENT_QUOTES, 'UTF-8');
}

function verify_razorpay_signature($order_id, $payment_id, $signature, $secret) {
    $expected_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, $secret);
    return hash_equals($expected_signature, $signature);
}
