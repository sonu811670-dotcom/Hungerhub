<?php
/**
 * HungerHub - Order Confirmation & Success Portal
 * Developed by Sonu Kumar (Lead Full-Stack Developer)
 * Displays order confirmation, payment receipt metadata, and direct links to live tracking.
 */

session_start();
require_once 'db.php';

$order_id = intval($_GET['id'] ?? ($_SESSION['last_order_id'] ?? 0));

if ($order_id <= 0) {
    header("Location: menu.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: menu.php");
    exit();
}

$payment_method = $order['payment_method'] ?? ($_SESSION['payment_method'] ?? 'COD');
$transaction_id = $order['payment_id'] ?? ($_SESSION['transaction_id'] ?? null);
$success_msg = $_SESSION['success'] ?? "Your order has been placed successfully!";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed #HH-<?= $order_id ?> - HungerHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .success-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
            padding: 40px;
        }

        .checkmark-circle {
            width: 90px;
            height: 90px;
            background: #dcfce7;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #10b981;
            font-size: 3rem;
            margin-bottom: 24px;
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }

        .btn-track {
            background: linear-gradient(135deg, #ff6b35 0%, #f59e0b 100%);
            color: white;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(255, 107, 53, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-track:hover {
            background: linear-gradient(135deg, #e85a24 0%, #d97706 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -3px rgba(255, 107, 53, 0.4);
        }

        .order-meta-box {
            background: #f1f5f9;
            border-radius: 16px;
            padding: 24px;
            text-align: left;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="HungerHub" width="36" height="36" class="me-2" onerror="this.style.display='none'">
                <span class="fw-bold text-warning">HungerHub</span>
            </a>
            <div class="ms-auto">
                <a href="order_history.php" class="btn btn-outline-light btn-sm">My Orders</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 text-center">
                <div class="success-card">
                    <div class="checkmark-circle">
                        <i class="fas fa-check"></i>
                    </div>

                    <h2 class="fw-bold mb-2">Order Confirmed!</h2>
                    <p class="text-muted mb-4">
                        Thank you, <strong class="text-dark"><?= htmlspecialchars($order['customer_name'] ?? 'Customer') ?></strong>! 
                        Your delicious meal is being processed.
                    </p>

                    <div class="alert alert-success d-inline-block px-4 py-2 rounded-pill small mb-4">
                        <i class="fas fa-circle-info me-1"></i><?= htmlspecialchars($success_msg) ?>
                    </div>

                    <!-- Primary Actions -->
                    <div class="d-grid gap-3 d-sm-flex justify-content-center mb-4">
                        <a href="track_order.php?id=<?= $order_id ?>" class="btn-track">
                            <i class="fas fa-satellite-dish me-2"></i>Track Order Live
                        </a>
                        <a href="invoice.php?id=<?= $order_id ?>" class="btn btn-outline-dark px-4 py-3 rounded-3 fw-semibold" target="_blank">
                            <i class="fas fa-file-invoice me-2"></i>Print Invoice
                        </a>
                    </div>

                    <!-- Order Details Summary Box -->
                    <div class="order-meta-box mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div>
                                <small class="text-muted d-block">Order Reference</small>
                                <span class="fw-bold text-dark fs-5">#HH-<?= $order_id ?></span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Order Total</small>
                                <span class="fw-bold text-success fs-5">₹<?= number_format($order['total'], 2) ?></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block">Dishes Ordered:</label>
                            <span class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($order['items'])) ?></span>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block">Delivering To:</label>
                            <span class="text-dark small"><i class="fas fa-location-dot text-danger me-1"></i><?= htmlspecialchars($order['address']) ?></span>
                        </div>

                        <div class="row g-2 pt-2 border-top">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Payment Mode</small>
                                <span class="badge bg-secondary"><?= htmlspecialchars($order['payment_method']) ?></span>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <small class="text-muted d-block">Payment Status</small>
                                <span class="badge bg-<?= $order['payment_status'] === 'Paid' ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($order['payment_status'] ?? 'Pending') ?>
                                </span>
                            </div>
                            <?php if (!empty($transaction_id)): ?>
                                <div class="col-12 mt-2">
                                    <small class="text-muted d-block">Transaction / UTR Reference</small>
                                    <code><?= htmlspecialchars($transaction_id) ?></code>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Secondary Links -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="menu.php" class="text-decoration-none text-muted small">
                            <i class="fas fa-utensils me-1"></i>Order More Food
                        </a>
                        <span class="text-muted">•</span>
                        <a href="order_history.php" class="text-decoration-none text-muted small">
                            <i class="fas fa-clock-rotate-left me-1"></i>Order History
                        </a>
                        <span class="text-muted">•</span>
                        <a href="index.php" class="text-decoration-none text-muted small">
                            <i class="fas fa-house me-1"></i>Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const count = 200;
            const defaults = { origin: { y: 0.7 } };
            function fire(particleRatio, opts) {
                confetti(Object.assign({}, defaults, opts, {
                    particleCount: Math.floor(count * particleRatio)
                }));
            }
            fire(0.25, { spread: 26, startVelocity: 55 });
            fire(0.2, { spread: 60 });
            fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
            fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
            fire(0.1, { spread: 120, startVelocity: 45 });
        });
    </script>
</body>

</html>
<?php
// Clear confirmation flags
unset($_SESSION['success']);
?>
