<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.php');

class Favorites {
    public static function addToFavorites(int $user_id, int $service_id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('INSERT INTO favorites (user_id, service_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $service_id]);
    }

    public static function getFavoriteItems(int $user_id): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT services.* 
            FROM favorites
            JOIN services ON favorites.service_id = services.id
            WHERE favorites.user_id = ?
        ');
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function removeFromFavorites(int $user_id, int $service_id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = ? AND service_id = ?');
        $stmt->execute([$user_id, $service_id]);
    }
}