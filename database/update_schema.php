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
        
        // Add the column if it doesn't exist - without UNIQUE constraint initially
        if (!$columnExists) {
            try {
                // First add the column without UNIQUE constraint
                $db->exec("ALTER TABLE users ADD COLUMN email TEXT");
                echo "Email column added successfully to users table.\n";
                
                // Now create a temporary table with the desired schema
                $db->exec("CREATE TABLE users_temp (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    name TEXT NOT NULL,
                    email TEXT UNIQUE,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");
                
                // Copy data from the original table to the temp table
                $db->exec("INSERT INTO users_temp SELECT * FROM users");
                
                // Drop the original table
                $db->exec("DROP TABLE users");
                
                // Rename the temp table to the original name
                $db->exec("ALTER TABLE users_temp RENAME TO users");
                
                echo "Table restructured with email as UNIQUE constraint.\n";
            } catch (PDOException $e) {
                // If the above fails, just keep the column without UNIQUE constraint
                echo "Could not add UNIQUE constraint: " . $e->getMessage() . "\n";
                echo "Email column added but without UNIQUE constraint.\n";
            }
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
