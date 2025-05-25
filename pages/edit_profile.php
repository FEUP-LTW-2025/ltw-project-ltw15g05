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

$editUserId = isset($_GET['id']) && in_array('admin', $currentUser['roles']) 
    ? (int)$_GET['id'] 
    : (int)$currentUser['id'];

$userData = $editUserId === $currentUser['id'] 
    ? $currentUser 
    : User::get_user_by_id($editUserId);

if (!$userData) {
    header('Location: edit_profile.php');
    exit();
}

$isAdminEdit = $editUserId !== (int)$currentUser['id'] && in_array('admin', $currentUser['roles']);

drawHeader(true, $currentUser);
drawEditProfileForm($userData, $isAdminEdit, $currentUser);
drawFooter();
?>
