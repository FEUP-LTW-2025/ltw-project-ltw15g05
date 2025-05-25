<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');

$session = Session::getInstance();
$user = $session->getUser();
if (!$user || !in_array('admin', $user['roles'])) {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->addMessage('error', 'Invalid category ID.');
    header('Location: ../pages/admin.php');
    exit();
}

$categoryId = (int)$_GET['id'];

try {
    $category = Service::getCategoryById($categoryId);
    if (!$category) {
        throw new Exception('Category not found.');
    }
    Service::deleteCategory($categoryId);
    logAdminAction($user['id'], "Deleted category: {$category['name']} (ID: {$categoryId})");
    
    $session->addMessage('success', 'Category deleted successfully.');
} catch (Exception $e) {
    $session->addMessage('error', 'Error deleting category: ' . $e->getMessage());
}

header('Location: ../pages/admin.php');
exit();

function logAdminAction($adminId, $action, $targetUserId = null) {
    try {
        require_once(__DIR__ . '/../includes/database.php');
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO admin_actions (admin_id, action, target_user_id) VALUES (?, ?, ?)');
        $stmt->execute([$adminId, $action, $targetUserId]);
        return true;
    } catch (PDOException $e) {
        error_log("Admin action logging error: " . $e->getMessage());
        return false;
    }
}
