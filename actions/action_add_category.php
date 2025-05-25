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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $session->addMessage('error', 'Category name cannot be empty.');
        header('Location: ../pages/admin.php');
        exit();
    }
    
    try {
        Service::addCategory($name, null);
        $session->addMessage('success', 'Category added successfully.');
        logAdminAction($user['id'], "Added new category: {$name}");
        
    } catch (Exception $e) {
        $session->addMessage('error', 'Error adding category: ' . $e->getMessage());
    }
    
    header('Location: ../pages/admin.php');
    exit();
}

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
