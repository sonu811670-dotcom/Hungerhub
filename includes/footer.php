<!-- Footer Start -->
<footer class="bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="images/logo.png" alt="HungerHub Logo" width="40" height="40" class="me-2" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/logo.png';" />
                    <span class="fw-bold text-warning fs-4">HungerHub</span>
                </div>
                <p class="text-light">Delicious food delivered fast to your doorstep. Experience the best flavors from local restaurants with just a few clicks.</p>
                <div class="social-links">
                    <a href="#" class="text-warning me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-warning me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-warning me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-warning me-3"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-warning mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-light text-decoration-none hover-warning">Home</a></li>
                    <li><a href="menu.php" class="text-light text-decoration-none hover-warning">Menu</a></li>
                    <li><a href="about.php" class="text-light text-decoration-none hover-warning">About Us</a></li>
                    <li><a href="contact.php" class="text-light text-decoration-none hover-warning">Contact</a></li>
                    <li><a href="cart.php" class="text-light text-decoration-none hover-warning">Cart</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-warning mb-3">Contact Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-warning me-2"></i>
                        <span class="text-light">Virandavan nagar road no.1 , Sai Vihar Colony , Madhukam ,Ranchi, Jharkhand</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-warning me-2"></i>
                        <span class="text-light">+91 8603972526</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-warning me-2"></i>
                        <span class="text-light">info@hungerhub.com</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning me-2"></i>
                        <span class="text-light">24/7 Service</span>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-warning mb-3">Newsletter</h5>
                <p class="text-light">Subscribe to get updates on new dishes and special offers!</p>
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button class="btn btn-warning" type="button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">© 2026 HungerHub. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="#" class="text-light text-decoration-none hover-warning">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item">
                        <a href="#" class="text-light text-decoration-none hover-warning">Terms of Service</a>
                    </li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item">
                        <a href="#" class="text-light text-decoration-none hover-warning">Support</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-warning:hover {
        color: #ffc107 !important;
        transition: color 0.3s ease;
    }

    .social-links a {
        display: inline-block;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        text-align: center;
        line-height: 35px;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background-color: #ffc107;
        color: #000 !important;
        transform: translateY(-2px);
    }

    footer ul li {
        margin-bottom: 8px;
    }

    footer .input-group .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    /* HungerBot Floating AI Concierge Styles */
    #hungerbot-launcher {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9990;
        background: linear-gradient(135deg, #ff6b35 0%, #e85a24 100%);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 12px 20px;
        box-shadow: 0 8px 25px rgba(232, 90, 36, 0.4);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #hungerbot-launcher:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 30px rgba(232, 90, 36, 0.55);
        color: #ffffff;
    }

    #hungerbot-widget {
        position: fixed;
        bottom: 90px;
        right: 25px;
        width: 370px;
        max-width: calc(100vw - 30px);
        height: 520px;
        max-height: calc(100vh - 120px);
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.08);
        animation: botFadeUp 0.3s ease-out;
    }

    @keyframes botFadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bot-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .bot-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .bot-msg {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 0.88rem;
        line-height: 1.45;
        word-wrap: break-word;
    }

    .bot-msg-bot {
        background: #ffffff;
        color: #1e293b;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .bot-msg-user {
        background: #ff6b35;
        color: #ffffff;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .bot-dish-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bot-chip {
        font-size: 0.75rem;
        padding: 5px 10px;
        background: #e2e8f0;
        border-radius: 14px;
        cursor: pointer;
        display: inline-block;
        margin: 2px;
        transition: all 0.2s;
        border: none;
        color: #334155;
    }

    .bot-chip:hover {
        background: #ff6b35;
        color: #ffffff;
    }

    .bot-footer {
        padding: 12px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
    }
</style>

<!-- HungerBot Floating Launcher Button -->
<button id="hungerbot-launcher" type="button" aria-label="Open HungerBot AI Concierge">
    <i class="fas fa-robot"></i>
    <span>Ask Chef AI</span>
    <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.65rem;">ONLINE</span>
</button>

<!-- HungerBot Chat Modal -->
<div id="hungerbot-widget" role="dialog" aria-modal="true" aria-label="HungerBot Chat Window">
    <div class="bot-header">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="fas fa-utensils"></i>
            </div>
            <div>
                <div class="fw-bold fs-6">HungerBot <span class="text-warning">AI</span></div>
                <small class="text-white-50" style="font-size: 0.72rem;">Culinary Concierge & Support</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm text-white-50 p-1" id="hungerbot-close" aria-label="Close Chat">
            <i class="fas fa-times fs-5"></i>
        </button>
    </div>

    <div class="bot-body" id="hungerbot-messages">
        <div class="bot-msg bot-msg-bot">
            👋 <strong>Hi! I am HungerBot.</strong><br>
            Craving something delicious? Ask me for recommendations, budget deals, or questions about your order!
        </div>
        <div id="hungerbot-chips" class="mt-1">
            <button class="bot-chip" onclick="sendHungerbotQuery('🍕 Pizzas')">🍕 Pizzas</button>
            <button class="bot-chip" onclick="sendHungerbotQuery('🍛 Biryani Specials')">🍛 Biryani</button>
            <button class="bot-chip" onclick="sendHungerbotQuery('🥗 Veg under 200')">🥗 Veg under ₹200</button>
            <button class="bot-chip" onclick="sendHungerbotQuery('🎟️ Active Offers')">🎟️ Coupon SAVE10</button>
            <button class="bot-chip" onclick="sendHungerbotQuery('📍 Track Order')">📍 Track Order</button>
        </div>
    </div>

    <div class="bot-footer">
        <form id="hungerbot-form" onsubmit="handleHungerbotSubmit(event)" class="d-flex gap-2">
            <input type="text" id="hungerbot-input" class="form-control form-control-sm rounded-pill px-3" placeholder="Ask for pizza, veg dishes, offers..." autocomplete="off">
            <button type="submit" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; flex-shrink: 0;">
                <i class="fas fa-paper-plane fa-sm"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const hbLauncher = document.getElementById('hungerbot-launcher');
    const hbWidget = document.getElementById('hungerbot-widget');
    const hbClose = document.getElementById('hungerbot-close');
    const hbMessages = document.getElementById('hungerbot-messages');
    const hbInput = document.getElementById('hungerbot-input');

    if (hbLauncher && hbWidget) {
        hbLauncher.addEventListener('click', () => {
            const isVisible = hbWidget.style.display === 'flex';
            hbWidget.style.display = isVisible ? 'none' : 'flex';
            if (!isVisible && hbInput) {
                setTimeout(() => hbInput.focus(), 150);
            }
        });

        if (hbClose) {
            hbClose.addEventListener('click', () => {
                hbWidget.style.display = 'none';
            });
        }
    }

    function sendHungerbotQuery(text) {
        if (!text) return;
        appendHbMessage(text, 'user');

        // Loading bubble
        const loadId = 'hb-loading-' + Date.now();
        const loadDiv = document.createElement('div');
        loadDiv.id = loadId;
        loadDiv.className = 'bot-msg bot-msg-bot text-muted small';
        loadDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Chef is thinking...';
        hbMessages.appendChild(loadDiv);
        hbMessages.scrollTop = hbMessages.scrollHeight;

        fetch('chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            const loadEl = document.getElementById(loadId);
            if (loadEl) loadEl.remove();

            if (data.message) {
                // Convert simple markdown bold and newlines
                let formatted = data.message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
                appendHbMessage(formatted, 'bot', data.items, data.quick_replies);
            }
        })
        .catch(() => {
            const loadEl = document.getElementById(loadId);
            if (loadEl) loadEl.remove();
            appendHbMessage("Sorry, I encountered a temporary connection glitch. Please try again!", 'bot');
        });
    }

    function handleHungerbotSubmit(e) {
        e.preventDefault();
        const val = hbInput.value.trim();
        if (!val) return;
        hbInput.value = '';
        sendHungerbotQuery(val);
    }

    function appendHbMessage(htmlContent, sender, items = [], quickReplies = []) {
        const div = document.createElement('div');
        div.className = `bot-msg bot-msg-${sender}`;
        div.innerHTML = htmlContent;

        // Render dish cards if provided
        if (items && items.length > 0) {
            const listContainer = document.createElement('div');
            listContainer.className = 'mt-2 d-flex flex-column gap-2';
            items.forEach(dish => {
                const card = document.createElement('div');
                card.className = 'bot-dish-card';
                card.innerHTML = `
                    <img src="${dish.image}" width="40" height="40" class="rounded flex-shrink-0" style="object-fit:cover;" onerror="this.src='images/default_food.jpg'">
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-bold small text-dark text-truncate">${dish.name}</div>
                        <span class="text-success small fw-bold">${dish.formatted_price}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" onclick="botQuickAdd(event, ${dish.id})">+ Add</button>
                `;
                listContainer.appendChild(card);
            });
            div.appendChild(listContainer);
        }

        // Render dynamic quick replies
        if (quickReplies && quickReplies.length > 0) {
            const repliesContainer = document.createElement('div');
            repliesContainer.className = 'mt-2';
            quickReplies.forEach(qr => {
                const btn = document.createElement('button');
                btn.className = 'bot-chip';
                btn.textContent = qr;
                btn.onclick = () => sendHungerbotQuery(qr);
                repliesContainer.appendChild(btn);
            });
            div.appendChild(repliesContainer);
        }

        hbMessages.appendChild(div);
        hbMessages.scrollTop = hbMessages.scrollHeight;
    }

    function botQuickAdd(e, itemId) {
        e.stopPropagation();
        const btn = e.target;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch(`add_to_cart.php?id=${itemId}&quantity=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Added';
            const badge = document.getElementById('navCartBadge');
            if (badge && data.cart_count) {
                badge.textContent = data.cart_count;
                badge.style.display = 'inline-block';
            }
            setTimeout(() => {
                btn.innerHTML = '+ Add';
                btn.disabled = false;
            }, 1500);
        })
        .catch(() => {
            btn.innerHTML = '+ Add';
            btn.disabled = false;
        });
    }
</script>

<?php
$footer_page = basename($_SERVER['PHP_SELF'], '.php');
$footer_cart = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$footer_auth = isset($_SESSION['user_id']);
?>

<!-- Native-Style Mobile Bottom App Bar (Only visible on screens < 992px) -->
<div class="mobile-bottom-bar d-lg-none fixed-bottom bg-dark py-2 px-2 shadow-lg border-top border-secondary border-opacity-25" style="z-index: 9980; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background: rgba(26, 30, 35, 0.95) !important;">
    <div class="row g-0 text-center align-items-center">
        <div class="col-3">
            <a href="index.php" class="text-decoration-none d-block <?= $footer_page === 'index' ? 'text-warning fw-bold' : 'text-light text-opacity-75' ?>">
                <i class="fas fa-home fs-5 d-block mb-1"></i>
                <span style="font-size: 0.72rem; letter-spacing: 0.2px;">Home</span>
            </a>
        </div>
        <div class="col-3">
            <a href="menu.php" class="text-decoration-none d-block <?= $footer_page === 'menu' ? 'text-warning fw-bold' : 'text-light text-opacity-75' ?>">
                <i class="fas fa-utensils fs-5 d-block mb-1"></i>
                <span style="font-size: 0.72rem; letter-spacing: 0.2px;">Menu</span>
            </a>
        </div>
        <div class="col-3">
            <a href="cart.php" class="text-decoration-none d-block position-relative <?= $footer_page === 'cart' ? 'text-warning fw-bold' : 'text-light text-opacity-75' ?>">
                <div class="position-relative d-inline-block">
                    <i class="fas fa-shopping-bag fs-5 d-block mb-1"></i>
                    <span id="bottomNavCartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.5em; <?= $footer_cart > 0 ? '' : 'display:none;' ?>">
                        <?= $footer_cart ?>
                    </span>
                </div>
                <span class="d-block" style="font-size: 0.72rem; letter-spacing: 0.2px;">Cart</span>
            </a>
        </div>
        <div class="col-3">
            <a href="<?= $footer_auth ? 'user/profile.php' : 'user/login.php' ?>" class="text-decoration-none d-block <?= in_array($footer_page, ['login', 'profile', 'register', 'order_history']) ? 'text-warning fw-bold' : 'text-light text-opacity-75' ?>">
                <i class="fas fa-user-circle fs-5 d-block mb-1"></i>
                <span style="font-size: 0.72rem; letter-spacing: 0.2px;"><?= $footer_auth ? 'Account' : 'Login' ?></span>
            </a>
        </div>
    </div>
</div>

<style>
@media (max-width: 991.98px) {
    body {
        padding-bottom: 74px !important;
    }
    #hungerbot-launcher {
        bottom: 78px !important;
        right: 16px !important;
        padding: 9px 15px !important;
        font-size: 0.82rem !important;
    }
    #hungerbot-window {
        bottom: 132px !important;
        right: 12px !important;
        left: 12px !important;
        width: auto !important;
        max-width: 100% !important;
    }
}
</style>
<!-- Footer End -->