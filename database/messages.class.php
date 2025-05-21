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
    
        $stmt = $db->prepare('
            SELECT u.id, u.name, u.username,
                   m.content AS last_message_content,
                   MAX(m.sent_at) AS last_message_time
            FROM (
                SELECT sender_id AS user_id, MAX(sent_at) AS last_message_time
                FROM messages
                WHERE receiver_id = ?
                GROUP BY sender_id
                UNION
                SELECT receiver_id AS user_id, MAX(sent_at) AS last_message_time
                FROM messages
                WHERE sender_id = ?
                GROUP BY receiver_id
            ) AS last_chats
            JOIN users u ON u.id = last_chats.user_id
            JOIN messages m ON (
                ((m.sender_id = ? AND m.receiver_id = u.id) OR (m.sender_id = u.id AND m.receiver_id = ?))
                AND m.sent_at = last_chats.last_message_time
            )
            GROUP BY u.id
            ORDER BY last_message_time DESC
        ');
    
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
