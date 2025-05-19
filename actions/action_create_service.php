<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');

// Check if user is logged in
$session = Session::getInstance();
$userData = $session->getUser();

header('Location: ../pages/new_service.php');

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

// Check if the user is a freelancer
if (!in_array('freelancer', $userData['roles'])) {
    $session->addMessage('error', 'You must be a freelancer to create services');
    header('Location: ../pages/profile.php');
    exit();
}

// Validate inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $delivery_time = isset($_POST['delivery_time']) ? (int)$_POST['delivery_time'] : 0;
    $featured = isset($_POST['featured']);
    
    // Basic validation
    if (empty($title) || empty($description) || $category_id <= 0 || $price <= 0 || $delivery_time <= 0) {
        $session->addMessage('error', 'Please fill in all required fields with valid values');
        header('Location: ../pages/new_service.php');
        exit();
    }
    
    try {
        // Process image uploads
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = __DIR__ . '/../uploads/services/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Process each uploaded file
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                // Limit to 5 images
                if ($i >= 5) break;
                
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][$i];
                    $name = basename($_FILES['images']['name'][$i]);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    
                    // Check if extension is valid
                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        continue;
                    }
                    
                    // Generate unique filename
                    $filename = uniqid('service_') . '.' . $ext;
                    $filepath = $upload_dir . $filename;
                    
                    // Move the file
                    if (move_uploaded_file($tmp_name, $filepath)) {
                        $images[] = 'uploads/services/' . $filename;
                    }
                }
            }
        }


        $photo_style = $_POST['photo_style'] ?? '';
        $equipment_provided = isset($_POST['equipment_provided']) ? 1 : 0;
        $location = $_POST['location'] ?? null;

        // Validação adicional (opcional)
        if (!in_array($photo_style, ['Portrait', 'Landscape'])) {
            $session->addMessage('error', 'Please select a valid photo style');
            header('Location: ../pages/new_service.php');
            exit();
        }

        
        // Create the service
        $service_id = Service::create(
            (int)$userData['id'],
            $title,
            $description,
            $category_id,
            $price,
            $delivery_time,
            $images,
            $featured,
            $photo_style,
            $equipment_provided,
            $location
        );
        

        // Success
        $session->addMessage('success', 'Service created successfully');
        header('Location: ../pages/profile.php');
        exit();
        
    } catch (Exception $e) {
        $session->addMessage('error', 'Error creating service: ' . $e->getMessage());
        header('Location: ../pages/new_service.php');
        exit();
    }
} else {
    // Not a POST request
    header('Location: ../pages/new_service.php');
    exit();
}
?>
