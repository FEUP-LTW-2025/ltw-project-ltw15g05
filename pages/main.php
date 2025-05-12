<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/main.tpl.php');

drawHeader(true); // Pass true to indicate user is logged in
drawMainPage($userData);
drawFooter();
?>