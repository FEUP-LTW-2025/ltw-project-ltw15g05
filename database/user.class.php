<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class User {
    public int $id;
    public string $name;
    public string $username;

    public function __construct(int $id, string $name, string $username) {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
    }

    public static function create($name, $username, $password) {
        $db = Database::getInstance();
        
        
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception('Username already exists');
        }
        
        $stmt = $db->prepare('INSERT INTO users (name, username, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $username, sha1($password)]);
        return $db->lastInsertId();
    }

    public static function get_user_by_username_password($username, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $stmt->execute([$username, sha1($password)]);

        return $stmt->fetch();
    }

    public static function get_user_by_id(int $id){
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}