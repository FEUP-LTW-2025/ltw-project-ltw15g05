<?php
declare(strict_types=1);
?>

<?php function drawProfile(array $userData) { ?>
    <section class="profile-section">
        <div class="container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($userData['name'], 0, 1)) ?>
                    </div>
                    <h1><?= htmlspecialchars($userData['name']) ?></h1>
                    <p>@<?= htmlspecialchars($userData['username']) ?></p>
                </div>
                <div class="profile-body">
                    <div class="profile-detail">
                        <span class="detail-label">Account Created</span>
                        <span class="detail-value"><?= date('F j, Y', strtotime($userData['created_at'])) ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?= htmlspecialchars($userData['email'] ?? 'Not provided') ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Role</span>
                        <span class="detail-value">Standard User</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>