<?php
session_start();
require 'db.php';
require 'payment_config.php';
require 'includes/coupon_utils.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: user/login.php");
  exit();
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
  header("Location: cart.php");
  exit();
}

$subtotal = 0;
$items_summary = [];

// Calculate cart subtotal and items
$ids = array_map('intval', array_keys($cart));
$ids = array_filter($ids, fn($v) => $v > 0);
$id_list = implode(',', $ids);
$query = $conn->query("SELECT * FROM menu_items WHERE id IN ($id_list)");
while ($item = $query->fetch_assoc()) {
  $id = $item['id'];
  $qty = $cart[$id]['quantity'];
  $line_subtotal = $item['price'] * $qty;
  $subtotal += $line_subtotal;
  $items_summary[] = "{$item['name']} (x$qty)";
}

// Coupon calculation
$coupon_code = $_SESSION['coupon_code'] ?? null;
$coupon_discount_percent = 0.0;
$coupon_discount_amount = 0.0;

if (!empty($coupon_code) && $subtotal > 0) {
  $coupon_code = hh_normalize_coupon_code((string)$coupon_code);
  $coupon = hh_get_coupon_by_code($conn, $coupon_code);
  if ($coupon && hh_is_coupon_valid($coupon)) {
    $coupon_discount_percent = (float)$coupon['discount_percent'];
    $coupon_discount_amount = hh_calculate_discount_amount((float)$subtotal, $coupon_discount_percent);
  } else {
    unset($_SESSION['coupon_code']);
    $coupon_code = null;
  }
}

$discounted_subtotal = max(0, (float)$subtotal - (float)$coupon_discount_amount);

// Get available payment methods for this order
$available_payment_methods = [];
foreach (getPaymentMethods() as $method => $config) {
  if (isPaymentMethodAvailable($method, $discounted_subtotal)) {
    $available_payment_methods[$method] = $config;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = htmlspecialchars(trim($_POST['name']));
  $phone = htmlspecialchars(trim($_POST['phone']));
  $address = htmlspecialchars(trim($_POST['address']));
  $payment_method = $_POST['payment_method'] ?? 'COD';
  $user_id = $_SESSION['user_id'];

  // Validate payment method
  if (!isPaymentMethodAvailable($payment_method, $discounted_subtotal)) {
    $_SESSION['error'] = "Selected payment method is not available for this order.";
    header("Location: checkout.php");
    exit();
  }

  $item_list = implode(", ", $items_summary);

  // Calculate payment fee
  $payment_fee = calculatePaymentFee($discounted_subtotal, $payment_method);
  $final_total = $discounted_subtotal + $payment_fee;

  // Insert into orders with payment information
  $stmt = $conn->prepare("
      INSERT INTO orders (
        user_id, customer_name, phone, address, items, total, 
        payment_method, payment_status, payment_amount, currency,
        coupon_code, coupon_discount_percent, coupon_discount_amount
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

  $payment_status = ($payment_method === 'COD') ? 'Pending' : 'Pending';
  $currency = DEFAULT_CURRENCY;

  $stmt->bind_param(
    "issssdssdssdd",
    $user_id,
    $name,
    $phone,
    $address,
    $item_list,
    $final_total,
    $payment_method,
    $payment_status,
    $final_total,
    $currency,
    $coupon_code,
    $coupon_discount_percent,
    $coupon_discount_amount
  );

  if ($stmt->execute()) {
    $order_id = $conn->insert_id;

    // Handle different payment methods
    $_SESSION['last_order_id'] = $order_id;
    if ($payment_method === 'COD') {
      // COD - redirect to success page
      unset($_SESSION['cart']);
      unset($_SESSION['coupon_code']);
      $_SESSION['payment_method'] = 'COD';
      $_SESSION['success'] = "Your order has been placed successfully! You can pay cash on delivery.";
      header("Location: order_success.php?id=" . $order_id);
      exit();
    } else if ($payment_method === 'UPI') {
      // UPI - redirect to UPI payment page
      $_SESSION['pending_order_id'] = $order_id;
      $_SESSION['pending_amount'] = $final_total;
      header("Location: payment_upi.php");
      exit();
    } else if ($payment_method === 'RAZORPAY') {
      // Razorpay - real online gateway (UPI, Cards, Netbanking)
      $_SESSION['pending_order_id'] = $order_id;
      $_SESSION['pending_amount'] = $final_total;
      header("Location: payment_razorpay.php?id=" . $order_id);
      exit();
    }
  } else {
    $_SESSION['error'] = "Failed to place order. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Checkout - HungerHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4">🧾 Checkout</h2>

    <div class="row">
      <!-- Order Form -->
      <div class="col-md-6">
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                          unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="post" id="checkoutForm">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control"
              value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
              value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Delivery Address</label>
            <textarea name="address" class="form-control" required><?= htmlspecialchars($_SESSION['user_address'] ?? '') ?></textarea>
          </div>

          <!-- Payment Method Selection -->
          <div class="mb-4">
            <h5 class="mb-3"><i class="fas fa-credit-card"></i> Select Payment Method</h5>
            <?php foreach ($available_payment_methods as $method => $config): ?>
              <?php
              $fee = calculatePaymentFee($discounted_subtotal, $method);
              $fee_text = $fee > 0 ? " (+₹" . number_format($fee, 2) . " fee)" : "";
              ?>
              <div class="card mb-2 payment-method-card" data-method="<?= $method ?>" data-fee="<?= $fee ?>">
                <div class="card-body p-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="payment_<?= $method ?>"
                      value="<?= $method ?>" <?= $method === 'COD' ? 'checked' : '' ?> required>
                    <label class="form-check-label w-100" for="payment_<?= $method ?>">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <i class="<?= $config['icon'] ?> me-2"></i>
                          <strong><?= $config['name'] ?></strong>
                          <small class="text-muted d-block"><?= $config['description'] ?></small>
                        </div>
                        <div class="text-end">
                          <small class="text-success"><?= $config['processing_time'] ?></small>
                          <?php if ($fee > 0): ?>
                            <small class="text-muted d-block"><?= $fee_text ?></small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <button type="submit" class="btn btn-success w-100" id="placeOrderBtn">
            <i class="fas fa-shopping-cart"></i> Place Order
          </button>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5><i class="fas fa-receipt"></i> Order Summary</h5>
          </div>
          <div class="card-body">
            <?php
            $ids = array_map('intval', array_keys($cart));
            $ids = array_filter($ids, fn($v) => $v > 0);
            $id_list = implode(',', $ids);
            $result = $conn->query("SELECT * FROM menu_items WHERE id IN ($id_list)");
            $cartItems = [];
            $summary_subtotal = 0;
            while ($item = $result->fetch_assoc()):
              $id = $item['id'];
              $qty = $cart[$id]['quantity'];
              $summary_line_subtotal = $item['price'] * $qty;
              $summary_subtotal += $summary_line_subtotal;
              $cartItems[] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $qty
              ];
            ?>
              <div class="d-flex justify-content-between mb-2">
                <span><?= htmlspecialchars($item['name']) ?> x <?= $qty ?></span>
                <span>₹<?= number_format($summary_line_subtotal, 2) ?></span>
              </div>
            <?php endwhile; ?>
            <hr>
            <div class="d-flex justify-content-between">
              <strong>Subtotal:</strong>
              <strong id="subtotal">₹<?= number_format($subtotal, 2) ?></strong>
            </div>
            <?php if (!empty($coupon_code) && $coupon_discount_amount > 0): ?>
              <div class="d-flex justify-content-between text-muted">
                <span>Coupon (<?= htmlspecialchars($coupon_code) ?>):</span>
                <span>-₹<?= number_format($coupon_discount_amount, 2) ?></span>
              </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between text-muted" id="payment-fee-row" style="display: none;">
              <span>Payment Fee:</span>
              <span id="payment-fee">₹0.00</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <strong>Total:</strong>
              <strong class="text-success" id="final-total">₹<?= number_format($discounted_subtotal, 2) ?></strong>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="card mt-3" id="payment-info" style="display: none;">
          <div class="card-header">
            <h6><i class="fas fa-info-circle"></i> Payment Information</h6>
          </div>
          <div class="card-body" id="payment-details">
            <!-- Payment specific information will be shown here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
  <script>
    $(document).ready(function() {
      // Handle payment method selection
      $('input[name="payment_method"]').change(function() {
        var method = $(this).val();
        var fee = parseFloat($('.payment-method-card[data-method="' + method + '"]').data('fee'));
        var subtotal = <?= $discounted_subtotal ?>;
        var finalTotal = subtotal + fee;

        // Update fee display
        if (fee > 0) {
          $('#payment-fee-row').show();
          $('#payment-fee').text('₹' + fee.toFixed(2));
        } else {
          $('#payment-fee-row').hide();
        }

        // Update total
        $('#final-total').text('₹' + finalTotal.toFixed(2));

        // Show payment information
        updatePaymentInfo(method);
        $('#payment-info').show();
      });

      // Update payment information based on selected method
      function updatePaymentInfo(method) {
        var infoHtml = '';

        switch (method) {
          case 'COD':
            infoHtml = `
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-money-bill-wave"></i>
                        <strong>Cash on Delivery</strong><br>
                        Pay with cash when your order arrives. No advance payment required.
                    </div>
                `;
            break;
          case 'RAZORPAY':
            infoHtml = `
                    <div class="alert alert-primary mb-0">
                        <i class="fas fa-credit-card"></i>
                        <strong>Razorpay Payment</strong><br>
                        Secure payment with UPI, Cards, Net Banking, and Wallets.
                        <br><small class="text-muted">You will be redirected to Razorpay's secure payment page.</small>
                    </div>
                `;
            break;
          case 'PAYPAL':
            infoHtml = `
                    <div class="alert alert-warning mb-0">
                        <i class="fab fa-paypal"></i>
                        <strong>PayPal Payment</strong><br>
                        Pay securely with your PayPal account or credit card.
                        <br><small class="text-muted">You will be redirected to PayPal's secure checkout.</small>
                    </div>
                `;
            break;
        }

        $('#payment-details').html(infoHtml);
      }

      // Initialize with default payment method
      var defaultMethod = $('input[name="payment_method"]:checked').val();
      if (defaultMethod) {
        updatePaymentInfo(defaultMethod);
        $('#payment-info').show();
      }

      // Add visual feedback for payment method selection
      $('.payment-method-card').click(function() {
        $('.payment-method-card').removeClass('border-primary');
        $(this).addClass('border-primary');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
      });

      // Style the selected payment method card
      $('input[name="payment_method"]').change(function() {
        $('.payment-method-card').removeClass('border-primary');
        $(this).closest('.payment-method-card').addClass('border-primary');
      });

      // Initialize with COD selected
      $('input[name="payment_method"]:checked').closest('.payment-method-card').addClass('border-primary');
    });
  </script>

</body>

</html>