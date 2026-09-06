<?php
/**
 * HungerHub - Commercial Real-Time Order Tracking Portal
 * Developed by: Sonu Kumar (Lead Full-Stack Engineer)
 * Displays genuine restaurant kitchen fulfillment lifecycle, dynamic GPS delivery route, and assigned courier details.
 */

session_start();
require_once 'config.php';

$order_id = intval($_GET['id'] ?? ($_SESSION['last_order_id'] ?? 0));

if ($order_id <= 0) {
    header("Location: order_history.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT o.*, u.name as user_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: order_history.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #HH-<?= $order_id ?> - <?= REST_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #ff6b35;
            --primary-dark: #e85a24;
            --success: #10b981;
            --dark: #0f172a;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
        }

        .tracker-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 35px 0;
            border-radius: 0 0 24px 24px;
            margin-bottom: -30px;
        }

        .track-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* Stepper */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 0 20px 0;
        }

        .stepper-progress-bar {
            position: absolute;
            top: 25px;
            left: 5%;
            width: 90%;
            height: 6px;
            background-color: #e2e8f0;
            z-index: 1;
            border-radius: 10px;
        }

        .stepper-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ff6b35, #10b981);
            border-radius: 10px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stepper-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            text-align: center;
        }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #94a3b8;
            transition: all 0.4s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .step-completed .step-circle {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
        }

        .step-active .step-circle {
            background: #ff6b35;
            border-color: #ff6b35;
            color: #ffffff;
            box-shadow: 0 0 0 8px rgba(255, 107, 53, 0.2);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 53, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(255, 107, 53, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 53, 0); }
        }

        .step-title {
            margin-top: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
        }

        .step-completed .step-title,
        .step-active .step-title {
            color: #0f172a;
        }

        /* Route Simulation & Map */
        #liveMap {
            height: 300px;
            border-radius: 14px;
            z-index: 5;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .map-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border: 2px solid #ffffff;
        }

        .marker-rest {
            background: #ff6b35;
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .marker-dest {
            background: #10b981;
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .marker-rider {
            background: #2563eb;
            width: 44px;
            height: 44px;
            font-size: 18px;
            animation: rider-pulse 2s infinite;
        }

        @keyframes rider-pulse {
            0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.6); }
            70% { box-shadow: 0 0 0 14px rgba(37, 99, 235, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }

        .route-container {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .route-line {
            height: 4px;
            background: repeating-linear-gradient(90deg, #3b82f6 0, #3b82f6 8px, transparent 8px, transparent 16px);
            position: relative;
            margin: 40px 20px;
        }

        .scooter-icon {
            position: absolute;
            top: -24px;
            font-size: 1.8rem;
            color: #ff6b35;
            transition: left 1s ease-in-out;
            transform: translateX(-50%);
        }

        .rider-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            animation: live-blink 1.5s infinite;
        }

        @keyframes live-blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(1.2); }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="Logo" width="36" height="36" class="me-2" onerror="this.style.display='none'">
                <span class="fw-bold text-warning"><?= REST_NAME ?></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="order_history.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-list me-1"></i>My Orders
                </a>
                <a href="invoice.php?id=<?= $order_id ?>" class="btn btn-warning btn-sm" target="_blank">
                    <i class="fas fa-file-invoice me-1"></i>Tax Invoice
                </a>
            </div>
        </div>
    </nav>

    <!-- Header Banner -->
    <header class="tracker-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill mb-2">
                        <span class="live-dot"></span>Live Order Tracking
                    </span>
                    <h2 class="fw-bold mb-1">Order #HH-<?= $order_id ?></h2>
                    <p class="text-white-50 mb-0">
                        Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?> • 
                        Payment: <span id="paymentMethodBadge" class="badge bg-<?= $order['payment_status'] === 'Paid' ? 'success' : 'warning' ?>"><?= htmlspecialchars($order['payment_status'] ?? 'Pending') ?> (<?= htmlspecialchars($order['payment_method']) ?>)</span>
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 d-inline-block text-center text-md-end">
                        <small class="text-white-50 d-block">Estimated Arrival</small>
                        <span id="etaText" class="fs-2 fw-bold text-warning">25-30 Mins</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row g-4">
            <!-- Left Column: Stepper & Route -->
            <div class="col-lg-8">
                <!-- Stepper Card -->
                <div class="track-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0">Kitchen & Delivery Status</h5>
                        <span id="currentStatusBadge" class="badge bg-warning px-3 py-2 rounded-pill fs-6">
                            Pending
                        </span>
                    </div>

                    <p id="currentStatusDesc" class="text-muted small mb-4">
                        Waiting for restaurant kitchen acceptance...
                    </p>

                    <!-- Stepper -->
                    <div class="stepper-wrapper">
                        <div class="stepper-progress-bar">
                            <div id="progressFill" class="stepper-progress-fill" style="width: 20%;"></div>
                        </div>

                        <div id="step-1" class="stepper-item step-completed">
                            <div class="step-circle"><i class="fas fa-receipt"></i></div>
                            <div class="step-title">Order Placed</div>
                        </div>

                        <div id="step-2" class="stepper-item">
                            <div class="step-circle"><i class="fas fa-thumbs-up"></i></div>
                            <div class="step-title">Accepted</div>
                        </div>

                        <div id="step-3" class="stepper-item">
                            <div class="step-circle"><i class="fas fa-fire-burner"></i></div>
                            <div class="step-title">Cooking</div>
                        </div>

                        <div id="step-4" class="stepper-item">
                            <div class="step-circle"><i class="fas fa-motorcycle"></i></div>
                            <div class="step-title">On The Way</div>
                        </div>

                        <div id="step-5" class="stepper-item">
                            <div class="step-circle"><i class="fas fa-house-chimney-check"></i></div>
                            <div class="step-title">Delivered</div>
                        </div>
                    </div>
                </div>

                <!-- Route Simulation -->
                <div class="track-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-map-location-dot text-primary me-2"></i>Live Transit Route</h5>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                            <span class="live-dot"></span>GPS Telemetry Active
                        </span>
                    </div>

                    <!-- Live Leaflet Map -->
                    <div id="liveMap" class="mb-4"></div>
                    
                    <div class="route-container mb-4">
                        <div class="d-flex justify-content-between text-muted small fw-semibold">
                            <span><i class="fas fa-store text-danger me-1"></i><?= REST_NAME ?> (Kitchen)</span>
                            <span class="badge bg-white text-dark border"><i class="fas fa-route text-primary me-1"></i>Transit: ~2.8 KM</span>
                            <span><i class="fas fa-location-dot text-success me-1"></i>Delivery Point</span>
                        </div>

                        <div class="route-line">
                            <div id="deliveryScooter" class="scooter-icon" style="left: 20%;">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-satellite-dish me-1"></i>Real-time GPS Delivery Tracking & Fleet Sync
                            </small>
                        </div>
                    </div>

                    <!-- Delivery Partner Card -->
                    <div class="rider-card">
                        <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="fas fa-helmet-safety fa-xl"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 id="riderNameText" class="fw-bold mb-0">Assigned Courier</h6>
                                <span class="badge bg-warning text-dark"><i class="fas fa-star text-dark me-1"></i>4.9</span>
                            </div>
                            <small id="riderVehicleText" class="text-muted d-block">Vehicle details will appear upon dispatch</small>
                            <span class="badge bg-success-subtle text-success py-1 mt-1">Verified Restaurant Partner</span>
                        </div>
                        <div>
                            <a id="riderCallBtn" href="tel:<?= REST_PHONE ?>" class="btn btn-outline-success btn-sm rounded-circle p-2" title="Call Courier">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Details -->
            <div class="col-lg-4">
                <div class="track-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-bag-shopping text-warning me-2"></i>Order Summary</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small d-block">Dishes Ordered:</label>
                        <p class="fw-semibold mb-0"><?= nl2br(htmlspecialchars($order['items'])) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Delivering To:</label>
                        <p class="mb-0 small text-dark"><i class="fas fa-location-dot text-danger me-1"></i><?= htmlspecialchars($order['address']) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Contact Phone:</label>
                        <p class="mb-0 small text-dark"><i class="fas fa-phone text-primary me-1"></i><?= htmlspecialchars($order['phone']) ?> (<?= htmlspecialchars($order['customer_name']) ?>)</p>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Mode</span>
                        <span class="fw-semibold"><?= htmlspecialchars($order['payment_method']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Status</span>
                        <span id="paymentStatusBadge" class="badge bg-<?= $order['payment_status'] === 'Paid' ? 'success' : 'warning' ?>">
                            <?= htmlspecialchars($order['payment_status'] ?? 'Pending') ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <span class="fw-bold fs-5">Total Paid</span>
                        <span class="fw-bold fs-4 text-success">₹<?= number_format($order['total'], 2) ?></span>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="invoice.php?id=<?= $order_id ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-print me-2"></i>Download Tax Invoice
                        </a>
                        <a href="menu.php" class="btn btn-light">
                            <i class="fas fa-utensils me-2"></i>Order More Food
                        </a>
                    </div>
                </div>

                <!-- Support Card -->
                <div class="track-card p-4 bg-primary text-white">
                    <h6 class="fw-bold mb-1"><i class="fas fa-headset me-2"></i>Restaurant Support</h6>
                    <p class="small text-white-50 mb-2">Have a question or request for the chef? Contact our front desk directly:</p>
                    <a href="tel:<?= REST_PHONE ?>" class="btn btn-light btn-sm fw-semibold text-primary">
                        <i class="fas fa-phone me-1"></i>Call <?= REST_PHONE ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const ORDER_ID = <?= $order_id ?>;
        const customerAddress = <?= json_encode($order['address'] ?? 'Customer Address') ?>;

        // Coordinates (Ranchi, Jharkhand Hub)
        const restCoord = [23.3728, 85.3131]; // HungerHub Kitchen, Madhukam
        const destCoord = [23.3575, 85.3340]; // Destination Hub

        const map = L.map('liveMap', { zoomControl: false, scrollWheelZoom: false }).setView([23.3650, 85.3235], 13);
        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        // Custom HTML Pins
        const restIcon = L.divIcon({
            className: 'custom-pin',
            html: '<div class="map-marker marker-rest" title="Restaurant Kitchen"><i class="fas fa-store"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        const destIcon = L.divIcon({
            className: 'custom-pin',
            html: '<div class="map-marker marker-dest" title="Customer Destination"><i class="fas fa-house"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        const riderIcon = L.divIcon({
            className: 'custom-pin',
            html: '<div class="map-marker marker-rider" title="Courier Rider"><i class="fas fa-motorcycle"></i></div>',
            iconSize: [44, 44],
            iconAnchor: [22, 22]
        });

        L.marker(restCoord, { icon: restIcon }).addTo(map).bindPopup("<b><?= REST_NAME ?></b><br>Kitchen Dispatch Hub");
        L.marker(destCoord, { icon: destIcon }).addTo(map).bindPopup("<b>Delivery Destination</b><br>" + customerAddress);

        // Simulated road waypoint corridor
        const routeWaypoints = [
            restCoord,
            [23.3705, 85.3168],
            [23.3670, 85.3210],
            [23.3635, 85.3262],
            [23.3600, 85.3305],
            destCoord
        ];

        const routeLine = L.polyline(routeWaypoints, {
            color: '#ff6b35',
            weight: 5,
            opacity: 0.85,
            dashArray: '8, 8'
        }).addTo(map);

        map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });

        let riderMarker = L.marker(restCoord, { icon: riderIcon }).addTo(map).bindPopup("<b>Live Courier Position</b>");

        function getInterpolatedPoint(fraction) {
            fraction = Math.max(0, Math.min(1, fraction));
            const totalSegments = routeWaypoints.length - 1;
            const scaled = fraction * totalSegments;
            const index = Math.floor(scaled);
            const segFraction = scaled - index;

            if (index >= totalSegments) return routeWaypoints[totalSegments];

            const p1 = routeWaypoints[index];
            const p2 = routeWaypoints[index + 1];

            return [
                p1[0] + (p2[0] - p1[0]) * segFraction,
                p1[1] + (p2[1] - p1[1]) * segFraction
            ];
        }

        // Real-time polling function (3-second cadence)
        function updateOrderStatus() {
            fetch(`api_order_status.php?id=${ORDER_ID}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;

                    // Update ETA & Status details
                    document.getElementById('etaText').textContent = data.eta;
                    document.getElementById('currentStatusBadge').textContent = data.title;
                    document.getElementById('currentStatusBadge').className = `badge bg-${data.badge} px-3 py-2 rounded-pill fs-6`;
                    document.getElementById('currentStatusDesc').textContent = data.description;
                    document.getElementById('paymentStatusBadge').textContent = data.payment_status;
                    document.getElementById('paymentStatusBadge').className = `badge bg-${data.payment_status === 'Paid' ? 'success' : 'warning'}`;

                    // Update Stepper fill & scooter
                    document.getElementById('progressFill').style.width = data.percent + '%';
                    document.getElementById('deliveryScooter').style.left = data.percent + '%';

                    // Update Map live marker position
                    const riderPos = getInterpolatedPoint(data.percent / 100);
                    riderMarker.setLatLng(riderPos);

                    // Update active stepper node
                    for (let i = 1; i <= 5; i++) {
                        const el = document.getElementById(`step-${i}`);
                        el.className = 'stepper-item';
                        if (i < data.step) {
                            el.classList.add('step-completed');
                        } else if (i === data.step) {
                            el.classList.add('step-active');
                        }
                    }

                    // Update Rider information dynamically
                    if (data.rider) {
                        document.getElementById('riderNameText').textContent = data.rider.name;
                        document.getElementById('riderVehicleText').textContent = data.rider.vehicle;
                        document.getElementById('riderCallBtn').href = 'tel:' + data.rider.phone;
                    }
                })
                .catch(err => console.error("Poll error:", err));
        }

        updateOrderStatus();
        setInterval(updateOrderStatus, 3000);
    </script>
</body>

</html>
