<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

// This script creates the service_images table if it doesn't exist

function run_migration() {
    $db = Database::getInstance();
    
    try {
        // Check if service_images table exists
        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='service_images'");
        $stmt->execute();
        $table_exists = $stmt->fetchColumn();
        
        if (!$table_exists) {
            // Create the table
            $db->exec("
                CREATE TABLE service_images (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    service_id INTEGER NOT NULL,
                    image_path TEXT NOT NULL,
                    is_primary BOOLEAN DEFAULT 0,
                    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
                )
            ");
            
            echo "Created service_images table successfully.\n";
        } else {
            echo "service_images table already exists.\n";
        }
        
        return true;
    } catch (PDOException $e) {
        echo "Error creating service_images table: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run the migration if this script is executed directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    if (run_migration()) {
        echo "Migration completed successfully.\n";
    } else {
        echo "Migration failed.\n";
    }
}
?>
