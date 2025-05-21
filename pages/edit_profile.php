<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/user.tpl.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

drawHeader(true, $userData);
drawEditProfileForm($userData);
drawFooter();
?>
