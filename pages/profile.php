<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/transaction.class.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}

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

// Load conversations
// This would be implemented in a Message class
// $conversations = Message::getUserConversations($userData['id']);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');

drawHeader();
drawProfile($userData);
drawFooter();