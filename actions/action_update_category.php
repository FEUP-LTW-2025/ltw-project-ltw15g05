<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');

// Get the current session user
$session = Session::getInstance();
$user = $session->getUser();

// Check if the user is logged in and has admin role
if (!$user || !in_array('admin', $user['roles'])) {
    // Redirect non-admin users to the home page
    header('Location: ../index.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    // Description is removed as the database schema doesn't support it
    
    // Validate the data
    if (empty($name) || $id <= 0) {
        $session->addMessage('error', 'Invalid category data.');
        header('Location: ../pages/admin.php');
        exit();
    }
    
    try {
        // Get the original category data for logging
        $originalCategory = Service::getCategoryById($id);
        if (!$originalCategory) {
            throw new Exception('Category not found.');
        }
        
        // Update the category
        Service::updateCategory($id, $name, $description);
        $session->addMessage('success', 'Category updated successfully.');
        
        // Log admin action
        $changes = "Updated category {$id} from '{$originalCategory['name']}' to '{$name}'";
        logAdminAction($user['id'], $changes);
        
    } catch (Exception $e) {
        $session->addMessage('error', 'Error updating category: ' . $e->getMessage());
    }
    
    header('Location: ../pages/admin.php');
    exit();
}

// Log admin actions to the database
function logAdminAction($adminId, $action, $targetUserId = null) {
    try {
        require_once(__DIR__ . '/../includes/database.php');
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO admin_actions (admin_id, action, target_user_id) VALUES (?, ?, ?)');
        $stmt->execute([$adminId, $action, $targetUserId]);
        return true;
    } catch (PDOException $e) {
        // Log error
        error_log("Admin action logging error: " . $e->getMessage());
        return false;
    }
}
