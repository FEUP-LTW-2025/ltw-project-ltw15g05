<?php
declare(strict_types=1);

require_once '../includes/database.php';
require_once '../database/service.class.php';
require_once '../templates/main.tpl.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Get filter parameters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$deliveryTime = isset($_GET['delivery_time']) ? (int)$_GET['delivery_time'] : null;

// Build the SQL query based on filters
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

// Execute the query
try {
    $db = Database::getInstance();
    $stmt = $db->prepare($sql);
    
    // Bind parameters
    for ($i = 0; $i < count($params); $i++) {
        $stmt->bindValue($i + 1, $params[$i]);
    }
      $stmt->execute();
    
    // Fetch services
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
    
    // Start output buffer to capture the HTML
    ob_start();
    drawServiceList($services);
    $html = ob_get_clean();
    
    // Return JSON response with HTML
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
