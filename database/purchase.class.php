<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

class Purchase {
    public static function createPurchase(int $userId, int $serviceId, string $paymentMethod): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO purchases (user_id, service_id, purchase_date, payment_method) 
            VALUES (?, ?, CURRENT_TIMESTAMP, ?)
        ');
        return $stmt->execute([$userId, $serviceId, $paymentMethod]);
    }

    public static function getUserPurchases(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT 
                purchases.purchase_date,
                purchases.payment_method,
                services.title,
                services.price,
                services.description
            FROM purchases
            JOIN services ON purchases.service_id = services.id
            WHERE purchases.user_id = ?
            ORDER BY purchases.purchase_date DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>