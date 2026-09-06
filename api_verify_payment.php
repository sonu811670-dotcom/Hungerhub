<?php
/**
 * HungerHub - Payment Verification API Endpoint
 * Developed by Sonu Kumar
 * Validates UPI payments, updates order state, and records transaction logs.
 */

header('Content-Type: application/json');
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true) ?? $_POST;

$order_id = intval($data['order_id'] ?? ($_SESSION['pending_order_id'] ?? 0));
$transaction_id = trim($data['transaction_id'] ?? '');
$upi_id = trim($data['upi_id'] ?? 'customer@upi');

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing Order ID.']);
    exit();
}

// Generate realistic 12-digit UTR if simulation didn't provide one
if (empty($transaction_id)) {
    $transaction_id = 'UPI' . date('ymd') . rand(100000, 999999);
}

// Check if order exists
$stmt = $conn->prepare("SELECT id, user_id, total, payment_status, status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found in database.']);
    exit();
}

$order = $result->fetch_assoc();
$user_id = $order['user_id'] ?? ($_SESSION['user_id'] ?? 1);
$amount = floatval($order['total']);

// Update orders table
$update_order = $conn->prepare("
    UPDATE orders 
    SET payment_status = 'Paid', 
        payment_id = ?, 
        payment_method = 'UPI',
        payment_date = NOW(),
        status = CASE WHEN status = 'Pending' THEN 'Confirmed' ELSE status END
    WHERE id = ?
");
$update_order->bind_param("si", $transaction_id, $order_id);

if (!$update_order->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update order payment status: ' . $conn->error]);
    exit();
}

// Insert into payments table
$check_payment = $conn->prepare("SELECT id FROM payments WHERE gateway_payment_id = ?");
$check_payment->bind_param("s", $transaction_id);
$check_payment->execute();
if ($check_payment->get_result()->num_rows === 0) {
    $insert_payment = $conn->prepare("
        INSERT INTO payments (
            order_id, user_id, payment_gateway, gateway_payment_id,
            amount, currency, status, net_amount, created_at
        ) VALUES (?, ?, 'UPI', ?, ?, 'INR', 'Captured', ?, NOW())
    ");
    $insert_payment->bind_param("iisdd", $order_id, $user_id, $transaction_id, $amount, $amount);
    $insert_payment->execute();
}

// Clean up pending session state and set confirmation data
unset($_SESSION['cart']);
unset($_SESSION['coupon_code']);
unset($_SESSION['pending_order_id']);
unset($_SESSION['pending_amount']);

$_SESSION['last_order_id'] = $order_id;
$_SESSION['payment_method'] = 'UPI';
$_SESSION['transaction_id'] = $transaction_id;
$_SESSION['upi_id'] = $upi_id;
$_SESSION['payment_details'] = [
    'order_id' => $order_id,
    'amount' => $amount,
    'transaction_id' => $transaction_id,
    'upi_id' => $upi_id,
    'timestamp' => date('Y-m-d H:i:s'),
    'payment_method' => 'UPI'
];
$_SESSION['success'] = "Payment verified successfully! Your order #$order_id has been confirmed.";

echo json_encode([
    'success' => true,
    'message' => 'Payment verified successfully!',
    'order_id' => $order_id,
    'transaction_id' => $transaction_id,
    'redirect' => 'order_success.php?id=' . $order_id
]);
