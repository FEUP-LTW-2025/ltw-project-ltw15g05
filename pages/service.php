<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/user.class.php');


$session = Session::getInstance();
$userData = $session->getUser();

$service = Service::getService((int)$_GET['id']);
$freelancer = User::get_user_by_id($service->freelancer_id);



if (!$service) {
    die('Serviço não encontrado.');
}

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/service.tpl.php');

drawHeader(true);
drawServicePage($service, $freelancer);
drawFooter();
?>
