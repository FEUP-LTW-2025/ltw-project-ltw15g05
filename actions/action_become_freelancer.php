<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

try {
    User::addRole((int)$userData['id'], 'freelancer');
    header('Location: ../pages/profile.php');
    exit();
} catch (Exception $e) {
    $session->addMessage('error', $e->getMessage());
    header('Location: ../pages/profile.php');
    exit();
}
?>
