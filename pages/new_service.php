<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/user.class.php');


$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

if (!in_array('freelancer', $userData['roles'])) {
    $session->addMessage('error', 'You must be a freelancer to create services.');
    header('Location: profile.php');
    exit();
}

$categories = Service::getAllCategories();
$messages = $session->getMessages();

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/service.tpl.php');

drawHeader(true, $userData);
drawNewServiceForm($categories, $messages);
drawFooter();
?>
