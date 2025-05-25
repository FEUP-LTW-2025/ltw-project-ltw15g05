<?php
declare(strict_types=1);

require_once '../includes/database.php';
require_once '../database/service.class.php';
require_once '../templates/main.tpl.php';

header('Content-Type: application/json');

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$deliveryTime = isset($_GET['delivery_time']) ? (int)$_GET['delivery_time'] : null;

$sql = "SELECT * FROM services WHERE 1=1";
$params = [];

if ($categoryId !== null) {
    $sql .= " AND category_id = ?";
    $params[] = $categoryId;
}

if ($minPrice !== null && $maxPrice !== null) {
    $sql .= " AND price >= ? AND price <= ?";
    $params[] = $minPrice;
    $params[] = $maxPrice;
}

if ($deliveryTime !== null) {
    $sql .= " AND delivery_time <= ?";
    $params[] = $deliveryTime;
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare($sql);
    
    for ($i = 0; $i < count($params); $i++) {
        $stmt->bindValue($i + 1, $params[$i]);
    }
      $stmt->execute();
    
    $services = [];
    while ($service = $stmt->fetch()) {
        $services[] = new Service(
            (int)$service['id'],
            (int)$service['freelancer_id'],
            $service['title'],
            $service['description'],
            (int)$service['category_id'],
            (float)$service['price'],
            (int)$service['delivery_time'],
            $service['photo_style'],
            (bool)$service['equipment_provided'],
            $service['location'],
            $service['created_at']
        );
    }
    
    ob_start();
    drawServiceList($services);
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'count' => count($services)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
