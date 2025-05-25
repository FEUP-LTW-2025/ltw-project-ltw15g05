<?php

require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');


$session = Session::getInstance();
$userData = $session->getUser();

$services = Service::getAllServices();

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$delivery_time = isset($_GET['delivery_time']) ? (int)$_GET['delivery_time'] : null;

if ($category_id !== null) {
    $services = Service::getServicesByCategory($category_id);
} elseif ($min_price !== null && $max_price !== null) {
    $services = Service::getServicesByPrice($min_price, $max_price);
} elseif ($delivery_time !== null) {
    $services = Service::getServicesByDeliveryTime($delivery_time);
} else {
    $services = Service::getAllServices();
}

$categories = Service::getAllCategories();
$priceRanges = Service::getPriceRanges();
$deliveryTimeRanges = Service::getDeliveryTimeRanges();

$activeFilters = [
    'category' => $category_id,
    'price' => ($min_price !== null && $max_price !== null) ? ['min' => $min_price, 'max' => $max_price] : null,
    'delivery_time' => $delivery_time
];

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    require_once(__DIR__ . '/../templates/main.tpl.php');
    drawServiceList($services);
    exit();
}

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/main.tpl.php');


drawHeader(true, $userData); 
drawFilterNavigation($categories, $priceRanges, $deliveryTimeRanges, $activeFilters);
drawServiceList($services);
drawFooter();
?>