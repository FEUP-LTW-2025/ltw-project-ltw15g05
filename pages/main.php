<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');


$session = Session::getInstance();
$userData = $session->getUser();
$services = Service::getAllServices();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

$messages = $session->getMessages();
foreach ($messages as $message) {
    echo '<div class="alert alert-' . ($message['type'] === 'error' ? 'danger' : $message['type']) . '">' .
         htmlspecialchars($message['content']) .
         '</div>';
}

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/main.tpl.php');


drawHeader(true, $userData);
drawServiceList($services);
drawFooter();
?>