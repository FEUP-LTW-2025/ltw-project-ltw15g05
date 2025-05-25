<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

if (!$currentUser || !in_array('admin', $currentUser['roles'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['user_id'])) {
    $userId = (int)$_GET['user_id'];
    
    if ($userId === (int)$currentUser['id']) {
        $_SESSION['error_message'] = "You cannot delete your own account.";
        header('Location: ../pages/admin.php');
        exit();
    }
    
    try {
        $userData = User::get_user_by_id($userId);
        
        if (!$userData) {
            throw new Exception('User not found');
        }
        User::deleteUser($userId);
        require_once(__DIR__ . '/../includes/database.php');
        if (!function_exists('logAdminAction')) {
            function logAdminAction($adminId, $action, $targetUserId = null) {
                try {
                    $db = Database::getInstance();
                    $stmt = $db->prepare('INSERT INTO admin_actions (admin_id, action, target_user_id) VALUES (?, ?, ?)');
                    $stmt->execute([$adminId, $action, $targetUserId]);
                    return true;
                } catch (PDOException $e) {
                    error_log("Admin action logging error: " . $e->getMessage());
                    return false;
                }
            }
        }
        logAdminAction($currentUser['id'], "Deleted user {$userId} ({$userData['username']})", null);
        
        $_SESSION['success_message'] = "User '{$userData['username']}' has been successfully deleted along with all associated data.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
    }
}

header('Location: ../pages/admin.php');
exit();
?>
