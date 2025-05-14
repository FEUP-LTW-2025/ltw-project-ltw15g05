<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/user.tpl.php');

$session = Session::getInstance();
$messages = $session->getMessages();

drawHeader();
drawLoginForm($messages ?? []);
drawFooter();
?>