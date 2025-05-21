<?php

declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/favorites.class.php');

$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: ../pages/form_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;

    if (!$service_id) {
        $session->addMessage('error', 'Invalid service.');
        header('Location: ../pages/main.php');
        exit();
    }

    try {
        Favorites::addToFavorites((int)$userData['id'], (int)$service_id);
        $session->addMessage('success', 'Service added to favorites.');
    } catch (Exception $e) {
        error_log('Error adding service to favorites: ' . $e->getMessage()); // Log do erro
        $session->addMessage('error', 'Error adding service to favorites: ' . $e->getMessage());
    }

    header('Location: ../pages/main.php');
    exit();
}
?>