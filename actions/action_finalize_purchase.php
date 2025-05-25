<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/purchase.class.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/transaction.class.php');
require_once(__DIR__ . '/../database/init_database.php');

ob_start();
$db = Database::getInstance();
ensure_purchases_table_exists($db);
ensure_transactions_table_structure($db);
ob_end_clean();

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    header('Location: ../pages/form_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = $_POST['service_id'] ?? null;
    $paymentMethod = $_POST['payment_method'] ?? null;

    $validPaymentMethods = ['mbway', 'credit_card', 'paypal'];
    if (!$serviceId || !$paymentMethod || !in_array($paymentMethod, $validPaymentMethods)) {
        $session->addMessage('error', 'Invalid service or payment method.');
        header('Location: ../pages/checkout.php?service_id=' . $serviceId);
        exit();
    }

    try {
        $service = Service::getService((int)$serviceId);
        if (!$service) {
            throw new Exception('Service not found.');
        }

        if (!Purchase::createPurchase((int)$user['id'], (int)$serviceId, $paymentMethod)) {
            throw new Exception('Failed to insert purchase into database.');
        }

        $transactionId = Transaction::create(
            (int)$serviceId,
            (int)$user['id'],
            (int)$service->freelancer_id,
            (float)$service->price,
            ''
        );

        if (!$transactionId) {
            throw new Exception('Failed to create transaction record.');
        }

        $session->addMessage('success', 'Purchase completed successfully.');
    } catch (Exception $e) {
        error_log('Error finalizing purchase: ' . $e->getMessage());
        $session->addMessage('error', 'Error finalizing purchase: ' . $e->getMessage());
    }

    header('Location: ../pages/profile.php');
    exit();
}
?>