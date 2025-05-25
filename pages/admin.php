<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$user = $session->getUser();

if (!$user || !in_array('admin', $user['roles'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $target_user_id = (int)$_GET['user_id'];
      if ($action === 'promote_to_admin') {
        $target_user = User::get_user_by_id($target_user_id);
        if ($target_user && !in_array('admin', $target_user['roles'])) {
            User::addRole($target_user_id, 'admin');
            
            logAdminAction($_SESSION['user_id'], "Promoted user {$target_user_id} to admin", $target_user_id);
            
            $_SESSION['success_message'] = "User {$target_user['username']} was promoted to admin successfully.";
        }
    }    elseif ($action === 'remove_admin') {
        $target_user = User::get_user_by_id($target_user_id);
        
        $admins = User::getUsersWithRole('admin');
        if (count($admins) > 1 && $target_user && in_array('admin', $target_user['roles'])) {
            User::removeRole($target_user_id, 'admin');
            
            logAdminAction($_SESSION['user_id'], "Removed admin role from user {$target_user_id}", $target_user_id);
            
            $_SESSION['success_message'] = "Admin role was removed from {$target_user['username']}.";
        } else {
            $_SESSION['error_message'] = "Cannot remove the last admin from the system.";
        }
    }
    
    header('Location: admin.php');
    exit();
}

function logAdminAction($adminId, $action, $targetUserId = null) {
    try {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='admin_actions'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        if (!$tableExists) {
            $db->exec("
                CREATE TABLE admin_actions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    admin_id INTEGER NOT NULL,
                    action TEXT NOT NULL,
                    target_user_id INTEGER,
                    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (admin_id) REFERENCES users(id)
                )
            ");
        }
        
        $stmt = $db->prepare('INSERT INTO admin_actions (admin_id, action, target_user_id) VALUES (?, ?, ?)');
        $stmt->execute([$adminId, $action, $targetUserId]);
        return true;
    } catch (PDOException $e) {
        error_log("Admin action logging error: " . $e->getMessage());
        return false;
    }
}

$users = User::getAllUsers();

require_once(__DIR__ . '/../database/service.class.php');
$categories = Service::getAllCategories();

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/admin.tpl.php');

drawHeader(true, $user);
drawAdminPanel($users);
drawCategoryManagement($categories);
drawFooter();
?>
