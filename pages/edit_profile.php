<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/user.tpl.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

if (!$currentUser) {
    header('Location: form_login.php');
    exit();
}

// Determine which profile to edit
// If an ID parameter is provided and the current user is an admin, they can edit another user's profile
$editUserId = isset($_GET['id']) && in_array('admin', $currentUser['roles']) 
    ? (int)$_GET['id'] 
    : (int)$currentUser['id'];

// Load user data for editing
$userData = $editUserId === $currentUser['id'] 
    ? $currentUser 
    : User::get_user_by_id($editUserId);

// If the user doesn't exist, redirect to the user's own edit profile page
if (!$userData) {
    header('Location: edit_profile.php');
    exit();
}

// Determine if the current user is editing someone else's profile (admin edit mode)
$isAdminEdit = $editUserId !== (int)$currentUser['id'] && in_array('admin', $currentUser['roles']);

drawHeader(true, $currentUser);
drawEditProfileForm($userData, $isAdminEdit, $currentUser);
drawFooter();
?>
