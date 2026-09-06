<?php
/**
 * HungerHub - Instant Live Search API
 * Developed by: Sonu Kumar
 * Provides rapid JSON search results for menu autocomplete and instant cart actions.
 */

header('Content-Type: application/json; charset=UTF-8');
require_once 'db.php';

$query = trim($_GET['q'] ?? '');

if (mb_strlen($query) < 2) {
    echo json_encode([
        'success' => true,
        'query' => $query,
        'count' => 0,
        'results' => []
    ]);
    exit();
}

$like = '%' . $query . '%';
$stmt = $conn->prepare("
    SELECT id, name, description, price, image, main_category, sub_category 
    FROM menu_items 
    WHERE name LIKE ? 
       OR description LIKE ? 
       OR sub_category LIKE ? 
       OR main_category LIKE ? 
    ORDER BY (name LIKE ?) DESC, name ASC 
    LIMIT 8
");

$stmt->bind_param("sssss", $like, $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'id' => (int)$row['id'],
        'name' => htmlspecialchars($row['name']),
        'description' => htmlspecialchars($row['description'] ?? ''),
        'price' => (float)$row['price'],
        'formatted_price' => '₹' . number_format((float)$row['price'], 2),
        'image' => htmlspecialchars($row['image'] ?? 'images/default_food.jpg'),
        'main_category' => htmlspecialchars($row['main_category'] ?? 'Veg'),
        'sub_category' => htmlspecialchars($row['sub_category'] ?? '')
    ];
}

echo json_encode([
    'success' => true,
    'query' => $query,
    'count' => count($items),
    'results' => $items
]);