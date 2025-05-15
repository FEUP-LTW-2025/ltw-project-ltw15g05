<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

// Add the email column to the users table if it doesn't exist
function addEmailColumnIfNotExists() {
    try {
        $db = Database::getInstance();
        
        // Check if email column exists
        $result = $db->query("PRAGMA table_info(users)");
        $columnExists = false;
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['name'] === 'email') {
                $columnExists = true;
                break;
            }
        }
        
        // Add the column if it doesn't exist
        if (!$columnExists) {
            $db->exec("ALTER TABLE users ADD COLUMN email TEXT UNIQUE");
            echo "Email column added successfully to users table.\n";
        } else {
            echo "Email column already exists in users table.\n";
        }
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
}

// Run the update
addEmailColumnIfNotExists();

echo "Database schema update completed.\n";
?>
