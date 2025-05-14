<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

// Check if user is logged in
$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

try {
    // Add freelancer role to the user
    User::addRole((int)$userData['id'], 'freelancer');
    
    // Add a success message
    $session->addMessage('success', 'You are now a freelancer! You can start creating services.');
    
    // Redirect back to profile
    header('Location: ../pages/profile.php');
    exit();
} catch (Exception $e) {
    $session->addMessage('error', $e->getMessage());
    header('Location: ../pages/profile.php');
    exit();
}
?>
