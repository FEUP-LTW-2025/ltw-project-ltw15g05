<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

// This script initializes the database with the base schema from project_db.sql
// and runs any necessary migrations

function initialize_database() {
    try {
        $db = Database::getInstance();
        
        // Check if users table already exists
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        $stmt->execute();
        $table_exists = $stmt->fetchColumn();
        
        if (!$table_exists) {
            echo "Creating database schema...\n";
            
            // Read and execute the SQL schema
            $sql = file_get_contents(__DIR__ . '/project_db.sql');
            $db->exec($sql);
            
            echo "Database schema created successfully.\n";
        } else {
            echo "Database schema already exists.\n";
        }
        
        // Run migrations for any additional tables if needed
        require_once(__DIR__ . '/migrate_service_images.php');
        if (run_migration()) {
            echo "Service images migration completed.\n";
        }
        
        // Add email column if needed
        require_once(__DIR__ . '/update_schema.php');
        
        return true;
    } catch (PDOException $e) {
        echo "Error initializing database: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run initialization if this script is executed directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    if (initialize_database()) {
        echo "Database initialization completed successfully.\n";
    } else {
        echo "Database initialization failed.\n";
    }
}
?>
