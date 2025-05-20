<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Messages {
    public static function getMessagesBetween($user1, $user2) {        
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY sent_at ASC
        ');
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sendMessage($senderId, $receiverId, $content) { 
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO messages (sender_id, receiver_id, content) 
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$senderId, $receiverId, $content]);
    }

    public static function getRecentChats(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                users.id,
                users.username,
                MAX(messages.sent_at) AS last_message_time
            FROM users
            JOIN messages ON users.id = CASE
                WHEN messages.sender_id = :userId THEN messages.receiver_id
                ELSE messages.sender_id
            END
            WHERE :userId IN (messages.sender_id, messages.receiver_id)
              AND users.id != :userId
            GROUP BY users.id, users.username
            ORDER BY last_message_time DESC
        ");
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
