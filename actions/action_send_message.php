<?php
declare(strict_types=1);

require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../database/messages.class.php';

$session = Session::getInstance();
$userId = $session->getUserId();

$receiverId = $_POST['receiver_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if ($receiverId && $content) {
    Messages::sendMessage($userId, $receiverId, $content);
    header("Location: ../pages/chat.php?user=" . urlencode($receiverId));
    exit();
} else {
    die('Invalid input.');
}
