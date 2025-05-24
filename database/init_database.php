<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

// This script initializes the database with the base schema from project_db.sql
// and runs any necessary migrations

function ensure_purchases_table_exists($db) {
    // Check if purchases table already exists
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='purchases'");
    $table_exists = $stmt->fetchColumn();
    
    if (!$table_exists) {
        // Create purchases table
        $sql = "
        CREATE TABLE IF NOT EXISTS purchases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            service_id INTEGER NOT NULL,
            purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            payment_method TEXT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (service_id) REFERENCES services(id)
        )";
        $db->exec($sql);
        echo "Purchases table created.\n";
    }
}

function ensure_transactions_table_structure($db) {
    // Check if the transactions table exists
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='transactions'");
    $table_exists = $stmt->fetchColumn();
    
    if ($table_exists) {
        // Check if it has all needed columns
        $columns = [];
        $stmt = $db->query("PRAGMA table_info(transactions)");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[$row['name']] = $row;
        }
        
        // If any required column is missing, recreate the table
        if (!isset($columns['custom_requirements']) || !isset($columns['completed_at'])) {
            // Create a new temp table with the correct schema
            $db->exec("
            CREATE TABLE transactions_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                service_id INTEGER NOT NULL,
                client_id INTEGER NOT NULL,
                freelancer_id INTEGER NOT NULL,
                status TEXT CHECK(status IN ('pending', 'in_progress', 'completed', 'canceled')) NOT NULL DEFAULT 'pending',
                payment_amount REAL NOT NULL,
                custom_requirements TEXT DEFAULT '',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME DEFAULT NULL,
                FOREIGN KEY (service_id) REFERENCES services(id),
                FOREIGN KEY (client_id) REFERENCES users(id),
                FOREIGN KEY (freelancer_id) REFERENCES users(id)
            )");
            
            // Copy existing data
            $db->exec("
            INSERT INTO transactions_new 
                (id, service_id, client_id, freelancer_id, status, payment_amount, created_at) 
            SELECT 
                id, service_id, client_id, freelancer_id, status, payment_amount, created_at 
            FROM transactions
            ");
            
            // Drop the old table and rename the new one
            $db->exec("DROP TABLE transactions");
            $db->exec("ALTER TABLE transactions_new RENAME TO transactions");
            
            echo "Transactions table structure updated.\n";
        }
    }
}

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
        
        // Make sure purchases table exists and has correct structure
        ensure_purchases_table_exists($db);
        
        // Make sure transactions table has the required columns
        ensure_transactions_table_structure($db);
        
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
