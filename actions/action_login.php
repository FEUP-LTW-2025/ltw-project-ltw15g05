<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');

require_once(__DIR__ . '/../database/user.class.php');

$username = $_POST['username'];
$password = $_POST['password'];

$user = User::get_user_by_username_password($username, $password);

if ($user) {
    Session::getInstance()->login($user['id']);
    header('Location: ../pages/profile.php');
    exit();
} else {
    header('Location: ../pages/form_login.php?error=1');
    exit();
}
?>4