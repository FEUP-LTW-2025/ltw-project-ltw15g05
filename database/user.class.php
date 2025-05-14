<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

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
    
        $user = $stmt->fetch();
    
        if (!$user) {
            throw new Exception('Invalid username or password.');
        }
    
        return $user;
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
        
        // First ensure the role exists in the roles table
        $roleId = self::ensureRoleExists($roleName);
        
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
    
    private static function ensureRoleExists(string $roleName) {
        $db = Database::getInstance();
        
        // Get role ID
        $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute([$roleName]);
        $roleId = $stmt->fetchColumn();
        
        // If role doesn't exist, create it
        if (!$roleId) {
            // Check if the role name is valid (based on CHECK constraint in schema)
            if (!in_array($roleName, ['freelancer', 'client', 'admin'])) {
                throw new Exception('Invalid role name');
            }
            
            $stmt = $db->prepare('INSERT INTO roles (name) VALUES (?)');
            $stmt->execute([$roleName]);
            $roleId = (int)$db->lastInsertId();
        }
        
        return $roleId;
    }
    
    public static function updateProfile($id, $name, $username, $currentPassword = '', $newPassword = '') {
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
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $username, password_hash($newPassword, PASSWORD_DEFAULT), $id]);
        } else {
            // Update user without changing password
            $stmt = $db->prepare('UPDATE users SET name = ?, username = ? WHERE id = ?');
            $stmt->execute([$name, $username, $id]);
        }
        
        return self::get_user_by_id($id);
    }
}