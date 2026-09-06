<?php
/**
 * HungerHub - Payment Status Polling API
 * Developed by Sonu Kumar
 * Checks whether an order has been paid.
 */

header('Content-Type: application/json');
session_start();
require_once 'db.php';

$order_id = intval($_GET['order_id'] ?? ($_SESSION['pending_order_id'] ?? 0));

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'paid' => false, 'message' => 'Missing order ID']);
    exit();
}

$stmt = $conn->prepare("SELECT id, payment_status, status, payment_id, total FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'paid' => false, 'message' => 'Order not found']);
    exit();
}

$order = $res->fetch_assoc();
$is_paid = ($order['payment_status'] === 'Paid');

if ($is_paid) {
    unset($_SESSION['cart']);
    unset($_SESSION['coupon_code']);
    unset($_SESSION['pending_order_id']);
    unset($_SESSION['pending_amount']);
    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['payment_method'] = 'UPI';
    $_SESSION['transaction_id'] = $order['payment_id'];
    $_SESSION['success'] = "Payment completed! Your order #$order_id is confirmed.";
}

echo json_encode([
    'success' => true,
    'order_id' => $order_id,
    'paid' => $is_paid,
    'status' => $order['status'],
    'payment_status' => $order['payment_status'],
    'redirect' => $is_paid ? 'order_success.php?id=' . $order_id : null
]);
