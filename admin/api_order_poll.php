<?php
/**
 * HungerHub - Kitchen Display Telemetry & Order Polling API
 * Developed by: Sonu Kumar
 * Notifies admin dashboard when new orders or payment verifications arrive in real-time.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../db.php';

// Fetch highest order ID and order counts
$result = $conn->query("
    SELECT 
        MAX(id) as latest_id,
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN payment_status = 'Processing' OR (payment_status = 'Pending' AND payment_id IS NOT NULL AND payment_id != '') THEN 1 ELSE 0 END) as verify_needed_count
    FROM orders
");

$row = $result ? $result->fetch_assoc() : null;

echo json_encode([
    'success' => true,
    'latest_id' => (int)($row['latest_id'] ?? 0),
    'total_orders' => (int)($row['total_orders'] ?? 0),
    'pending_count' => (int)($row['pending_count'] ?? 0),
    'verify_needed_count' => (int)($row['verify_needed_count'] ?? 0)
]);