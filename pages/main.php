<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/service.class.php');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
$session = Session::getInstance();
$userData = $session->getUser();

// Get category filter from query string
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Get services based on category filter
if ($category_id !== null) {
    $services = Service::getServicesByCategory($category_id);
} else {
    $services = Service::getAllServices();
}

// Get all categories for the navigation bar
$categories = Service::getAllCategories();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/main.tpl.php');

drawHeader(true, $userData); // Pass true to indicate user is logged in
drawCategoryNavigation($categories, $category_id);
drawServiceList($services);
drawFooter();
?>