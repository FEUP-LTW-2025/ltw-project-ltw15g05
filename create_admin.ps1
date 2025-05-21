# Create admin account script for Windows
# Run this script from the root directory of the application

# Ensure PHP is in the PATH or provide full path to PHP
$phpPath = "php"

# Check if PHP exists
try {
    $phpVersion = & $phpPath --version
    Write-Host "Using PHP:" $phpVersion
} catch {
    Write-Host "Error: PHP not found. Please make sure PHP is installed and in your PATH."
    Write-Host "You may need to specify the full path to PHP in this script."
    exit
}

# Run the admin injection script
Write-Host "Creating admin account..."
& $phpPath database/admin_inject.php

Write-Host "`nIf successful, you can now log in with:"
Write-Host "Username: admin"
Write-Host "Password: Admin123!"
Write-Host "`nPlease change this password immediately after logging in."
