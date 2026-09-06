<?php
/**
 * HungerHub - Culinary Concierge AI Engine (HungerBot)
 * Developed by: Sonu Kumar
 * Provides context-aware gastronomic recommendations, store policies, discounts, and real-time menu queries.
 */

header('Content-Type: application/json; charset=UTF-8');
require_once 'db.php';
require_once 'config.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
$userMessage = trim($data['message'] ?? ($_POST['message'] ?? ($_GET['message'] ?? '')));

if (empty($userMessage)) {
    echo json_encode([
        'success' => true,
        'message' => "Hello! 👋 I'm **HungerBot**, your personal culinary concierge. What are you craving today? You can ask for recommendations, discounts, or dietary preferences (e.g. *'veg under 200'*, *'spicy biryani'*, *'today offers'*).",
        'items' => [],
        'quick_replies' => ["🍕 Best Pizzas", "🍛 Biryani Specials", "🥗 Pure Veg", "💰 Under ₹200", "🎟️ Active Offers"]
    ]);
    exit();
}

$lowerMsg = mb_strtolower($userMessage);
$responseMessage = "";
$items = [];
$quickReplies = ["Browse Menu", "Apply SAVE10", "Track Order"];

// 1. Check for Coupons / Offers
if (preg_match('/\b(coupon|discount|offer|promo|code|save|deal)\b/i', $lowerMsg)) {
    $responseMessage = "🎉 **Exclusive Deal:** Use coupon code <strong class='text-warning bg-dark px-2 py-1 rounded'>SAVE10</strong> at checkout for a **10% flat discount** on your order!";
    $quickReplies = ["Apply SAVE10 in Cart", "Browse Pizzas", "Browse Biryani"];
}
// 2. Check for Tracking / Delivery Time
elseif (preg_match('/\b(track|status|where is|delivery|arrive|late|rider|driver)\b/i', $lowerMsg)) {
    $responseMessage = "🛵 You can monitor your meal's live preparation and courier GPS transit on our <a href='track_order.php' class='fw-bold text-decoration-underline'>Live Tracking Portal</a> with real-time telemetry!";
    $quickReplies = ["Track My Order", "Call Restaurant", "Menu"];
}
// 3. Check for Payment Methods
elseif (preg_match('/\b(pay|payment|upi|cod|cash|card|wallet|gpay|phonepe)\b/i', $lowerMsg)) {
    $responseMessage = "💳 We support multiple secure payment options:\n• **Direct Cafe UPI** (Scan QR & verify via Bank UTR)\n• **Razorpay Gateway** (Credit/Debit cards, Netbanking)\n• **Cash on Delivery (COD)**";
    $quickReplies = ["How to Pay via UPI", "Order Now", "Discounts"];
}
// 4. Location & Contact
elseif (preg_match('/\b(location|address|where are you|phone|contact|hours|open|timing)\b/i', $lowerMsg)) {
    $responseMessage = "📍 **" . REST_NAME . "** is located at: Virandavan Nagar Road No. 1, Sai Vihar Colony, Madhukam, Ranchi, Jharkhand.\n📞 Direct Kitchen Hotline: **" . REST_PHONE . "**\n⏰ Open 24/7 for express delivery!";
    $quickReplies = ["Call Front Desk", "View Menu", "Track Order"];
}
// 5. Greetings
elseif (preg_match('/^(hi|hello|hey|yo|greetings|hola|namaste|morning|evening)/i', $lowerMsg) && mb_strlen($lowerMsg) < 20) {
    $responseMessage = "Hello! Delicious food is just a click away. Hungry? Let me recommend something freshly cooked from our kitchen!";
    $quickReplies = ["🍕 Best Pizzas", "🍛 Biryani", "🥗 Veg Delights", "💰 Under ₹200"];
}

// Check for culinary searches (keywords, categories, or price budget)
$sqlConditions = [];
$params = [];
$types = "";

// Price filter detection: e.g. "under 200", "under 300", "below 150"
$priceBudget = null;
if (preg_match('/(?:under|below|less than|within)\s*(?:₹|rs\.?|inr)?\s*(\d+)/i', $lowerMsg, $pMatch)) {
    $priceBudget = (float)$pMatch[1];
    $sqlConditions[] = "price <= ?";
    $params[] = $priceBudget;
    $types .= "d";
}

// Category filter
if (preg_match('/\b(veg|vegetarian|paneer)\b/i', $lowerMsg) && !preg_match('/\b(non-veg|chicken|mutton|egg)\b/i', $lowerMsg)) {
    $sqlConditions[] = "main_category = 'Veg'";
} elseif (preg_match('/\b(non-veg|chicken|meat|mutton|fish)\b/i', $lowerMsg)) {
    $sqlConditions[] = "main_category = 'Non-Veg'";
}

// Specific food items
$dishKeywords = ['pizza', 'biryani', 'burger', 'thali', 'noodles', 'paneer', 'chicken', 'dessert', 'rolls', 'chaat', 'sandwich', 'salad', 'shake'];
$matchedKeyword = null;
foreach ($dishKeywords as $kw) {
    if (strpos($lowerMsg, $kw) !== false) {
        $matchedKeyword = $kw;
        $sqlConditions[] = "(name LIKE ? OR description LIKE ? OR sub_category LIKE ?)";
        $kwLike = '%' . $kw . '%';
        $params[] = $kwLike;
        $params[] = $kwLike;
        $params[] = $kwLike;
        $types .= "sss";
        break;
    }
}

// If no specific condition was matched but response is still empty, search query broadly
if (empty($responseMessage) && empty($sqlConditions)) {
    $sqlConditions[] = "(name LIKE ? OR description LIKE ? OR sub_category LIKE ?)";
    $rawLike = '%' . $userMessage . '%';
    $params[] = $rawLike;
    $params[] = $rawLike;
    $params[] = $rawLike;
    $types .= "sss";
}

// Query database if conditions exist
if (!empty($sqlConditions)) {
    $whereClause = implode(" AND ", $sqlConditions);
    $querySql = "SELECT id, name, description, price, image, main_category, sub_category FROM menu_items WHERE $whereClause ORDER BY price ASC LIMIT 4";
    
    $stmt = $conn->prepare($querySql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $items[] = [
                'id' => (int)$r['id'],
                'name' => htmlspecialchars($r['name']),
                'description' => htmlspecialchars($r['description'] ?? ''),
                'price' => (float)$r['price'],
                'formatted_price' => '₹' . number_format((float)$r['price'], 2),
                'image' => htmlspecialchars($r['image'] ?? 'images/default_food.jpg'),
                'main_category' => htmlspecialchars($r['main_category'] ?? 'Veg')
            ];
        }
    }
}

// Format final response text if dishes were found
if (!empty($items)) {
    if (empty($responseMessage)) {
        $responseMessage = "Here are our top chef-recommended dishes matching your preference:";
    } else {
        $responseMessage .= "\n\n**Here are delicious dishes you'll love:**";
    }
} elseif (empty($responseMessage)) {
    $responseMessage = "I couldn't find an exact dish matching that, but our kitchen has incredible pizzas, biryanis, and chef specials! Would you like me to recommend our best-sellers?";
    $quickReplies = ["🍕 Top Pizzas", "🍛 Biryani Specials", "🥗 Pure Veg", "💰 Under ₹200"];
}

echo json_encode([
    'success' => true,
    'message' => $responseMessage,
    'items' => $items,
    'quick_replies' => $quickReplies
]);