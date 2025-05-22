<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/transaction.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

// Check if the user is logged in
if (!$currentUser) {
    header('Location: form_login.php');
    exit();
}

// Determine which user profile to display
// If an ID is provided in the URL and the current user is an admin, show that user's profile
// Otherwise, show the current user's own profile
$profileUserId = isset($_GET['id']) && in_array('admin', $currentUser['roles']) 
    ? (int)$_GET['id'] 
    : (int)$currentUser['id'];
    
// For debugging
error_log("Current user ID: " . $currentUser['id'] . ", Profile user ID: " . $profileUserId);

// Get the user data for the profile we're viewing
$userData = $profileUserId === $currentUser['id'] 
    ? $currentUser 
    : User::get_user_by_id($profileUserId);

// If the user doesn't exist, redirect to the user's own profile
if (!$userData) {
    header('Location: profile.php');
    exit();
}

// Get any success or error messages from query parameters
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
/*
// Load user's services if they are a freelancer
$services = [];
$clientTransactions = [];
$freelancerTransactions = [];
$totalEarnings = 0;
$completedOrders = 0;
$pendingOrders = 0;
$conversations = [];


if (in_array('freelancer', $userData['roles'])) {
    $services = Service::getByFreelancerId((int)$userData['id']);
    $freelancerTransactions = Transaction::getByFreelancerId((int)$userData['id']);
    
    // Calculate statistics
    foreach ($freelancerTransactions as $transaction) {
        if ($transaction['status'] === 'completed') {
            $totalEarnings += $transaction['payment_amount'];
            $completedOrders++;
        } elseif ($transaction['status'] === 'pending' || $transaction['status'] === 'in_progress') {
            $pendingOrders++;
        }
    }
}

// Load client transactions
$clientTransactions = Transaction::getByClientId((int)$userData['id']);
*/
// Load conversations
// This would be implemented in a Message class
// $conversations = Message::getUserConversations($userData['id']);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');

// Debug info
$isViewingOtherProfile = $profileUserId !== $currentUser['id'];
error_log("Is viewing other profile: " . ($isViewingOtherProfile ? 'Yes' : 'No'));

drawHeader(true, $currentUser);
drawProfile($userData, $isViewingOtherProfile, $currentUser);
drawFooter();