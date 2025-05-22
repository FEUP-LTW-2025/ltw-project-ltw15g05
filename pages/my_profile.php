<?php
// This file is a direct version of profile.php to ensure the Edit Profile button works

declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/transaction.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');

$session = Session::getInstance();
$currentUser = $session->getUser();

// Check if the user is logged in
if (!$currentUser) {
    header('Location: form_login.php');
    exit();
}

// Process success/error messages
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

drawHeader(true, $currentUser);
?>

<section class="profile-section">
    <div class="container">
        <?php if ($successMessage): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                <?= $successMessage ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>
    
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <h1><?= htmlspecialchars($currentUser['name']) ?></h1>
                <p>@<?= htmlspecialchars($currentUser['username']) ?></p>
                
                <div class="profile-roles">
                    <?php if (in_array('freelancer', $currentUser['roles'])): ?>
                        <span class="role-badge freelancer">Freelancer</span>
                    <?php endif; ?>
                    
                    <span class="role-badge client">Client</span>
                    
                    <?php if (in_array('admin', $currentUser['roles'])): ?>
                        <span class="role-badge admin">Admin</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="profile-body">
                <div class="profile-detail">
                    <span class="detail-label">Account Created</span>
                    <span class="detail-value"><?= date('F j, Y', strtotime($currentUser['created_at'])) ?></span>
                </div>
                <div class="profile-detail">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= htmlspecialchars($currentUser['email'] ?? 'Not provided') ?></span>
                </div>
                
                <div class="profile-actions">
                    <!-- This button should always show for users on their own profile -->
                    <a href="edit_profile.php" class="btn btn-outline">Edit Profile</a>
                    
                    <?php if (!in_array('freelancer', $currentUser['roles'])): ?>
                        <form action="../actions/action_become_freelancer.php" method="post">
                            <button type="submit" class="btn btn-primary">Become a Freelancer</button>
                        </form>
                    <?php else: ?>
                        <a href="new_service.php" class="btn btn-primary">Create New Service</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
drawFooter();
?>
