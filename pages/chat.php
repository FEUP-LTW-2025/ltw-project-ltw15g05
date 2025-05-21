<?php
declare(strict_types=1);

require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../database/user.class.php';
require_once '../database/messages.class.php';
require_once '../templates/messages.tpl.php';
require_once '../templates/common.tpl.php';
$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}
$currentUserId = $_SESSION['user_id'];
$chatWithId = $_GET['chat_with'] ?? null;
$recentChats = Messages::getRecentChats($currentUserId);

if (!$chatWithId) {
    die('Select a user to chat with.');
}

$messages = Messages::getMessagesBetween($currentUserId, $chatWithId);

drawHeader(true);
drawChat($messages, $currentUserId, $chatWithId, $recentChats);
?>

