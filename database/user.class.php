<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class User {
    public int $id;
    public string $name;
    public string $username;
    public string $email;
    public array $roles;

    public function __construct(int $id, string $name, string $username, string $email = '', array $roles = []) {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->roles = $roles;
    }

    public static function create($name, $username, $password, $email = '') {
        $db = Database::getInstance();
        
        // Check if username already exists
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception('Username already exists');
        }
        
        // Check if email already exists (if provided)
        if (!empty($email)) {
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }
        }
        
        // Create the user with email field
        $stmt = $db->prepare('INSERT INTO users (name, username, password, email) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), $email]);
        
        // Set default role as client
        $userId = $db->lastInsertId();
        self::addRole($userId, 'client');
        
        return $userId;
    }

    public static function get_user_by_username_password($username, $password) {
        $db = Database::getInstance();
        // First get the user by username only
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        
        $user = $stmt->fetch();
        
        // If user exists, verify the password
        if ($user) {
            // For backward compatibility with sha1 passwords
            if (strlen($user['password']) === 40) { // SHA1 hash length is 40
                if (sha1($password) === $user['password']) {
                    // Upgrade the password hash
                    $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                    return $user;
                }
            } else {
                // Use PHP's built-in password verification
                if (password_verify($password, $user['password'])) {
                    // If password needs rehash due to stronger algorithm or cost
                    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
                        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                    }
                    return $user;
                }
            }
        }
        
        return false;
    }

    public static function get_user_by_id(int $id){
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Add user roles
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
        
        // Get role ID
        $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute([$roleName]);
        $roleId = $stmt->fetchColumn();
        
        if (!$roleId) {
            throw new Exception('Role does not exist');
        }
        
        // Check if user already has this role
        $stmt = $db->prepare('SELECT COUNT(*) FROM user_roles WHERE user_id = ? AND role_id = ?');
        $stmt->execute([$userId, $roleId]);
        if ($stmt->fetchColumn() > 0) {
            return; // User already has this role
        }
        
        // Assign role to user
        $stmt = $db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
        $stmt->execute([$userId, $roleId]);
    }
    
    public static function updateProfile($id, $name, $username, $email = '', $currentPassword = '', $newPassword = '') {
        $db = Database::getInstance();
        
        // First get the current user data
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // If changing username, check if new username is available
        if ($username !== $user['username']) {
            $stmt = $db->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                throw new Exception('Username already exists');
            }
        }
        
        // If email provided, check if it's available
        if (!empty($email) && $email !== $user['email']) {
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }
        }
        
        // If changing password, verify current password
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                throw new Exception('Current password is required');
            }
            
            // Verify current password
            $isPasswordValid = false;
            
            // For backward compatibility with sha1 passwords
            if (strlen($user['password']) === 40) { // SHA1 hash length
                $isPasswordValid = (sha1($currentPassword) === $user['password']);
            } else {
                $isPasswordValid = password_verify($currentPassword, $user['password']);
            }
            
            if (!$isPasswordValid) {
                throw new Exception('Current password is incorrect');
            }
            
            // Update user with new password
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $username, $email, password_hash($newPassword, PASSWORD_DEFAULT), $id]);
        } else {
            // Update user without changing password
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $username, $email, $id]);
        }
        
        return self::get_user_by_id($id);
    }
}