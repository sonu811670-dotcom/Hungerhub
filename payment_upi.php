<?php
/**
 * HungerHub - Direct Restaurant UPI Payment & UTR Reconciliation Gateway
 * Developed by: Sonu Kumar (Lead Full-Stack Engineer)
 * Allows customers to pay directly to the restaurant VPA and submit real bank UTR for verification.
 */

session_start();
require_once 'config.php';

// Check if user has a pending order
$order_id = intval($_GET['id'] ?? ($_SESSION['pending_order_id'] ?? 0));

if ($order_id <= 0) {
    header("Location: checkout.php");
    exit();
}

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: checkout.php");
    exit();
}

$amount = floatval($order['total']);
$merchant_vpa = MERCHANT_UPI_VPA;
$merchant_name = MERCHANT_UPI_NAME;

// Handle UTR Submission
$error_msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_utr'])) {
    $utr_number = trim($_POST['utr_number'] ?? '');
    $customer_upi = trim($_POST['customer_upi'] ?? '');

    // Validate 12-digit UTR
    if (strlen($utr_number) < 8) {
        $error_msg = "Please enter a valid Bank UTR / UPI Transaction Reference Number.";
    } else {
        // Record payment as Processing (awaiting manager verification)
        $update = $conn->prepare("
            UPDATE orders 
            SET payment_method = 'UPI',
                payment_id = ?,
                payment_status = 'Processing',
                payment_date = NOW()
            WHERE id = ?
        ");
        $update->bind_param("si", $utr_number, $order_id);
        $update->execute();

        // Insert into payments ledger
        $user_id = $order['user_id'] ?? 1;
        $insert_pay = $conn->prepare("
            INSERT INTO payments (
                order_id, user_id, payment_gateway, gateway_payment_id,
                amount, currency, status, net_amount, created_at
            ) VALUES (?, ?, 'UPI', ?, ?, 'INR', 'Authorized', ?, NOW())
        ");
        $insert_pay->bind_param("iisdd", $order_id, $user_id, $utr_number, $amount, $amount);
        $insert_pay->execute();

        // Clear shopping cart and session markers
        unset($_SESSION['cart']);
        unset($_SESSION['coupon_code']);
        unset($_SESSION['pending_order_id']);
        unset($_SESSION['pending_amount']);

        $_SESSION['last_order_id'] = $order_id;
        $_SESSION['payment_method'] = 'UPI';
        $_SESSION['transaction_id'] = $utr_number;
        $_SESSION['success'] = "Payment reference submitted! Order #$order_id is pending restaurant verification.";

        header("Location: order_success.php?id=" . $order_id);
        exit();
    }
}

// Generate UPI intent link
$upi_intent = "upi://pay?pa=" . urlencode($merchant_vpa) .
    "&pn=" . urlencode($merchant_name) .
    "&am=" . number_format($amount, 2, '.', '') .
    "&cu=INR" .
    "&tn=" . urlencode("Order #" . $order_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct UPI Payment - <?= REST_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            color: #334155;
            padding: 30px 15px;
        }

        .upi-box {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            max-width: 520px;
            margin: 0 auto;
        }

        .upi-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f59e0b 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .qr-frame {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .qr-img {
            max-width: 220px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        .btn-submit-utr {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 12px;
            border: none;
            width: 100%;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .btn-submit-utr:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>
    <div class="upi-box">
        <div class="upi-header">
            <div class="d-flex align-items-center justify-content-center mb-1">
                <i class="fas fa-qrcode me-2 fa-lg"></i>
                <h4 class="mb-0 fw-bold">Direct UPI Payment</h4>
            </div>
            <p class="text-white-50 small mb-0"><?= REST_NAME ?></p>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <span class="text-muted">Order ID</span>
                <span class="fw-bold">#HH-<?= $order_id ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <span class="text-muted">Payable Amount</span>
                <span class="fs-4 fw-bold text-success">₹<?= number_format($amount, 2) ?></span>
            </div>

            <!-- QR Code Box -->
            <div class="qr-frame">
                <img src="images/upi_qr_code.jpeg"
                     alt="UPI QR Code"
                     class="img-fluid qr-img mb-2"
                     onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($upi_intent) ?>';">
                
                <div class="mt-2">
                    <small class="text-muted d-block mb-1">UPI ID: <strong><?= htmlspecialchars($merchant_vpa) ?></strong></small>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="navigator.clipboard.writeText('<?= $merchant_vpa ?>'); alert('UPI ID copied!');">
                        <i class="fas fa-copy me-1"></i>Copy UPI ID
                    </button>
                </div>
            </div>

            <!-- Steps for Customer -->
            <div class="bg-light p-3 rounded-3 mb-4 small">
                <strong class="d-block mb-2 text-dark"><i class="fas fa-circle-info text-primary me-1"></i>Payment Steps:</strong>
                <ol class="mb-0 ps-3 text-muted">
                    <li>Scan QR with Google Pay, PhonePe, Paytm, or BHIM.</li>
                    <li>Pay the exact amount: <strong>₹<?= number_format($amount, 2) ?></strong>.</li>
                    <li>Copy the <strong>12-digit UTR / UPI Ref ID</strong> from your transaction receipt.</li>
                    <li>Paste the UTR number below and submit for instant verification.</li>
                </ol>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger py-2 small">
                    <i class="fas fa-triangle-exclamation me-1"></i><?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <!-- UTR Submission Form -->
            <form method="POST">
                <div class="mb-3">
                    <label for="utr_number" class="form-label fw-semibold small">12-Digit Bank UTR / UPI Reference No. *</label>
                    <input type="text"
                           class="form-control"
                           id="utr_number"
                           name="utr_number"
                           placeholder="e.g. 423819201948"
                           maxlength="25"
                           required>
                    <div class="form-text">Found in your UPI app transaction receipt or bank SMS.</div>
                </div>

                <div class="mb-3">
                    <label for="customer_upi" class="form-label fw-semibold small">Your UPI ID / Phone (Optional)</label>
                    <input type="text"
                           class="form-control"
                           id="customer_upi"
                           name="customer_upi"
                           placeholder="yourname@okhdfcbank">
                </div>

                <button type="submit" name="submit_utr" class="btn-submit-utr mb-3">
                    <i class="fas fa-check-circle me-2"></i>Submit UTR for Verification
                </button>
            </form>

            <div class="text-center">
                <a href="checkout.php" class="text-muted small text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Cancel & Back to Checkout
                </a>
            </div>
        </div>
    </div>
</body>

</html>
