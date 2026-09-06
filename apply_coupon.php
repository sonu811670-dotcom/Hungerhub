<?php
session_start();
require 'db.php';
require 'includes/coupon_utils.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['coupon_error'] = 'Your cart is empty.';
    unset($_SESSION['coupon_code']);
    header('Location: cart.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit();
}

$raw_code = $_POST['coupon_code'] ?? '';
$code = hh_normalize_coupon_code($raw_code);

if ($code === '') {
    $_SESSION['coupon_error'] = 'Please enter a coupon code.';
    header('Location: cart.php');
    exit();
}

$coupon = hh_get_coupon_by_code($conn, $code);
if (!$coupon || !hh_is_coupon_valid($coupon)) {
    $_SESSION['coupon_error'] = 'Invalid or expired coupon code.';
    unset($_SESSION['coupon_code']);
    header('Location: cart.php');
    exit();
}

// Calculate cart subtotal
$ids = array_map('intval', array_keys($cart));
$ids = array_filter($ids, fn($v) => $v > 0);
if (empty($ids)) {
    $_SESSION['coupon_error'] = 'Your cart is empty.';
    unset($_SESSION['coupon_code']);
    header('Location: cart.php');
    exit();
}

$id_list = implode(',', $ids);
$result = $conn->query("SELECT id, price FROM menu_items WHERE id IN ($id_list)");
$subtotal = 0.0;
while ($row = $result->fetch_assoc()) {
    $id = (int)$row['id'];
    $qty = (int)($cart[$id]['quantity'] ?? 0);
    if ($qty > 0) {
        $subtotal += ((float)$row['price']) * $qty;
    }
}

if ($subtotal <= 0) {
    $_SESSION['coupon_error'] = 'Your cart subtotal is invalid.';
    unset($_SESSION['coupon_code']);
    header('Location: cart.php');
    exit();
}

$_SESSION['coupon_code'] = $coupon['code'];
$_SESSION['coupon_success'] = "Coupon applied: {$coupon['code']} ({$coupon['discount_percent']}% OFF)";

header('Location: cart.php');
exit();
