<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.php');

class Cart {
    public static function addToCart(int $user_id, int $service_id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('INSERT INTO cart (user_id, service_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $service_id]);
    }

    public static function getCartItems(int $user_id): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT services.* 
            FROM cart
            JOIN services ON cart.service_id = services.id
            WHERE cart.user_id = ?
        ');
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function removeFromCart(int $user_id, int $service_id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM cart WHERE user_id = ? AND service_id = ?');
        $stmt->execute([$user_id, $service_id]);
    }
}