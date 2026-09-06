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
    || str_starts_with($_SERVER['HTTP_HOST'] ?? '', '192.168.')
    || str_starts_with($_SERVER['HTTP_HOST'] ?? '', '10.')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'ngrok')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'pinggy')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost.run')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'lhr.life')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'trycloudflare');

if ($is_local_env) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('DB_NAME', getenv('DB_NAME') ?: 'hungerhub');
} else {
    // Production / Live Hosting credentials (InfinityFree)
    define('DB_HOST', getenv('DB_HOST') ?: 'sql211.infinityfree.com');
    define('DB_USER', getenv('DB_USER') ?: 'if0_42836912');
    define('DB_PASS', getenv('DB_PASS') ?: 'YOUR_INFINITYFREE_PASSWORD');
    define('DB_NAME', getenv('DB_NAME') ?: 'if0_42836912_hungerhub');
}

// -----------------------------------------------------------------------------
// 4. DATABASE CONNECTION SINGLETON
// -----------------------------------------------------------------------------
function get_db_connection() {
    static $conn = null;
    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            http_response_code(500);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Database Configuration Required - <?= htmlspecialchars(REST_NAME) ?></title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
            </head>
            <body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-4">
                <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="max-width: 580px;">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-database fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Live Database Setup Required</h4>
                    <p class="text-muted small">HungerHub is deployed to your live server. Please set your hosting MySQL credentials to connect to your live database.</p>
                    <div class="alert alert-warning text-start small mb-3">
                        <strong>Quick Setup Steps:</strong><br>
                        1. Open <code>config.php</code> in your hosting File Manager.<br>
                        2. Under Production credentials, update <code>DB_USER</code>, <code>DB_PASS</code>, and <code>DB_NAME</code>.<br>
                        3. Import <code>database.sql</code> into your database using phpMyAdmin.<br>
                        <br>
                        <span class="text-danger"><em>Server message: <?= htmlspecialchars($conn->connect_error) ?></em></span>
                    </div>
                    <a href="check_installation.php" class="btn btn-primary btn-sm fw-semibold">
                        <i class="fas fa-stethoscope me-1"></i>Run Server Health Diagnostic
                    </a>
                </div>
            </body>
            </html>
            <?php
            exit();
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
