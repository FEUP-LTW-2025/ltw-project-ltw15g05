<?php
declare(strict_types=1);

// This script applies the database schema updates

require_once(__DIR__ . '/../includes/database.php');

try {
    $db = Database::getInstance();
    
    // Read the SQL file content
    $sqlContent = file_get_contents(__DIR__ . '/update_schema.sql');
    
    // Execute the SQL statements
    $db->exec($sqlContent);
    
    echo "Database schema updated successfully.\n";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
    exit(1);
}
?>
