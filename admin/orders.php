<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit();
}
require '../db.php';

// Fetch profile image
$stmt = $conn->prepare("SELECT profile_pic FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result_admin = $stmt->get_result();
$admin = $result_admin->fetch_assoc();
$profile_img = $admin['profile_pic'] ?? 'default.png';

// Handle status update
if (isset($_POST['update_status'])) {
  $order_id = intval($_POST['order_id']);
  $new_status = $_POST['status'];
  $admin_notes = htmlspecialchars(trim($_POST['admin_notes'] ?? ''));
  $rider_name = htmlspecialchars(trim($_POST['rider_name'] ?? ''));
  $rider_phone = htmlspecialchars(trim($_POST['rider_phone'] ?? ''));
  $rider_vehicle = htmlspecialchars(trim($_POST['rider_vehicle'] ?? ''));

  $stmt = $conn->prepare("
    UPDATE orders 
    SET status = ?, 
        admin_notes = ?, 
        rider_name = COALESCE(NULLIF(?, ''), rider_name),
        rider_phone = COALESCE(NULLIF(?, ''), rider_phone),
        rider_vehicle = COALESCE(NULLIF(?, ''), rider_vehicle)
    WHERE id = ?
  ");
  $stmt->bind_param("sssssi", $new_status, $admin_notes, $rider_name, $rider_phone, $rider_vehicle, $order_id);

  if ($stmt->execute()) {
    $_SESSION['success'] = "Order status updated successfully!";
  } else {
    $_SESSION['error'] = "Failed to update order status.";
  }
  header("Location: orders.php");
  exit();
}

// Handle real bank payment verification by restaurant manager
if (isset($_POST['verify_payment'])) {
  $order_id = intval($_POST['order_id']);
  $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', status = 'Confirmed', payment_date = NOW() WHERE id = ?");
  $stmt->bind_param("i", $order_id);

  if ($stmt->execute()) {
    // Also record in payments table
    $stmt_pay = $conn->prepare("UPDATE payments SET status = 'Captured' WHERE order_id = ?");
    $stmt_pay->bind_param("i", $order_id);
    $stmt_pay->execute();
    $_SESSION['success'] = "Payment verified successfully! Order #$order_id is now Confirmed.";
  } else {
    $_SESSION['error'] = "Failed to verify payment.";
  }
  header("Location: orders.php");
  exit();
}

$max_res = $conn->query("SELECT MAX(id) as max_id FROM orders");
$current_max_id = (int)($max_res ? ($max_res->fetch_assoc()['max_id'] ?? 0) : 0);

$result = $conn->query("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders Management - HungerHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .sidebar .nav-link {
      color: rgba(255, 255, 255, 0.8);
      border-radius: 8px;
      margin: 2px 0;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      color: white;
      background: rgba(255, 255, 255, 0.1);
    }

    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .order-status {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-confirmed {
      background: #d1ecf1;
      color: #0c5460;
    }

    .status-preparing {
      background: #cce5ff;
      color: #004085;
    }

    .status-ready {
      background: #d4edda;
      color: #155724;
    }

    .status-delivered {
      background: #d4edda;
      color: #155724;
    }

    .status-cancelled {
      background: #f8d7da;
      color: #721c24;
    }

    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-3">
        <div class="text-center mb-4">
          <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" class="profile-img mb-2" alt="Admin Image" />
          <h6 class="text-white"><?php echo $_SESSION['admin_name']; ?></h6>
        </div>

        <h4 class="text-white mb-4">
          <i class="fas fa-utensils me-2"></i>HungerHub Admin
        </h4>

        <nav class="nav flex-column">
          <a class="nav-link" href="admin_dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
          </a>
          <a class="nav-link active" href="orders.php">
            <i class="fas fa-shopping-bag me-2"></i>Orders
          </a>
          <a class="nav-link" href="menu_items.php">
            <i class="fas fa-utensils me-2"></i>Menu Items
          </a>
          <a class="nav-link" href="payments.php">
            <i class="fas fa-credit-card me-2"></i>Payments
          </a>
          <a class="nav-link" href="coupons.php">
            <i class="fas fa-ticket-alt me-2"></i>Coupons
          </a>
          <a class="nav-link" href="customers.php">
            <i class="fas fa-users me-2"></i>Customers
          </a>
          <a class="nav-link" href="messages.php">
            <i class="fas fa-envelope me-2"></i>Messages
          </a>
          <hr class="text-white-50">
          <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a>
        </nav>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="mb-1"><i class="fas fa-shopping-bag me-2"></i>Kitchen Orders & Dispatch</h2>
            <small class="text-muted"><i class="fas fa-satellite-dish text-success me-1"></i>Live Kitchen Display System (KDS) & Real-time Telemetry</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button id="btnSoundToggle" type="button" class="btn btn-sm btn-outline-primary" onclick="toggleKitchenSound()">
              <i class="fas fa-bell me-1"></i>Chime: <span id="soundStatusText">ON</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
              <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
          </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success'];
                                                    unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error'];
                                                          unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <!-- Order Filter Tabs -->
        <div class="d-flex flex-wrap gap-2 mb-3">
          <button type="button" class="btn btn-sm btn-dark filter-btn active" data-filter="all">All Orders</button>
          <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-filter="pending">Pending</button>
          <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="confirmed">Confirmed / Cooking</button>
          <button type="button" class="btn btn-sm btn-outline-info filter-btn" data-filter="on the way">Out for Delivery</button>
          <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-filter="needs-utr">Needs UTR Verification</button>
          <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-filter="delivered">Delivered</button>
        </div>

        <?php if ($result->num_rows > 0): ?>
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Orders</h5>
              <span class="badge bg-primary" id="totalOrdersCountBadge"><?= $result->num_rows ?> Total Orders</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0" id="ordersTable">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Customer</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Ordered On</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;
                    while ($row = $result->fetch_assoc()): 
                      $row_status = strtolower($row['status'] ?? 'pending');
                      $needs_utr = (!empty($row['payment_id']) && $row['payment_status'] !== 'Paid') ? '1' : '0';
                    ?>
                      <tr class="order-table-row" data-status="<?= $row_status ?>" data-needs-utr="<?= $needs_utr ?>">
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td>
                          <div>
                            <strong><?= htmlspecialchars($row['customer_name']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($row['user_email'] ?? 'N/A') ?></small>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><small><?= nl2br(htmlspecialchars($row['address'])) ?></small></td>
                        <td><small><?= htmlspecialchars($row['items']) ?></small></td>
                        <td>
                          <strong>₹<?= number_format($row['total'], 2) ?></strong>
                          <div class="mt-1">
                            <?php if ($row['payment_status'] === 'Paid'): ?>
                              <span class="badge bg-success-subtle text-success border border-success-subtle py-1">
                                <i class="fas fa-check-circle me-1"></i>Paid (<?= htmlspecialchars($row['payment_method']) ?>)
                              </span>
                            <?php elseif ($row['payment_method'] === 'UPI'): ?>
                              <div class="d-flex flex-column gap-1">
                                <span class="badge bg-warning text-dark text-wrap" style="font-size: 0.75rem;">
                                  <i class="fas fa-clock me-1"></i>UTR: <?= htmlspecialchars($row['payment_id'] ?? 'Not submitted') ?>
                                </span>
                                <?php if (!empty($row['payment_id'])): ?>
                                  <form method="post" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="verify_payment" class="btn btn-sm btn-success py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                                      <i class="fas fa-check-double me-1"></i>Verify UTR
                                    </button>
                                  </form>
                                <?php endif; ?>
                              </div>
                            <?php else: ?>
                              <span class="badge bg-secondary"><?= htmlspecialchars($row['payment_method']) ?></span>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <span class="order-status status-<?= strtolower(str_replace(' ', '', $row['status'] ?? 'pending')) ?>">
                            <?= htmlspecialchars($row['status'] ?? 'Pending') ?>
                          </span>
                          <?php if (!empty($row['rider_name'])): ?>
                            <small class="d-block text-muted mt-1"><i class="fas fa-motorcycle me-1 text-primary"></i><?= htmlspecialchars($row['rider_name']) ?></small>
                          <?php endif; ?>
                        </td>
                        <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?= $row['id'] ?>" title="Update Status">
                              <i class="fas fa-edit me-1"></i>Update
                            </button>
                            <a href="../track_order.php?id=<?= $row['id'] ?>" class="btn btn-info text-white" target="_blank" title="View Customer Tracking View">
                              <i class="fas fa-satellite-dish"></i>
                            </a>
                            <a href="../invoice.php?id=<?= $row['id'] ?>" class="btn btn-secondary" target="_blank" title="Print Tax Invoice">
                              <i class="fas fa-file-invoice"></i>
                            </a>
                          </div>
                        </td>
                      </tr>

                      <!-- Status Update Modal -->
                      <div class="modal fade" id="statusModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>Update Order #<?= $row['id'] ?>
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                              <div class="modal-body">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                                <div class="mb-3">
                                  <label class="form-label fw-semibold">Order Lifecycle Status</label>
                                  <select name="status" class="form-select" required>
                                    <option value="Pending" <?= ($row['status'] ?? 'Pending') == 'Pending' ? 'selected' : '' ?>>Pending (Order Received)</option>
                                    <option value="Confirmed" <?= ($row['status'] ?? '') == 'Confirmed' ? 'selected' : '' ?>>Confirmed (Accepted by Kitchen)</option>
                                    <option value="Preparing" <?= ($row['status'] ?? '') == 'Preparing' ? 'selected' : '' ?>>Preparing (Cooking in Kitchen)</option>
                                    <option value="Ready" <?= ($row['status'] ?? '') == 'Ready' ? 'selected' : '' ?>>Ready (Packaged for Pickup)</option>
                                    <option value="Out for Delivery" <?= ($row['status'] ?? '') == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery (Dispatched)</option>
                                    <option value="Delivered" <?= ($row['status'] ?? '') == 'Delivered' ? 'selected' : '' ?>>Delivered (Completed)</option>
                                    <option value="Cancelled" <?= ($row['status'] ?? '') == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                  </select>
                                </div>

                                <div class="card bg-light p-3 border-0 rounded-3 mb-3">
                                  <label class="form-label fw-semibold small mb-2"><i class="fas fa-motorcycle text-primary me-1"></i>Delivery Courier Assignment (For 'Out for Delivery')</label>
                                  <div class="mb-2">
                                    <input type="text" name="rider_name" class="form-control form-control-sm" placeholder="Driver Name (e.g. Amit Sharma)" value="<?= htmlspecialchars($row['rider_name'] ?? '') ?>">
                                  </div>
                                  <div class="row g-2">
                                    <div class="col-6">
                                      <input type="text" name="rider_phone" class="form-control form-control-sm" placeholder="Driver Phone (+91 ...)" value="<?= htmlspecialchars($row['rider_phone'] ?? '') ?>">
                                    </div>
                                    <div class="col-6">
                                      <input type="text" name="rider_vehicle" class="form-control form-control-sm" placeholder="Vehicle Info (Activa / Bike)" value="<?= htmlspecialchars($row['rider_vehicle'] ?? '') ?>">
                                    </div>
                                  </div>
                                </div>

                                <div class="mb-3">
                                  <label class="form-label">Admin Notes</label>
                                  <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes about this order..."><?= htmlspecialchars($row['admin_notes'] ?? '') ?></textarea>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                  <i class="fas fa-save me-1"></i>Update Status
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">No orders found</h5>
              <p class="text-muted">Orders from customers will appear here.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Toast container for live order alerts -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="newOrderToast" class="toast align-items-center text-bg-warning border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-bold fs-6">
          <i class="fas fa-bell fa-shake me-2"></i><span id="toastAlertText">🔔 New Order Placed! Reloading kitchen feed...</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentMaxId = <?= $current_max_id ?>;
    let isSoundEnabled = true;

    // Filter Buttons Functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const orderRows = document.querySelectorAll('.order-table-row');

    filterBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        filterBtns.forEach(b => {
          b.classList.remove('active', 'btn-dark');
          b.classList.add('btn-outline-secondary');
        });
        this.classList.add('active', 'btn-dark');
        this.classList.remove('btn-outline-secondary');

        const filter = this.getAttribute('data-filter');
        orderRows.forEach(row => {
          const status = row.getAttribute('data-status');
          const needsUtr = row.getAttribute('data-needs-utr');

          if (filter === 'all') {
            row.style.display = '';
          } else if (filter === 'needs-utr') {
            row.style.display = (needsUtr === '1') ? '' : 'none';
          } else if (filter === 'confirmed') {
            row.style.display = (status === 'confirmed' || status === 'processing' || status === 'cooking') ? '' : 'none';
          } else {
            row.style.display = (status === filter) ? '' : 'none';
          }
        });
      });
    });

    // Web Audio Synthesizer for Kitchen Chime (Zero External Dependencies)
    function playKitchenChime() {
      if (!isSoundEnabled) return;
      try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();

        // Pleasant melodic triad (A5, C#6, E6)
        const notes = [880, 1108.73, 1318.51];
        notes.forEach((freq, idx) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          const startTime = ctx.currentTime + (idx * 0.12);

          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, startTime);

          gain.gain.setValueAtTime(0.2, startTime);
          gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.6);

          osc.connect(gain);
          gain.connect(ctx.destination);

          osc.start(startTime);
          osc.stop(startTime + 0.65);
        });
      } catch (err) {
        console.warn("Kitchen chime audio playback prevented by browser autoplay policy:", err);
      }
    }

    function toggleKitchenSound() {
      isSoundEnabled = !isSoundEnabled;
      document.getElementById('soundStatusText').textContent = isSoundEnabled ? 'ON' : 'MUTED';
      const btn = document.getElementById('btnSoundToggle');
      if (isSoundEnabled) {
        btn.className = 'btn btn-sm btn-outline-primary';
        playKitchenChime(); // Sample ding
      } else {
        btn.className = 'btn btn-sm btn-outline-secondary';
      }
    }

    // Real-Time Kitchen Polling (every 7 seconds)
    function pollKitchenOrders() {
      fetch('api_order_poll.php')
        .then(res => res.json())
        .then(data => {
          if (!data.success) return;

          if (data.latest_id > currentMaxId) {
            currentMaxId = data.latest_id;
            playKitchenChime();

            const toastEl = document.getElementById('newOrderToast');
            const alertText = document.getElementById('toastAlertText');
            if (alertText) {
              alertText.textContent = `🔔 New Order #${data.latest_id} Received! Kitchen feed refreshing...`;
            }
            const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
            toast.show();

            // Auto-refresh orders table to load the new row
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }
        })
        .catch(err => console.error("Kitchen polling error:", err));
    }

    setInterval(pollKitchenOrders, 7000);
  </script>
</body>

</html>