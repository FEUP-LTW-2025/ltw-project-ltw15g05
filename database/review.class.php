<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

class Review {
    public static function addReview(int $userId, int $serviceId, int $freelancerId, int $rating, string $comment): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            INSERT INTO reviews (client_id, service_id, freelancer_id, rating, comment, created_at)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');
        $stmt->execute([$userId, $serviceId, $freelancerId, $rating, $comment]);
    }

    public static function getReviewsByService(int $serviceId): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT r.*, u.username AS client_name
            FROM reviews r
            JOIN users u ON r.client_id = u.id
            WHERE r.service_id = ?
            ORDER BY r.created_at DESC
        ');
        $stmt->execute([$serviceId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getFreelancerAverageRating($freelancerId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT AVG(r.rating) AS average_rating, COUNT(r.id) AS total_reviews
            FROM reviews r
            INNER JOIN services s ON r.service_id = s.id
            WHERE s.freelancer_id = ?
        ");
        $stmt->execute([$freelancerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>