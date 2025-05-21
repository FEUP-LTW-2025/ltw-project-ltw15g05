<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

function init_roles() {
    $db = Database::getInstance();
    
    try {
        // Check if roles table exists
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
            
            echo "Roles table created successfully.\n";
        }
        
        // Insert default roles if they don't exist
        $roles = ['freelancer', 'client', 'admin'];
        
        foreach ($roles as $role) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM roles WHERE name = ?");
            $stmt->execute([$role]);
            $exists = $stmt->fetchColumn();
            
            if (!$exists) {
                $stmt = $db->prepare("INSERT INTO roles (name) VALUES (?)");
                $stmt->execute([$role]);
                echo "Role '{$role}' added.\n";
            }
        }
        
        return true;
    } catch (Exception $e) {
        echo "Error initializing roles: " . $e->getMessage() . "\n";
        return false;
    }
}

// Call the function to initialize roles
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    init_roles();
}
?>
