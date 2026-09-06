<?php
session_start();
require 'db.php';
require 'includes/coupon_utils.php';

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;

// If cart is empty, remove any applied coupon
if (empty($cart)) {
  unset($_SESSION['coupon_code']);
}

if (!empty($cart)) {
  $ids = array_map('intval', array_keys($cart));
  $ids = array_filter($ids, fn($v) => $v > 0);
  $ids = implode(',', $ids);
  $sql = "SELECT * FROM menu_items WHERE id IN ($ids)";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $quantity = $cart[$id]['quantity'];
    $subtotal = $row['price'] * $quantity;
    $total_price += $subtotal;

    $cart_items[] = [
      'id' => $id,
      'name' => $row['name'],
      'image' => $row['image'],
      'price' => $row['price'],
      'quantity' => $quantity,
      'subtotal' => $subtotal
    ];
  }
}

// Coupon calculation
$coupon_code = $_SESSION['coupon_code'] ?? null;
$coupon = null;
$discount_percent = 0.0;
$discount_amount = 0.0;

if (!empty($coupon_code) && $total_price > 0) {
  $coupon_code = hh_normalize_coupon_code((string)$coupon_code);
  $coupon = hh_get_coupon_by_code($conn, $coupon_code);
  if ($coupon && hh_is_coupon_valid($coupon)) {
    $discount_percent = (float)$coupon['discount_percent'];
    $discount_amount = hh_calculate_discount_amount((float)$total_price, $discount_percent);
  } else {
    unset($_SESSION['coupon_code']);
    $coupon_code = null;
  }
}

$final_total = max(0, (float)$total_price - (float)$discount_amount);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cart - HungerHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">

  <?php include 'includes/navbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4">🛒 Your Cart</h2>

    <?php if (isset($_SESSION['coupon_success'])): ?>
      <div class="alert alert-success"><?php echo $_SESSION['coupon_success'];
                                        unset($_SESSION['coupon_success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['coupon_error'])): ?>
      <div class="alert alert-danger"><?php echo $_SESSION['coupon_error'];
                                      unset($_SESSION['coupon_error']); ?></div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
      <div class="alert alert-info">Your cart is empty.</div>
      <a href="menu.php" class="btn btn-primary">Browse Menu</a>
    <?php else: ?>
      <div class="table-responsive shadow-sm rounded-3 mb-4">
        <table class="table table-bordered align-middle text-center mb-0">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Item</th>
              <th>Price (₹)</th>
              <th>Quantity</th>
              <th>Subtotal (₹)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_items as $item): ?>
              <tr>
                <td><img src="<?= htmlspecialchars($item['image']) ?>" width="70" height="70" style="object-fit:cover;" class="rounded" onerror="this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/' + (this.getAttribute('src') || '');"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['subtotal'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
            <tr class="table-light">
              <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
              <td><strong>₹ <?= number_format($total_price, 2) ?></strong></td>
            </tr>
            <?php if ($discount_amount > 0 && !empty($coupon_code)): ?>
              <tr class="table-light">
                <td colspan="4" class="text-end">
                  <strong>Coupon (<?= htmlspecialchars($coupon_code) ?>):</strong>
                </td>
                <td><strong>-₹ <?= number_format($discount_amount, 2) ?></strong></td>
              </tr>
            <?php endif; ?>
            <tr class="table-light">
              <td colspan="4" class="text-end"><strong>Total:</strong></td>
              <td><strong>₹ <?= number_format($final_total, 2) ?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <h5 class="mb-3"><i class="fa-solid fa-ticket"></i> Apply Coupon</h5>
          <?php if (!empty($coupon_code) && $discount_amount > 0): ?>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($coupon_code) ?></strong>
                <small class="text-muted d-block"><?= number_format($discount_percent, 2) ?>% OFF applied</small>
              </div>
              <a href="remove_coupon.php" class="btn btn-outline-secondary">Remove</a>
            </div>
          <?php else: ?>
            <form method="post" action="apply_coupon.php" class="row g-2 mb-3">
              <div class="col-sm-8">
                <input type="text" id="couponInput" name="coupon_code" class="form-control" placeholder="Enter coupon code (e.g. SAVE10)" required>
              </div>
              <div class="col-sm-4">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
              </div>
            </form>
            <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                <div>
                    <span class="badge bg-warning text-dark me-2">EXCLUSIVE OFFER</span>
                    <span class="fw-bold">SAVE10</span>
                    <small class="text-muted ms-1">— Flat 10% OFF all menu items</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="applyQuickPromo('SAVE10')">Apply Deal</button>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <a href="clear_cart.php" class="btn btn-outline-danger">Clear Cart</a>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
      </div>
    <?php endif; ?>
  </div>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
  <script>
    function applyQuickPromo(code) {
        const inp = document.getElementById('couponInput');
        if (inp) {
            inp.value = code;
            inp.form.submit();
        }
    }
    <?php if (isset($_SESSION['coupon_success']) || (!empty($coupon_code) && $discount_amount > 0)): ?>
    window.addEventListener('DOMContentLoaded', () => {
        confetti({
            particleCount: 80,
            spread: 70,
            origin: { y: 0.6 }
        });
    });
    <?php endif; ?>
  </script>
</body>

</html>