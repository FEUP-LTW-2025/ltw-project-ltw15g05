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

// Check if ID parameter is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->addMessage('error', 'Invalid category ID.');
    header('Location: ../pages/admin.php');
    exit();
}

$categoryId = (int)$_GET['id'];

try {
    // Get the category for logging
    $category = Service::getCategoryById($categoryId);
    if (!$category) {
        throw new Exception('Category not found.');
    }
    
    // Delete the category
    Service::deleteCategory($categoryId);
    
    // Log admin action
    logAdminAction($user['id'], "Deleted category: {$category['name']} (ID: {$categoryId})");
    
    $session->addMessage('success', 'Category deleted successfully.');
} catch (Exception $e) {
    $session->addMessage('error', 'Error deleting category: ' . $e->getMessage());
}

// Redirect back to admin page
header('Location: ../pages/admin.php');
exit();

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
