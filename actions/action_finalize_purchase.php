<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/purchase.class.php');

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    header('Location: ../pages/form_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = $_POST['service_id'] ?? null;
    $paymentMethod = $_POST['payment_method'] ?? null;

    // Validar o método de pagamento
    $validPaymentMethods = ['mbway', 'credit_card', 'paypal'];
    if (!$serviceId || !$paymentMethod || !in_array($paymentMethod, $validPaymentMethods)) {
        $session->addMessage('error', 'Invalid service or payment method.');
        header('Location: ../pages/checkout.php?service_id=' . $serviceId);
        exit();
    }

    try {
        // Registrar a compra no banco de dados
        if (!Purchase::createPurchase((int)$user['id'], (int)$serviceId, $paymentMethod)) {
            throw new Exception('Failed to insert purchase into database.');
        }
        $session->addMessage('success', 'Purchase completed successfully.');
    } catch (Exception $e) {
        error_log('Error finalizing purchase: ' . $e->getMessage());
        $session->addMessage('error', 'Error finalizing purchase.');
    }

    header('Location: ../pages/profile.php');
    exit();
}
?>