<?php
declare(strict_types=1);

// This script directly injects an admin user into the database
require_once(__DIR__ . '/../includes/database.php');

function inject_admin_user() {
    $db = Database::getInstance();
    
    try {
        echo "Starting admin user injection...\n";
        
        // 1. Check if admin_actions table exists
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='admin_actions'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        if (!$tableExists) {
            echo "Creating admin_actions table...\n";
            
            // Create the admin_actions table
            $db->exec("
                CREATE TABLE admin_actions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    admin_id INTEGER NOT NULL,
                    action TEXT NOT NULL,
                    target_user_id INTEGER,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            
            echo "Admin actions table created successfully.\n";
        }
        
        // 2. Check if roles table exists
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='roles'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        if (!$tableExists) {
            echo "Creating roles table...\n";
            
            // Create the roles table
            $db->exec("
                CREATE TABLE roles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT UNIQUE NOT NULL CHECK(name IN ('freelancer', 'client', 'admin'))
                )
            ");
            
            // Insert default roles
            $db->exec("INSERT INTO roles (name) VALUES ('freelancer')");
            $db->exec("INSERT INTO roles (name) VALUES ('client')");
            $db->exec("INSERT INTO roles (name) VALUES ('admin')");
            
            echo "Roles table created and populated successfully.\n";
        }
        
        // 3. Check if user_roles table exists
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='user_roles'");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        if (!$tableExists) {
            echo "Creating user_roles table...\n";
            
            // Create the user_roles table
            $db->exec("
                CREATE TABLE user_roles (
                    user_id INTEGER NOT NULL,
                    role_id INTEGER NOT NULL,
                    PRIMARY KEY (user_id, role_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
                )
            ");
            
            echo "User roles table created successfully.\n";
        }
        
        // 4. Check if admin user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->fetchColumn();
        
        if (!$adminExists) {
            echo "Creating admin user...\n";
            
            // Create the admin user directly with a valid email
            $hashedPassword = password_hash('Admin123!', PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (name, username, password, email, created_at) VALUES (?, ?, ?, ?, datetime("now"))');
            $stmt->execute(['Administrator', 'admin', $hashedPassword, 'admin@gmail.com']);
            
            $adminId = $db->lastInsertId();
            
            // Get the admin role ID
            $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
            $stmt->execute(['admin']);
            $adminRoleId = $stmt->fetchColumn();
            
            if (!$adminRoleId) {
                echo "Creating admin role...\n";
                $stmt = $db->prepare('INSERT INTO roles (name) VALUES (?)');
                $stmt->execute(['admin']);
                $adminRoleId = $db->lastInsertId();
            }
            
            // Assign admin role to the user
            $stmt = $db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
            $stmt->execute([$adminId, $adminRoleId]);
            
            echo "Admin user created successfully!\n";
            echo "Username: admin\n";
            echo "Password: Admin123!\n";
        } else {
            echo "Admin user already exists.\n";
        }
        
        return true;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Execute the injection
inject_admin_user();
echo "Done!\n";
