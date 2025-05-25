<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../includes/session.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

if (!$currentUser) {
    header('Location: ../pages/form_login.php');
    exit();
}

error_log("Edit Profile - Current User ID: " . $currentUser['id'] . " (" . gettype($currentUser['id']) . ")");
error_log("Edit Profile - Current User Roles: " . implode(", ", $currentUser['roles']));

$isAdminEdit = isset($_GET['id']) && in_array('admin', $currentUser['roles']);
$editUserId = $isAdminEdit ? (int)$_GET['id'] : (int)$currentUser['id'];

error_log("Edit Profile - isAdminEdit: " . ($isAdminEdit ? 'true' : 'false'));
error_log("Edit Profile - editUserId: $editUserId (" . gettype($editUserId) . ")");
error_log("Edit Profile - GET id: " . (isset($_GET['id']) ? $_GET['id'] . " (" . gettype($_GET['id']) . ")" : "not set"));

$userData = $editUserId === (int)$currentUser['id'] 
    ? $currentUser 
    : User::get_user_by_id($editUserId);

if (!$userData || ($editUserId !== (int)$currentUser['id'] && !$isAdminEdit)) {
    header('Location: ../pages/profile.php?error=' . urlencode('Invalid user or insufficient permissions'));
    exit();
}

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$adminEdit = isset($_POST['admin_edit']) ? (bool)$_POST['admin_edit'] : false;

if (empty($name) || empty($username)) {
    $redirectUrl = $isAdminEdit 
        ? "../pages/edit_profile.php?id={$editUserId}&error=" . urlencode('Name and username are required')
        : "../pages/edit_profile.php?error=" . urlencode('Name and username are required');
    header("Location: $redirectUrl");
    exit();
}

if (!$adminEdit && !$isAdminEdit && !empty($newPassword) && empty($currentPassword)) {
    $redirectUrl = $isAdminEdit 
        ? "../pages/edit_profile.php?id={$editUserId}&error=" . urlencode('Current password is required when changing password')
        : "../pages/edit_profile.php?error=" . urlencode('Current password is required when changing password');
    header("Location: $redirectUrl");
    exit();
}

if (!empty($newPassword) && $newPassword !== $confirmPassword) {
    $redirectUrl = $isAdminEdit 
        ? "../pages/edit_profile.php?id={$editUserId}&error=" . urlencode('New passwords do not match')
        : "../pages/edit_profile.php?error=" . urlencode('New passwords do not match');
    header("Location: $redirectUrl");
    exit();
}

try {
    error_log("Edit Profile - Validations starting for user ID: {$userData['id']} (Admin Edit: " . ($isAdminEdit ? 'Yes' : 'No') . ")");
    
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ../pages/edit_profile.php' . ($isAdminEdit ? '?id=' . $editUserId : '') . '&error=' . urlencode('Please enter a valid email address'));
            exit();
        }
        
        $valid_domains = ['@gmail.com', '@hotmail.com', '@outlook.com', '@yahoo.com', '@icloud.com', '@protonmail.com', '@mail.com'];
        $valid_email = false;
        
        foreach ($valid_domains as $domain) {
            if (str_ends_with(strtolower($email), $domain)) {
                $valid_email = true;
                break;
            }
        }
        
        if (!$valid_email) {
            $redirectUrl = $isAdminEdit 
                ? "../pages/edit_profile.php?id={$editUserId}&error=" . urlencode('Please use a valid email domain (gmail.com, hotmail.com, outlook.com, etc)')
                : "../pages/edit_profile.php?error=" . urlencode('Please use a valid email domain (gmail.com, hotmail.com, outlook.com, etc)');
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    error_log("Edit Profile - About to update profile for user ID: {$userData['id']}");
    try {
        $updatedUser = User::updateProfile(
            (int)$userData['id'], 
            $name, 
            $username, 
            $currentPassword, 
            $newPassword,
            $email,
            $adminEdit || $isAdminEdit 
        );
        error_log("Edit Profile - Profile updated successfully for user ID: {$userData['id']}");
    } catch (Exception $e) {
        error_log("Edit Profile - Error updating profile: " . $e->getMessage());
        throw $e;
    }
    $updatedUserData = User::get_user_by_id((int)$userData['id']);
    $session->updateUser($updatedUserData);
    
    if ($isAdminEdit) {
        header('Location: ../pages/profile.php?id=' . $editUserId . '&success=' . urlencode('Profile updated successfully'));
    } else {
        header('Location: ../pages/profile.php?success=' . urlencode('Profile updated successfully'));
    }
    exit();
} catch (Exception $e) {
    error_log("Edit Profile - Final Error: " . $e->getMessage());
    $redirectUrl = $isAdminEdit 
        ? "../pages/edit_profile.php?id={$editUserId}&error=" . urlencode($e->getMessage())
        : "../pages/edit_profile.php?error=" . urlencode($e->getMessage());
    header("Location: $redirectUrl");
    exit();
}
?>
