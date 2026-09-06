# HungerHub: Resume Bullets & Viva Presentation Guide
**Architected & Developed by Sonu Kumar**

Use this guide for updating your Resume, LinkedIn, GitHub portfolio, and acing your Final Year Project Viva Voce examination with your professor or interviewer tomorrow.

---

## 📄 Ready-to-Use Resume Project Section

### Option 1: Bullet Points for Software Engineer / Full-Stack Developer Role

**HungerHub – Industrial Full-Stack Food Ordering & Real-Time Restaurant Management Ecosystem**  
*Technologies: PHP 8, MySQL 8, JavaScript (ES6+), Bootstrap 5.3, Leaflet.js, Razorpay SDK, Web Audio API, AJAX, Apache, Git*
- **Engineered an enterprise-grade food ordering platform** supporting full customer lifecycle: debounced autocomplete menu search, session-based cart ledger, promotional discount calculation (`SAVE10`), and real-time order tracking.
- **Architected a production dual-payment infrastructure** implementing Razorpay Standard Gateway with HMAC SHA-256 cryptographic signature validation and a Direct Cafe UPI channel with 12-digit bank UTR entry and administrative verification against bank soundbox/SMS notifications.
- **Developed a real-time GPS delivery tracking portal** utilizing Leaflet.js and OpenStreetMap, featuring dynamic road-waypoint interpolation for moving courier scooter markers, pulsing HTML map nodes, and asynchronous 3-second telemetry polling.
- **Engineered HungerBot, an AI-powered culinary concierge**, featuring natural language intent parsing for dietary preferences, budget-conscious meal recommendations (e.g. *"veg under ₹200"*), operational FAQ responses, and direct 1-click cart addition.
- **Implemented an administrative Kitchen Display System (KDS)** with zero-dependency Web Audio API harmonic chime synthesis, automated 7-second order polling, instant status filter tabs, dynamic courier assignment, and one-click bank UTR reconciliation.
- **Designed a GST-compliant tax billing engine (`invoice.php`)** with itemized rates, promotional deductions, merchant GSTIN verification, and automatic `@media print` A4 optimization.
- **Enforced stringent OWASP Top 10 security standards**: 100% parameterized MySQLi prepared statements preventing SQLi, `htmlspecialchars` output escaping for XSS protection, BCrypt password hashing, and `.htaccess` file shielding.

---

### Option 2: Concise 3-Line Summary (For 1-Page Resume)

- **HungerHub (Industrial Food Delivery & Restaurant KDS | PHP 8, MySQL, JS, Bootstrap, Leaflet):** Engineered a commercial food ordering platform featuring Razorpay HMAC-verified payments, Direct UPI UTR reconciliation, and interactive Leaflet.js GPS delivery tracking.
- Integrated **HungerBot AI concierge** for budget-aware dish discovery, instant autocomplete search, and a real-time Kitchen Display System with Web Audio chime alerts and live order polling.
- Secured full stack with parameterized prepared statements, BCrypt hashing, and automated GST-compliant tax invoicing.

---

## 🎓 Final Year Project Viva Voce (Teacher Q&A Cheat Sheet)

When your teacher, professor, or examiner asks questions during tomorrow's evaluation, answer with these concise, technically confident responses:

### 1. "Can you give a 1-minute elevator pitch of your project?"
> *"Sir/Ma'am, HungerHub is an industrial-grade, commercial food ordering and restaurant management platform I developed using PHP 8, MySQL, and modern JavaScript. Unlike toy projects with fake checkout buttons, HungerHub implements real-world commercial standards: official Razorpay gateway with cryptographic HMAC SHA-256 verification, Direct Cafe UPI with customer 12-digit bank UTR entry, an interactive Leaflet.js GPS delivery route tracker, an AI-powered culinary assistant ('HungerBot'), and a real-time Kitchen Display System with sound notifications and GST billing."*

### 2. "How did you implement the real-time GPS order tracking?"
> *"I integrated Leaflet.js and OpenStreetMap for interactive geospatial visualization. The system calculates a multi-waypoint transit corridor between our restaurant dispatch hub in Ranchi and the customer's delivery destination. An asynchronous client-side engine queries `api_order_status.php` every 3 seconds. As the kitchen advances the order through stages (Placed → Accepted → Cooking → Out for Delivery → Delivered), the script interpolates the courier scooter's coordinates along the transit path and updates the live ETA and dynamic rider contact details."*

### 3. "How does your payment system verify transactions securely?"
> *"We support two production payment channels without any mock shortcuts:*
> *1. **Razorpay Modal Gateway**: The server validates the payment signature using HMAC SHA-256 (`hash_hmac('sha256', $order_id . '|' . $payment_id, SECRET)`). If the client signature doesn't match the server hash, the transaction is rejected, preventing payment tampering.*
> *2. **Direct Restaurant UPI**: The customer scans the restaurant QR code (`8603972526@ptyes`), completes the payment in their UPI app, and submits the genuine 12-digit bank UTR reference number. The transaction is marked as 'Processing' until the restaurant manager verifies the UTR against their bank SMS or soundbox in `admin/orders.php` with one click.*
> *We also support Cash on Delivery."*

### 4. "What is HungerBot and how does it work?"
> *"HungerBot is our AI culinary concierge accessible across the website. It parses natural language queries using rule and semantic intent engines. It understands price constraints (e.g. 'veg under ₹200' extracts budget <= 200 and category = Veg), dish cravings ('spicy biryani', 'margherita pizza'), coupons ('SAVE10' offers), and operational FAQs. It queries the live menu database and returns rich dish cards with direct 1-click 'Add to Cart' functionality right inside the chat."*

### 5. "How does the Kitchen Display System (KDS) alert the chef?"
> *"In `admin/orders.php`, the frontend polls `api_order_poll.php` every 7 seconds. When a new order ID is detected, the browser generates a multi-harmonic triad chime (A5, C#6, E6) using the Web Audio API without needing external MP3 audio assets. It displays a toast alert and updates the order list immediately."*

### 6. "How do you protect against SQL Injection and XSS?"
> *"100% of our database queries are written using parameterized Prepared Statements with MySQLi (`$conn->prepare()` and `$stmt->bind_param()`), ensuring that user input is never concatenated into SQL execution strings. For Cross-Site Scripting (XSS), all dynamic data displayed in the DOM is escaped via `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`."*

---

## 🏆 Step-by-Step Presentation Demo Flow for Tomorrow

1. **Step 1: Homepage & Instant Live Search**
   - Open `index.php` in your browser.
   - Type `"piz"` in the top navbar search box $\rightarrow$ show the instant autocomplete dropdown displaying dish photos, prices, Veg/Non-Veg badges, and the "+ Add" button.
2. **Step 2: HungerBot AI Concierge**
   - Click the floating **"Ask Chef AI"** button at the bottom-right.
   - Click the chip **"🥗 Veg under ₹200"** $\rightarrow$ show HungerBot recommending matching vegetarian meals and click **"+ Add"** inside the chat!
   - Type `"Do you have any discount coupon?"` $\rightarrow$ show HungerBot sharing the **`SAVE10`** promo code!
3. **Step 3: Cart & Confetti Micro-interaction**
   - Navigate to `cart.php`.
   - Click **"Apply Deal"** on the **`SAVE10`** card $\rightarrow$ watch the instant 10% discount deduction and the colorful celebratory confetti blast!
4. **Step 4: Authentic Checkout & Dual Payment Options**
   - Click **"Proceed to Checkout"** $\rightarrow$ enter customer details.
   - Select **Direct UPI** $\rightarrow$ click **Place Order**.
   - Point out the dynamic restaurant QR code and merchant VPA (`8603972526@ptyes`).
   - Enter a 12-digit UTR (e.g., `425519842103`) $\rightarrow$ click **"Submit UTR & Confirm Order"**.
5. **Step 5: Order Confirmation & Confetti Celebration**
   - On `order_success.php` $\rightarrow$ highlight the celebratory fireworks, order ID, and transaction breakdown.
   - Click **"Download Tax Invoice"** $\rightarrow$ show the GST Tax Invoice with printable layout.
6. **Step 6: Real-Time Leaflet.js GPS Tracking**
   - Click **"🚀 Track Order Live"** (`track_order.php`).
   - Show the interactive OpenStreetMap with the restaurant origin, delivery destination, road route polyline, and delivery rider pin!
7. **Step 7: Admin Kitchen Display System & Real-Time Sync**
   - Open `admin/orders.php` in a second tab/window.
   - Point out the **"Kitchen Orders & Dispatch (KDS)"** header, **"Chime: ON"** button, and operational status filter tabs.
   - Find the new order with the submitted UTR $\rightarrow$ click **"Verify UTR"** $\rightarrow$ status changes to Confivered and Paid!
   - Assign a delivery courier (e.g., *Ramesh Kumar*, *Bajaj Pulsar*, *+91 9876543210*) and set status to **"On The Way"**.
   - Switch back to the customer's `track_order.php` tab $\rightarrow$ show that **without reloading**, the stepper updates to "On The Way", the rider scooter moves on the Leaflet GPS map, and courier details appear dynamically!