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

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/main.tpl.php');


drawHeader(true, $userData); // Pass true to indicate user is logged in
drawServiceList($services);
drawFooter();
?>