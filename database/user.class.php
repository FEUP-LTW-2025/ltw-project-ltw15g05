<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }
}

class User {
    public int $id;
    public string $name;
    public string $username;
    public ?string $email;
    public array $roles;

    public function __construct(int $id, string $name, string $username, ?string $email = null, array $roles = []) {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->roles = $roles;
    }

    public static function create($name, $username, $password, $email = '') {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();

        if (!$tableExists) {
            $db->exec("
                CREATE TABLE users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    name TEXT NOT NULL,
                    email TEXT UNIQUE,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
        } else {
            $result = $db->query("PRAGMA table_info(users)");
            $emailColumnExists = false;

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($row['name'] === 'email') {
                    $emailColumnExists = true;
                    break;
                }
            }

            if (!$emailColumnExists) {
                $db->exec("ALTER TABLE users ADD COLUMN email TEXT");
            }
        }

        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception('Username already exists');
        }

        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Please enter a valid email address');
            }

            $valid_domains = ['@gmail.com', '@hotmail.com', '@outlook.com', '@yahoo.com', '@icloud.com', '@protonmail.com', '@mail.com'];
            $valid_email = false;

            foreach ($valid_domains as $domain) {
                if (str_ends_with(strtolower($email), $domain)) {
                    $valid_email = true;
                    break;
                }
            }

            if (!$valid_email) {
                throw new Exception('Please use a valid email domain (gmail.com, hotmail.com, outlook.com, etc)');
            }

            $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }

            $stmt = $db->prepare('INSERT INTO users (name, username, password, email) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), $email]);
        } else {
            $stmt = $db->prepare('INSERT INTO users (name, username, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT)]);
        }

        return $db->lastInsertId();
    }

    public static function get_user_by_username_password(string $username, string $password): ?array {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        throw new Exception('Invalid username or password');
    }


    public static function get_user_by_id(int $id){
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $user['roles'] = self::getUserRoles($id);
        }

        return $user;
    }

    public static function getUserRoles(int $userId) {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT r.name FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ?
        ');
        $stmt->execute([$userId]);

        $roles = [];
        while ($role = $stmt->fetchColumn()) {
            $roles[] = $role;
        }

        return $roles;
    }

    public static function addRole(int $userId, string $roleName) {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='roles'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();

        if (!$tableExists) {
            $db->exec("
                CREATE TABLE roles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT UNIQUE NOT NULL CHECK(name IN ('freelancer', 'client', 'admin'))
                )
            ");
        }

        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='user_roles'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();

        if (!$tableExists) {
            $db->exec("
                CREATE TABLE user_roles (
                    user_id INTEGER NOT NULL,
                    role_id INTEGER NOT NULL,
                    PRIMARY KEY (user_id, role_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
                )
            ");
        }

        $roleId = self::ensureRoleExists($roleName);

        $stmt = $db->prepare('SELECT COUNT(*) FROM user_roles WHERE user_id = ? AND role_id = ?');
        $stmt->execute([$userId, (int)$roleId]);
        if ($stmt->fetchColumn() > 0) {
            return;
        }

        $stmt = $db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
        $stmt->execute([$userId, (int)$roleId]);
    }

    private static function ensureRoleExists(string $roleName) {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute([$roleName]);
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            if (!in_array($roleName, ['freelancer', 'client', 'admin'])) {
                throw new Exception('Invalid role name');
            }

            $stmt = $db->prepare('INSERT INTO roles (name) VALUES (?)');
            $stmt->execute([$roleName]);
            $roleId = (int)$db->lastInsertId();
        }

        return (int)$roleId;
    }

    public static function updateProfile($id, $name, $username, $currentPassword = '', $newPassword = '', $email = '') {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception('User not found');
        }

        if ($username !== $user['username']) {
            $stmt = $db->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                throw new Exception('Username already exists');
            }
        }

        $isPasswordValid = false;

        if (strlen($user['password']) === 40) {
            $isPasswordValid = (sha1($currentPassword) === $user['password']);
        } else {
            $isPasswordValid = password_verify($currentPassword, $user['password']);
        }

        if (!$isPasswordValid) {
            throw new Exception('Current password is incorrect');
        }

        if (!empty($email) && $email !== ($user['email'] ?? '')) {
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }
        }

        if (!empty($newPassword) && !empty($email)) {
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, password = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $username, password_hash($newPassword, PASSWORD_DEFAULT), $email, $id]);
        } elseif (!empty($newPassword)) {
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $username, password_hash($newPassword, PASSWORD_DEFAULT), $id]);
        } elseif (!empty($email)) {
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $username, $email, $id]);
        } else {
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ? WHERE id = ?');
            $stmt->execute([$name, $username, $id]);
        }

        return self::get_user_by_id($id);
    }
}
?>