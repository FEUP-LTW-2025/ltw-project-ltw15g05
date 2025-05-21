<?php
declare(strict_types=1);

require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../database/messages.class.php';
require_once '../database/user.class.php';
require_once '../templates/messages.tpl.php';

$session = Session::getInstance();
$currentUserId = $session->getUserId();
$chatWithId = $_GET['user'] ?? null;

if (!$chatWithId) {
    die('Invalid request');
}

$messages = Messages::getMessagesBetween($currentUserId, $chatWithId);

foreach ($messages as $msg) {
    echo drawMessage($msg, $currentUserId);
}