<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../includes/session.php');

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';

// Basic validation
if (empty($name) || empty($username) || empty($password)) {
    header('Location: /../pages/form_register.php?error=' . urlencode('All fields are required'));
    exit();
}

try {
    User::create($name, $username, $password, $email);
    header('Location: /../pages/form_login.php');
    exit();
} catch (Exception $e) {
    header('Location: /../pages/form_register.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>