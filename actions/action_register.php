<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');

$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password'];

try {
    User::create($name, $username, $password);
    header('Location: /../pages/form_login.php');
    exit();
} catch (Exception $e) {
    header('Location: /../pages/form_register.php?error=' . urlencode($e->getMessage()));
    exit();
}

?>