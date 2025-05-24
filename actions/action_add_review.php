<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/review.class.php');
require_once(__DIR__ . '/../includes/database.php');

$session = Session::getInstance();
$userId = $session->getUserId();

if (!$userId) {
    header('Location: ../pages/form_login.php');
    exit();
}

$serviceId = $_POST['service_id'] ?? null;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$comment = trim($_POST['comment'] ?? '');

if (!$serviceId || $rating === null || $rating < 0 || $rating > 5 || empty($comment)) {
    header('Location: ../pages/service.php?id=' . urlencode($serviceId) . '&error=Invalid input');
    exit();
}

$db = Database::getInstance();
$stmt = $db->prepare('SELECT freelancer_id FROM services WHERE id = ?');
$stmt->execute([$serviceId]);
$freelancerId = $stmt->fetchColumn();

if (!$freelancerId) {
    header('Location: ../pages/service.php?id=' . urlencode($serviceId) . '&error=Freelancer not found');
    exit();
}

Review::addReview($userId, (int)$serviceId, $freelancerId, $rating, $comment);
header("Location: ../pages/service.php?id=" . urlencode($serviceId) . "&success=1");
exit();
?>