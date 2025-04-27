<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');

class Session {
    private static ?Session $instance = null;

    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function __construct() {
        session_start();
    }

    public function login(int $userId): void {
        $_SESSION['user_id'] = $userId;
    }
    
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUser(): ?array {
        if (!$this->getUserId()) {
            return null;
        }
        return User::get_user_by_id($this->getUserId());
    
    }

    public function isLoggedIn(): bool {
        return $this->getUserId() !== null;
    }

    public function logout() {
        session_destroy();
    }
}
?>