<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

// Check if the user is logged in
if (!$currentUser) {
    header('Location: form_login.php');
    exit();
}

// Get profile user ID
$profileUserId = isset($_GET['id']) && in_array('admin', $currentUser['roles']) 
    ? (int)$_GET['id'] 
    : (int)$currentUser['id'];

// Calculate $isViewingOtherProfile
$isViewingOtherProfile = ($profileUserId !== $currentUser['id']);

echo "<h2>Debug Profile Information</h2>";
echo "<p>Current user ID: " . $currentUser['id'] . "</p>";
echo "<p>Profile user ID: " . $profileUserId . "</p>";
echo "<p>Is viewing other profile? " . ($isViewingOtherProfile ? 'Yes' : 'No') . "</p>";
echo "<p>Current user roles: " . implode(", ", $currentUser['roles']) . "</p>";

echo "<p><a href='profile.php'>Go back to profile</a></p>";
?>
