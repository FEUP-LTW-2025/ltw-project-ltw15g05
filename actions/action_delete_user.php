<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

// Get the current session user
$session = Session::getInstance();
$currentUser = $session->getUser();

// Check if the user is logged in and has admin role
if (!$currentUser || !in_array('admin', $currentUser['roles'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['user_id'])) {
    $userId = (int)$_GET['user_id'];
    
    // Don't allow admins to delete themselves
    if ($userId === (int)$currentUser['id']) {
        $_SESSION['error_message'] = "You cannot delete your own account.";
        header('Location: ../pages/admin.php');
        exit();
    }
    
    try {
        // Get user information for logging purposes
        $userData = User::get_user_by_id($userId);
        
        if (!$userData) {
            throw new Exception('User not found');
        }
          // Delete the user and all their data
        User::deleteUser($userId);
          // Log the action
        require_once(__DIR__ . '/../includes/database.php');
        // Define logAdminAction locally if it's not already defined
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

// Redirect back to admin panel
header('Location: ../pages/admin.php');
exit();
?>
