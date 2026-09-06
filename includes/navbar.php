<?php
// Get current page name for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Cart count calculation
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

// User authentication check
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<style>
    .login-btn{
        background: blue;
        color: #fff;
    }
    .login-btn:hover{
        background: #5989f9ff;
    }
</style>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="HungerHub Logo" width="38" height="38" class="me-2" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/logo.png';" />
            <span class="fw-bold text-warning">HungerHub</span>
        </a>

        <!-- Mobile Header Actions: Direct Cart shortcut + Hamburger button -->
        <div class="d-flex align-items-center d-lg-none gap-2">
            <a href="cart.php" class="btn btn-outline-warning btn-sm position-relative px-2 py-1 text-warning" title="Shopping Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.62rem; <?= $cart_count > 0 ? '' : 'display:none;' ?>">
                    <?= $cart_count ?>
                </span>
            </a>
            <button class="navbar-toggler p-2 border-warning" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'menu' ? 'active' : '' ?>" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'about' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'contact' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>

                <!-- Live Search Bar -->
                <li class="nav-item position-relative my-auto me-lg-3 my-2 my-lg-0">
                    <div class="input-group input-group-sm" style="min-width: 220px;">
                        <input type="text" id="navLiveSearchInput" class="form-control bg-secondary bg-opacity-25 text-light border-secondary" placeholder="Search dishes..." autocomplete="off">
                        <button class="btn btn-warning" type="button" id="navSearchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <div id="navSearchResults" class="dropdown-menu shadow-lg p-2 mt-1" style="display:none; position:absolute; left:0; width: 330px; max-height: 400px; overflow-y: auto; z-index: 1060; border-radius: 12px; background: #ffffff;"></div>
                </li>

                <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($user_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="user/profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="order_history.php">Order History</a></li>
                            <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn login-btn ms-2 px-3" href="user/login.php">Login</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link position-relative <?= $current_page === 'cart' ? 'active' : '' ?>" href="cart.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart
                        <span id="navCartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" style="<?= $cart_count > 0 ? '' : 'display:none;' ?>">
                            <?= $cart_count ?>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    (function() {
        const searchInput = document.getElementById('navLiveSearchInput');
        const resultsBox = document.getElementById('navSearchResults');
        const searchBtn = document.getElementById('navSearchBtn');
        let debounceTimer;

        if (searchInput && resultsBox) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const q = this.value.trim();
                if (q.length < 2) {
                    resultsBox.style.display = 'none';
                    resultsBox.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`api_search_menu.php?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success || !data.results || data.results.length === 0) {
                                resultsBox.innerHTML = '<div class="p-3 text-muted text-center small"><i class="fas fa-search me-1"></i>No dishes found for "' + q + '"</div>';
                                resultsBox.style.display = 'block';
                                return;
                            }

                            let html = '<div class="px-2 py-1 small text-muted fw-bold border-bottom mb-1">FOUND ' + data.results.length + ' DISHES</div>';
                            data.results.forEach(item => {
                                const vegBadge = item.main_category === 'Veg' ? '<span class="badge bg-success" style="font-size: 0.65rem;">VEG</span>' : '<span class="badge bg-danger" style="font-size: 0.65rem;">NON-VEG</span>';
                                html += `
                                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom rounded-2 hover-item" style="transition: background 0.2s;">
                                        <img src="${item.image}" width="44" height="44" class="rounded me-2 flex-shrink-0" style="object-fit:cover;" onerror="this.src='images/default_food.jpg'">
                                        <div class="flex-grow-1 text-truncate me-2">
                                            <a href="menu.php?search=${encodeURIComponent(item.name)}" class="fw-semibold text-dark text-decoration-none d-block text-truncate small">${item.name}</a>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-success fw-bold small">${item.formatted_price}</span>
                                                ${vegBadge}
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" onclick="navQuickAddToCart(event, ${item.id})">+ Add</button>
                                    </div>
                                `;
                            });
                            html += `<div class="text-center pt-2"><a href="menu.php?search=${encodeURIComponent(q)}" class="small text-primary text-decoration-none fw-semibold">View all matching items &rarr;</a></div>`;
                            resultsBox.innerHTML = html;
                            resultsBox.style.display = 'block';
                        })
                        .catch(() => {
                            resultsBox.style.display = 'none';
                        });
                }, 250);
            });

            // Enter key navigates to menu search
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.value.trim()) {
                        window.location.href = 'menu.php?search=' + encodeURIComponent(this.value.trim());
                    }
                }
            });

            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    if (searchInput.value.trim()) {
                        window.location.href = 'menu.php?search=' + encodeURIComponent(searchInput.value.trim());
                    }
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }
    })();

    // Quick Add to Cart via AJAX from Live Search
    function navQuickAddToCart(e, itemId) {
        e.stopPropagation();
        const btn = e.target;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch(`add_to_cart.php?id=${itemId}&quantity=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            const badge = document.getElementById('navCartBadge');
            if (badge && data.cart_count) {
                badge.textContent = data.cart_count;
                badge.style.display = 'inline-block';
                badge.classList.add('animate__animated', 'animate__pulse');
            }
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 1200);
        })
        .catch(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>
<!-- Navbar End -->