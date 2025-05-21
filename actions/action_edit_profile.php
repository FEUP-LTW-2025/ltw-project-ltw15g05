<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../includes/session.php');

// Start the session
$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($name) || empty($username) || empty($currentPassword)) {
    header('Location: ../pages/edit_profile.php?error=' . urlencode('Name, username, and current password are required'));
    exit();
}

// Check if passwords match when changing password
if (!empty($newPassword) && $newPassword !== $confirmPassword) {
    header('Location: ../pages/edit_profile.php?error=' . urlencode('New passwords do not match'));
    exit();
}

try {
    // Validate email if provided
    if (!empty($email)) {
        // Check if it's a valid email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ../pages/edit_profile.php?error=' . urlencode('Please enter a valid email address'));
            exit();
        }
        
        // Check if email ends with a valid domain
        $valid_domains = ['@gmail.com', '@hotmail.com', '@outlook.com', '@yahoo.com', '@icloud.com', '@protonmail.com', '@mail.com'];
        $valid_email = false;
        
        foreach ($valid_domains as $domain) {
            if (str_ends_with(strtolower($email), $domain)) {
                $valid_email = true;
                break;
            }
        }
        
        if (!$valid_email) {
            header('Location: ../pages/edit_profile.php?error=' . urlencode('Please use a valid email domain (gmail.com, hotmail.com, outlook.com, etc)'));
            exit();
        }
    }
      // Update user profile with all information including email
    $updatedUser = User::updateProfile(
        (int)$userData['id'], 
        $name, 
        $username, 
        $currentPassword, 
        $newPassword,
        $email
    );
    
    // Refresh the session data with the updated user information
    $updatedUserData = User::get_user_by_id((int)$userData['id']);
    $session->updateUser($updatedUserData);
    
    header('Location: ../pages/profile.php?success=' . urlencode('Profile updated successfully'));
    exit();
} catch (Exception $e) {
    header('Location: ../pages/edit_profile.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>
