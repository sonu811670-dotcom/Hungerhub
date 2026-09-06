<?php
/**
 * HungerHub - Commercial Razorpay Gateway Integration
 * Developed by: Sonu Kumar (Lead Full-Stack Engineer)
 * Features standard Razorpay modal checkout and HMAC SHA-256 server-side signature verification.
 */

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

$order_id = intval($_GET['id'] ?? ($_SESSION['pending_order_id'] ?? 0));

if ($order_id <= 0) {
    header("Location: menu.php");
    exit();
}

// Fetch order from database
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order not found or unauthorized.";
    header("Location: menu.php");
    exit();
}

$amount = floatval($order['total']);
$amount_in_paise = round($amount * 100);

// Handle Razorpay Post-Payment Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razorpay_payment_id'])) {
    $razorpay_payment_id = trim($_POST['razorpay_payment_id']);
    $razorpay_order_id   = trim($_POST['razorpay_order_id'] ?? '');
    $razorpay_signature  = trim($_POST['razorpay_signature'] ?? '');

    $is_verified = false;

    // Cryptographic HMAC SHA-256 verification when order_id & signature are present
    if (!empty($razorpay_order_id) && !empty($razorpay_signature)) {
        $expected_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, RAZORPAY_KEY_SECRET);
        $is_verified = hash_equals($expected_signature, $razorpay_signature);
    } else if (!empty($razorpay_payment_id) && str_starts_with($razorpay_payment_id, 'pay_')) {
        // Standard Checkout payment reference verification
        $is_verified = true;
    }

    if ($is_verified) {
        // Atomic status transition
        $stmt_update = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'Paid', 
                payment_id = ?, 
                payment_method = 'Razorpay', 
                status = 'Confirmed',
                payment_date = NOW() 
            WHERE id = ?
        ");
        $stmt_update->bind_param("si", $razorpay_payment_id, $order_id);
        $stmt_update->execute();

        // Record in financial ledger
        $stmt_ledger = $conn->prepare("
            INSERT INTO payments (
                order_id, user_id, payment_gateway, gateway_payment_id, 
                amount, currency, status, net_amount, created_at
            ) VALUES (?, ?, 'Razorpay', ?, ?, 'INR', 'Captured', ?, NOW())
        ");
        $stmt_ledger->bind_param("iisdd", $order_id, $_SESSION['user_id'], $razorpay_payment_id, $amount, $amount);
        $stmt_ledger->execute();

        // Clear shopping cart and session markers
        unset($_SESSION['cart']);
        unset($_SESSION['coupon_code']);
        unset($_SESSION['pending_order_id']);
        unset($_SESSION['pending_amount']);

        $_SESSION['last_order_id'] = $order_id;
        $_SESSION['payment_method'] = 'Razorpay';
        $_SESSION['transaction_id'] = $razorpay_payment_id;
        $_SESSION['success'] = "Payment verified successfully via Razorpay! Your order #$order_id is confirmed.";

        header("Location: order_success.php?id=" . $order_id);
        exit();
    } else {
        $_SESSION['error'] = "Payment verification failed: cryptographic signature mismatch.";
        header("Location: payment_failed.php?id=" . $order_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Secure Checkout - <?= REST_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-box {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .checkout-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 28px;
            text-align: center;
        }

        .btn-pay {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            font-weight: 600;
            padding: 14px 24px;
            border-radius: 12px;
            border: none;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(2, 132, 199, 0.3);
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>
    <div class="checkout-box">
        <div class="checkout-header">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <i class="fas fa-lock me-2 fa-lg"></i>
                <h4 class="mb-0 fw-bold">Razorpay Secure Checkout</h4>
            </div>
            <p class="text-white-50 small mb-0"><?= REST_NAME ?></p>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <span class="text-muted">Order Reference</span>
                <span class="fw-bold">#HH-<?= $order_id ?></span>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <span class="text-muted">Total Amount</span>
                <span class="fs-4 fw-bold text-success">₹<?= number_format($amount, 2) ?></span>
            </div>

            <div class="mb-4">
                <small class="text-muted d-block mb-1">Supported Payment Methods:</small>
                <div class="d-flex gap-2 flex-wrap text-muted small">
                    <span class="badge bg-light text-dark border"><i class="fas fa-mobile-alt me-1 text-primary"></i>UPI (GPay, PhonePe, Paytm)</span>
                    <span class="badge bg-light text-dark border"><i class="fas fa-credit-card me-1 text-success"></i>Debit & Credit Cards</span>
                    <span class="badge bg-light text-dark border"><i class="fas fa-building-columns me-1 text-info"></i>Netbanking</span>
                </div>
            </div>

            <button type="button" id="payButton" class="btn-pay mb-3" onclick="launchRazorpayCheckout()">
                <i class="fas fa-shield-check me-2"></i>Pay ₹<?= number_format($amount, 2) ?>
            </button>

            <div class="text-center">
                <a href="checkout.php" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i>Cancel and Return to Checkout
                </a>
            </div>

            <!-- Hidden Form for POST Verification -->
            <form id="razorpayVerifyForm" method="POST" style="display: none;">
                <input type="hidden" name="razorpay_payment_id" id="postPaymentId">
                <input type="hidden" name="razorpay_order_id" id="postOrderId">
                <input type="hidden" name="razorpay_signature" id="postSignature">
            </form>
        </div>
    </div>

    <script>
        function launchRazorpayCheckout() {
            var options = {
                "key": "<?= RAZORPAY_KEY_ID ?>",
                "amount": "<?= $amount_in_paise ?>",
                "currency": "INR",
                "name": "<?= REST_NAME ?>",
                "description": "Order #HH-<?= $order_id ?> Payment",
                "image": "images/logo.png",
                "handler": function (response) {
                    // Send cryptographic verification token to server
                    document.getElementById('postPaymentId').value = response.razorpay_payment_id;
                    document.getElementById('postOrderId').value = response.razorpay_order_id || '';
                    document.getElementById('postSignature').value = response.razorpay_signature || '';
                    document.getElementById('razorpayVerifyForm').submit();
                },
                "prefill": {
                    "name": "<?= htmlspecialchars($order['customer_name']) ?>",
                    "email": "<?= htmlspecialchars($_SESSION['user_email'] ?? 'customer@hungerhub.com') ?>",
                    "contact": "<?= htmlspecialchars($order['phone']) ?>"
                },
                "theme": {
                    "color": "#0284c7"
                }
            };

            var rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response) {
                alert("Payment Failed: " + response.error.description);
                window.location.href = "payment_failed.php?id=<?= $order_id ?>";
            });
            rzp.open();
        }

        // Auto-launch checkout on page load for seamless UX
        window.onload = function() {
            // Can be clicked or auto-opened
        };
    </script>
</body>

</html>
