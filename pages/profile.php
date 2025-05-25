<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/transaction.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

if (!$currentUser) {
    header('Location: form_login.php');
    exit();
}


$profileUserId = isset($_GET['id']) && in_array('admin', $currentUser['roles']) 
    ? (int)$_GET['id'] 
    : (int)$currentUser['id'];
    
error_log("Current user ID: " . $currentUser['id'] . " (type: " . gettype($currentUser['id']) . 
         "), Profile user ID: " . $profileUserId . " (type: " . gettype($profileUserId) . ")");

$userData = $profileUserId === (int)$currentUser['id'] 
    ? $currentUser 
    : User::get_user_by_id($profileUserId);

if (!$userData) {
    header('Location: profile.php');
    exit();
}

$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

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
    
    foreach ($freelancerTransactions as $transaction) {
        if ($transaction['status'] === 'completed') {
            $totalEarnings += $transaction['payment_amount'];
            $completedOrders++;
        } elseif ($transaction['status'] === 'pending' || $transaction['status'] === 'in_progress') {
            $pendingOrders++;
        }
    }
}

$clientTransactions = Transaction::getByClientId((int)$userData['id']);
$isViewingOtherProfile = $profileUserId !== (int)$currentUser['id'];
error_log("Is viewing other profile: " . ($isViewingOtherProfile ? 'Yes' : 'No'));

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');

drawHeader(true, $currentUser);
drawProfile($userData, $isViewingOtherProfile, $currentUser);
drawFooter();