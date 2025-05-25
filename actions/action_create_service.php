<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

if (!in_array('freelancer', $userData['roles'])) {
    $session->addMessage('error', 'You must be a freelancer to create services');
    header('Location: ../pages/profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $delivery_time = isset($_POST['delivery_time']) ? (int)$_POST['delivery_time'] : 0;
    $featured = isset($_POST['featured']);
    $photo_style = $_POST['photo_style'] ?? '';
    $equipment_provided = isset($_POST['equipment_provided']) ? true : false;
    $location = $_POST['location'] ?? null;

    if (empty($title) || empty($description) || $category_id <= 0 || $price <= 0 || $delivery_time <= 0) {
        $session->addMessage('error', 'Please fill in all required fields with valid values');
        header('Location: ../pages/new_service.php');
        exit();
    }

    try {
        $service_id = Service::create(
            (int)$userData['id'],
            $title,
            $description,
            $category_id,
            $price,
            $delivery_time,
            [],
            $photo_style,
            $equipment_provided,
            $location
        );

        $target_dir = __DIR__ . '/../images/services/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $target_file = $target_dir . $service_id . '.jpg';
            move_uploaded_file($tmp_name, $target_file);
        }

        $session->addMessage('success', 'Service created successfully');
        header('Location: ../pages/main.php'); 
        exit();
    } catch (Exception $e) {
        $session->addMessage('error', 'Error creating service: ' . $e->getMessage());
        header('Location: ../pages/new_service.php');
        exit();
    }
} else {
    header('Location: ../pages/new_service.php');
    exit();
}
?>