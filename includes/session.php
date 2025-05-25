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
        $user = User::get_user_by_id($this->getUserId());
        return $user !== false ? $user : null; 
    }

    public function isLoggedIn(): bool {
        return $this->getUserId() !== null;
    }

    public function logout() {
        session_destroy();
    }
    
    public function addMessage(string $type, string $content): void {
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = [];
        }
        $_SESSION['messages'][] = ['type' => $type, 'content' => $content];
    }
    
    public function getMessages(): array {
        $messages = $_SESSION['messages'] ?? [];
        $_SESSION['messages'] = []; 
        return $messages;
    }

    public function updateUser(array $userData): void {
        if (!$this->isLoggedIn()) {
            return;
        }
        
        $_SESSION['user_data'] = $userData;
    }
}
?>