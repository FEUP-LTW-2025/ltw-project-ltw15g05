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
    $userId = User::create($name, $username, $password, $email);
    
    // If we get here, registration was successful
    header('Location: /../pages/form_login.php');
    exit();
} catch (Exception $e) {
    // Log error for debugging
    error_log("Registration error: " . $e->getMessage());
    header('Location: /../pages/form_register.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>