<?php
/**
 * HungerHub - Server Health & Installation Diagnostic Tool
 * Developed by: Sonu Kumar
 * Validates server dependencies, database connectivity, and directory permissions for production hosting.
 */

// Basic security: require session or allow initial setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$checks = [];

// 1. PHP Version Check
$php_version = phpversion();
$php_pass = version_compare($php_version, '7.4.0', '>=');
$checks[] = [
    'title' => 'PHP Version',
    'value' => 'PHP ' . $php_version,
    'status' => $php_pass ? 'pass' : 'fail',
    'recommendation' => 'PHP 8.0 or higher is recommended for optimal performance.'
];

// 2. Required PHP Extensions
$required_exts = ['mysqli', 'json', 'mbstring', 'session', 'curl'];
$missing_exts = [];
foreach ($required_exts as $ext) {
    if (!extension_loaded($ext)) {
        $missing_exts[] = $ext;
    }
}
$checks[] = [
    'title' => 'PHP Extensions',
    'value' => empty($missing_exts) ? 'All essential extensions loaded (mysqli, json, mbstring, session, curl)' : 'Missing: ' . implode(', ', $missing_exts),
    'status' => empty($missing_exts) ? 'pass' : 'fail',
    'recommendation' => 'Enable missing extensions in your hosting PHP settings / cPanel Select PHP Version.'
];

// 3. Database Connectivity
$db_pass = false;
$db_msg = '';
$table_count = 0;
$tables = [];

if (file_exists('config.php')) {
    include_once 'config.php';
    if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
        $db_pass = true;
        $db_msg = "Connected to database `" . DB_NAME . "` on `" . DB_HOST . "` as `" . DB_USER . "`";
        
        // Check tables
        $t_res = $conn->query("SHOW TABLES");
        if ($t_res) {
            while ($row = $t_res->fetch_array()) {
                $tables[] = $row[0];
            }
            $table_count = count($tables);
        }
    } else {
        $db_msg = "Database connection failed. Please check credentials in config.php";
    }
} else {
    $db_msg = "config.php file not found!";
}

$checks[] = [
    'title' => 'Database Connection',
    'value' => $db_msg,
    'status' => $db_pass ? 'pass' : 'fail',
    'recommendation' => 'Verify DB_HOST, DB_USER, DB_PASS, and DB_NAME in config.php.'
];

// 4. Database Schema Import Check
$required_tables = ['users', 'admins', 'menu_items', 'orders', 'order_items', 'payments', 'coupons'];
$missing_tables = array_diff($required_tables, $tables);
$schema_pass = $db_pass && empty($missing_tables);

$checks[] = [
    'title' => 'Database Schema & Tables',
    'value' => $schema_pass ? "Found $table_count tables (all core tables active)" : "Found $table_count tables. Missing core: " . (empty($missing_tables) ? 'None' : implode(', ', $missing_tables)),
    'status' => $schema_pass ? 'pass' : 'fail',
    'recommendation' => 'Import database.sql into your database via phpMyAdmin.'
];

// 5. Uploads Directory Writable
$uploads_dir = __DIR__ . '/uploads';
$uploads_writable = is_dir($uploads_dir) && is_writable($uploads_dir);
$checks[] = [
    'title' => 'Uploads Folder Permissions',
    'value' => $uploads_writable ? 'uploads/ directory is writable (CHMOD 755/777)' : 'uploads/ directory is not writable',
    'status' => $uploads_writable ? 'pass' : 'warning',
    'recommendation' => 'Set permissions on `uploads/` to 755 (or 775) in your hosting file manager so food photos can be uploaded.'
];

// 6. HTTPS Protocol Check
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$checks[] = [
    'title' => 'SSL / HTTPS Security',
    'value' => $is_https ? 'HTTPS is active (Encrypted traffic)' : 'HTTP detected (Not secure for live payment processing)',
    'status' => $is_https ? 'pass' : 'warning',
    'recommendation' => 'Enable free SSL certificate (Let\'s Encrypt / AutoSSL) in your hosting panel.'
];

// Calculate overall score
$all_passed = !in_array('fail', array_column($checks, 'status'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Health & Diagnostics - <?= REST_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 40px 0;
        }

        .diag-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            border: none;
            overflow: hidden;
        }

        .diag-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 30px;
        }

        .check-row {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .check-row:last-child {
            border-bottom: none;
        }

        .check-row:hover {
            background-color: #f8fafc;
        }

        .badge-pass {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-fail {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="diag-card">
                    <div class="diag-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-warning text-dark px-2 py-1 rounded">PRODUCTION READY</span>
                                <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded">V2.4</span>
                            </div>
                            <h2 class="fw-bold mb-0">Server Health & Installation Diagnostic</h2>
                            <p class="text-white-50 small mb-0"><?= REST_NAME ?> Deployment Pre-Flight Checklist</p>
                        </div>
                        <div class="text-end">
                            <span class="fs-1">
                                <?= $all_passed ? '🟢' : '🟡' ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Overall Deployment Status:</strong>
                            <?php if ($all_passed): ?>
                                <span class="badge badge-pass ms-2 px-3 py-2 fs-6"><i class="fas fa-check-circle me-1"></i>System Ready for Production</span>
                            <?php else: ?>
                                <span class="badge badge-fail ms-2 px-3 py-2 fs-6"><i class="fas fa-exclamation-triangle me-1"></i>Action Required</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-outline-primary btn-sm fw-semibold">
                                <i class="fas fa-store me-1"></i>Storefront
                            </a>
                            <a href="admin/admin_login.php" class="btn btn-primary btn-sm fw-semibold">
                                <i class="fas fa-user-shield me-1"></i>Admin Portal
                            </a>
                        </div>
                    </div>

                    <!-- Checklist -->
                    <div class="p-0">
                        <?php foreach ($checks as $item): ?>
                            <div class="check-row d-flex align-items-start gap-3">
                                <div class="mt-1 fs-5">
                                    <?php if ($item['status'] === 'pass'): ?>
                                        <i class="fas fa-circle-check text-success"></i>
                                    <?php elseif ($item['status'] === 'warning'): ?>
                                        <i class="fas fa-circle-exclamation text-warning"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold mb-0"><?= $item['title'] ?></h6>
                                        <span class="badge badge-<?= $item['status'] ?> text-uppercase" style="font-size: 0.7rem;">
                                            <?= $item['status'] ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 text-muted small"><?= $item['value'] ?></p>
                                    <?php if ($item['status'] !== 'pass'): ?>
                                        <div class="alert alert-light border p-2 mb-0 small text-dark">
                                            <strong><i class="fas fa-lightbulb text-warning me-1"></i>Recommendation:</strong> <?= $item['recommendation'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Deployment Guide Footer -->
                    <div class="p-4 bg-light border-top">
                        <h6 class="fw-bold mb-2"><i class="fas fa-book-open text-primary me-2"></i>Quick Hosting Deployment Instructions</h6>
                        <ol class="small text-muted mb-0 ps-3">
                            <li>Upload all files into your hosting <code>public_html</code> directory.</li>
                            <li>Create a MySQL database and user in your hosting control panel.</li>
                            <li>Open phpMyAdmin on your hosting and import <code>database.sql</code>.</li>
                            <li>Open <code>config.php</code> on your hosting file manager and enter your database credentials under Production.</li>
                            <li>Visit your website domain to start receiving real customer orders!</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>