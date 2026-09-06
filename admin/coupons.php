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

function normalize_code($code)
{
    $code = trim($code);
    $code = preg_replace('/\s+/', '', $code);
    return strtoupper($code);
}

// Create coupon
if (isset($_POST['create_coupon'])) {
    $code = normalize_code($_POST['code'] ?? '');
    $discount_percent = (float)($_POST['discount_percent'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;
    $expires_at = trim($_POST['expires_at'] ?? '');
    $expires_at = $expires_at === '' ? null : $expires_at;

    if ($code === '' || $discount_percent <= 0 || $discount_percent > 100) {
        $_SESSION['error'] = "Please enter a valid coupon code and discount percentage (1-100).";
        header("Location: coupons.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount_percent, active, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $code, $discount_percent, $active, $expires_at);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Coupon created successfully.";
    } else {
        $_SESSION['error'] = "Failed to create coupon. Make sure the code is unique.";
    }
    $stmt->close();

    header("Location: coupons.php");
    exit();
}

// Update coupon
if (isset($_POST['update_coupon'])) {
    $coupon_id = intval($_POST['coupon_id'] ?? 0);
    $discount_percent = (float)($_POST['discount_percent'] ?? 0);
    $active = (int)($_POST['active'] ?? 0) === 1 ? 1 : 0;
    $expires_at = trim($_POST['expires_at'] ?? '');
    $expires_at = $expires_at === '' ? null : $expires_at;

    if ($coupon_id <= 0 || $discount_percent <= 0 || $discount_percent > 100) {
        $_SESSION['error'] = "Please enter a valid discount percentage (1-100).";
        header("Location: coupons.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE coupons SET discount_percent = ?, active = ?, expires_at = ? WHERE id = ?");
    $stmt->bind_param("disi", $discount_percent, $active, $expires_at, $coupon_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Coupon updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update coupon.";
    }
    $stmt->close();

    header("Location: coupons.php");
    exit();
}

// Delete coupon
if (isset($_POST['delete_coupon'])) {
    $coupon_id = intval($_POST['coupon_id'] ?? 0);

    if ($coupon_id > 0) {
        $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->bind_param("i", $coupon_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Coupon deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete coupon.";
        }
        $stmt->close();
    }

    header("Location: coupons.php");
    exit();
}

$result = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons - HungerHub Admin</title>
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
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-bag me-2"></i>Orders
                    </a>
                    <a class="nav-link" href="menu_items.php">
                        <i class="fas fa-utensils me-2"></i>Menu Items
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="fas fa-credit-card me-2"></i>Payments
                    </a>
                    <a class="nav-link active" href="coupons.php">
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
                    <h2><i class="fas fa-ticket-alt me-2"></i>Coupons</h2>
                    <div class="text-muted">
                        <i class="fas fa-calendar me-1"></i><?= date('M d, Y') ?>
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

                <!-- Create Coupon -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create Coupon</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Coupon Code</label>
                                <input type="text" name="code" class="form-control" placeholder="SAVE10" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" name="discount_percent" class="form-control" min="1" max="100" step="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Expires At (optional)</label>
                                <input type="datetime-local" name="expires_at" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="active" id="active" checked>
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                                <button type="submit" name="create_coupon" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save
                                </button>
                            </div>
                        </form>
                        <small class="text-muted d-block mt-2">Tip: Codes are stored in uppercase. Users can enter lowercase too.</small>
                    </div>
                </div>

                <!-- Coupons List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Coupons</h5>
                        <span class="badge bg-primary"><?= $result ? $result->num_rows : 0 ?> Coupons</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Discount</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                        <th style="width: 340px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['code']) ?></strong></td>
                                                <td><?= number_format((float)$row['discount_percent'], 2) ?>%</td>
                                                <td>
                                                    <?php if (!empty($row['expires_at'])): ?>
                                                        <small><?= date('M j, Y g:i A', strtotime($row['expires_at'])) ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">No expiry</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ((int)$row['active'] === 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="post" class="row g-2">
                                                        <input type="hidden" name="coupon_id" value="<?= (int)$row['id'] ?>">
                                                        <div class="col-4">
                                                            <input type="number" name="discount_percent" class="form-control form-control-sm" min="1" max="100" step="0.01"
                                                                value="<?= htmlspecialchars($row['discount_percent']) ?>" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="datetime-local" name="expires_at" class="form-control form-control-sm"
                                                                value="<?= !empty($row['expires_at']) ? date('Y-m-d\TH:i', strtotime($row['expires_at'])) : '' ?>">
                                                        </div>
                                                        <div class="col-4">
                                                            <select name="active" class="form-select form-select-sm">
                                                                <option value="1" <?= (int)$row['active'] === 1 ? 'selected' : '' ?>>Active</option>
                                                                <option value="0" <?= (int)$row['active'] === 0 ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 d-flex gap-2">
                                                            <button type="submit" name="update_coupon" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit me-1"></i>Update
                                                            </button>
                                                            <button type="submit" name="delete_coupon" class="btn btn-sm btn-danger" onclick="return confirm('Delete this coupon?');">
                                                                <i class="fas fa-trash me-1"></i>Delete
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-4 text-muted">No coupons found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>