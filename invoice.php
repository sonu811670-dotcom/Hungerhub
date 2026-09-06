<?php
/**
 * HungerHub - Professional GST-Ready Tax Invoice & Receipt Generator
 * Developed by Sonu Kumar (Lead Full-Stack Developer)
 * Features A4 print optimization, itemized breakdown, and audit-ready receipt formatting.
 */

session_start();
require_once 'db.php';

$order_id = intval($_GET['id'] ?? ($_SESSION['last_order_id'] ?? 0));

if ($order_id <= 0) {
    die("Invalid Order ID. Please access invoice via Order History.");
}

$stmt = $conn->prepare("
    SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found in database.");
}

// Fetch payment transaction ID if available
$payment_id = $order['payment_id'] ?? 'N/A';
if ($order['payment_method'] === 'UPI' && $payment_id === 'N/A') {
    $pay_stmt = $conn->prepare("SELECT gateway_payment_id FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1");
    $pay_stmt->bind_param("i", $order_id);
    $pay_stmt->execute();
    $pay_row = $pay_stmt->get_result()->fetch_assoc();
    if ($pay_row) {
        $payment_id = $pay_row['gateway_payment_id'];
    }
}

// Parse items text
$items_raw = $order['items'];
$parsed_items = [];
$lines = explode(',', $items_raw);
$subtotal_calc = 0.0;

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    
    // Check pattern "Item Name (x2)"
    if (preg_match('/^(.*?)\s*\(x(\d+)\)$/i', $line, $matches)) {
        $name = trim($matches[1]);
        $qty = intval($matches[2]);
    } else {
        $name = $line;
        $qty = 1;
    }
    
    // Look up price in menu_items
    $lookup = $conn->prepare("SELECT price FROM menu_items WHERE name LIKE ? LIMIT 1");
    $searchTerm = "%$name%";
    $lookup->bind_param("s", $searchTerm);
    $lookup->execute();
    $dish = $lookup->get_result()->fetch_assoc();
    $unit_price = $dish ? floatval($dish['price']) : 0.0;
    
    $item_total = $unit_price * $qty;
    $subtotal_calc += $item_total;
    
    $parsed_items[] = [
        'name' => $name,
        'qty' => $qty,
        'unit_price' => $unit_price,
        'total' => $item_total
    ];
}

$discount_amount = floatval($order['coupon_discount_amount'] ?? 0);
$grand_total = floatval($order['total']);
if ($subtotal_calc == 0) {
    $subtotal_calc = $grand_total + $discount_amount;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-HH-<?= $order_id ?> - HungerHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            padding-bottom: 40px;
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 40px;
            margin-top: 30px;
        }

        .invoice-title {
            font-weight: 700;
            color: #ff6b35;
        }

        .table-invoice th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
        }

        .table-invoice td {
            vertical-align: middle;
        }

        .total-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }

        .status-stamp {
            border: 2px dashed #10b981;
            color: #10b981;
            padding: 6px 18px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
        }

        .status-stamp.pending {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Top Action Bar (hidden when printing) -->
    <div class="container mt-4 no-print">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm">
            <a href="track_order.php?id=<?= $order_id ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Live Tracking
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="container">
        <div class="invoice-card">
            <!-- Header Section -->
            <div class="row align-items-center border-bottom pb-4 mb-4">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center mb-2">
                        <img src="images/logo.png" alt="Logo" width="48" height="48" class="me-2" onerror="this.style.display='none'">
                        <h2 class="invoice-title mb-0"><?= REST_NAME ?></h2>
                    </div>
                    <p class="text-muted small mb-0">
                        <?= REST_TAGLINE ?><br>
                        <?= REST_ADDRESS ?><br>
                        Phone: <?= REST_PHONE ?> | Email: <?= REST_EMAIL ?><br>
                        GSTIN: <?= REST_GSTIN ?> | FSSAI: <?= REST_FSSAI ?>
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <h4 class="fw-bold mb-1">TAX INVOICE</h4>
                    <p class="text-muted mb-1">Invoice No: <strong>#INV-HH-<?= date('Y') ?>-<?= str_pad($order_id, 4, '0', STR_PAD_LEFT) ?></strong></p>
                    <p class="text-muted mb-2">Date: <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                    <div>
                        <span class="status-stamp <?= $order['payment_status'] === 'Paid' ? '' : 'pending' ?>">
                            <?= $order['payment_status'] === 'Paid' ? 'PAID' : 'PAYMENT PENDING' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer & Order Meta -->
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-uppercase text-muted small">Billed To:</h6>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($order['customer_name'] ?? 'Valued Customer') ?></h5>
                    <p class="text-muted mb-1"><i class="fas fa-phone me-1 text-primary"></i> <?= htmlspecialchars($order['phone']) ?></p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-location-dot me-1 text-danger"></i>
                        <?= nl2br(htmlspecialchars($order['address'])) ?>
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <h6 class="fw-bold text-uppercase text-muted small">Payment Details:</h6>
                    <p class="mb-1">Method: <strong><?= htmlspecialchars($order['payment_method']) ?></strong></p>
                    <?php if ($order['payment_method'] === 'UPI'): ?>
                        <p class="mb-1 text-muted small">UPI Ref / UTR: <code><?= htmlspecialchars($payment_id) ?></code></p>
                    <?php endif; ?>
                    <p class="mb-0 text-muted small">Order Status: <span class="badge bg-secondary"><?= htmlspecialchars($order['status']) ?></span></p>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="table-responsive mb-4">
                <table class="table table-invoice">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Dish Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($parsed_items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="text-center"><?= $item['qty'] ?></td>
                                <td class="text-end">₹<?= number_format($item['unit_price'] > 0 ? $item['unit_price'] : ($item['total'] / $item['qty']), 2) ?></td>
                                <td class="text-end fw-semibold">₹<?= number_format($item['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Calculations Box -->
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="total-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">₹<?= number_format($subtotal_calc, 2) ?></span>
                        </div>

                        <?php if ($discount_amount > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Coupon Discount (<?= htmlspecialchars($order['coupon_code'] ?? 'PROMO') ?>):</span>
                                <span class="fw-semibold">-₹<?= number_format($discount_amount, 2) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Delivery Partner Fee:</span>
                            <span class="text-success fw-semibold">FREE</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Taxes & CGST/SGST (5%):</span>
                            <span class="text-muted small">Included</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="fs-5">Grand Total:</strong>
                            <strong class="fs-4 text-primary">₹<?= number_format($grand_total, 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="border-top pt-4 mt-4 text-center text-muted small">
                <p class="mb-1"><strong>Thank you for choosing HungerHub!</strong></p>
                <p class="mb-0">This is a system-generated electronic receipt created by HungerHub Order Management. For inquiries, reach out at info@hungerhub.com.</p>
            </div>
        </div>
    </div>
</body>

</html>
