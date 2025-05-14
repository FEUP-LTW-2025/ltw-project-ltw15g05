<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($password)) {
    $session->addMessage('error', 'Username and password are required');
    header('Location: ../pages/form_login.php');
    exit();
}

try {
    $user = User::get_user_by_username_password($username, $password);
    $session->login((int)$user['id']);
    header('Location: ../pages/main.php');
    exit();
} catch (Exception $e) {
    header('Location: /../pages/form_login.php?error=' . urlencode($e->getMessage()));
    exit();
}

?>