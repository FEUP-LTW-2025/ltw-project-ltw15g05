<?php
declare(strict_types=1);
?>

<?php function drawLoginForm() { ?>
    <head>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <div class="autenticação">
        <a href="../index.php">
            <img src="../images/logo_short.png" alt="Logo" class="logo">
        </a>
        <h1>Welcome Back</h1>
        <h2>Enter your credentials to access your account</h2>
        <form action="../actions/action_login.php" method="post">
                <?php if (isset($_GET['error'])): ?>
                    <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                <?php if (isset($_GET['message'])): ?>
                    <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
                <?php endif; ?>
            <section>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="username" required>
            </section>
            <section>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </section>
            <button type="submit">Sign In</button>
        </form>
        <p>Don't have an account?
            <a href="form_register.php">Create one</a>
        </p>
    </div>
    
<?php } ?>

<?php function drawRegisterForm() { ?>
    <head>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <div class="autenticação">
        <a href="../index.php">
            <img src="../images/logo_short.png" alt="Logo" class="logo">
        </a>
        <h1>Create an Account</h1>
        <h2>Get started with your account</h2>
        <form action="../actions/action_register.php" method="post">
            <section>
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe" required>
            </section>
            <section>
                <?php if (isset($_GET['error'])): ?>
                    <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="username" required>
            </section>
            <section>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                <small class="note">Email must end with a valid domain (e.g. @gmail.com, @hotmail.com)</small>
            </section>
            <section>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </section>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account?
            <a href="form_login.php">Sign In</a>
        </p>
    </div>

<?php } ?>

<?php function drawEditProfileForm($userData, $isAdminEdit = false, $currentUser = null) { ?>
    <head>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <div class="edit-profile-container">
        <?php if ($isAdminEdit): ?>
            <div class="admin-navigation" style="margin-bottom: 1rem;">
                <a href="../pages/admin.php" class="btn-secondary">← Back to Admin Panel</a>
            </div>
            <h1>Edit User Profile</h1>
            <h2>Admin editing: <?= htmlspecialchars($userData['name']) ?> (@<?= htmlspecialchars($userData['username']) ?>)</h2>
        <?php else: ?>
            <h1>Edit Profile</h1>
            <h2>Update your account information</h2>
        <?php endif; ?>
        
        <form action="../actions/action_edit_profile.php<?= $isAdminEdit ? '?id=' . $userData['id'] : '' ?>" method="post">
            <?php if (isset($_GET['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php endif; ?>
            
            <section>
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($userData['name']) ?>" required>
            </section>
            
            <section>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($userData['username']) ?>" required>
            </section>
            
            <section>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" placeholder="your.email@example.com">
                <small class="note">Email must end with a valid domain (e.g. @gmail.com, @hotmail.com)</small>
            </section>
            
            <?php if (!$isAdminEdit): ?>
            <section>
                <label for="current_password">Current Password (required for any changes)</label>
                <input type="password" id="current_password" name="current_password" placeholder="••••••••" required>
            </section>
            <?php else: ?>
            <input type="hidden" name="admin_edit" value="1">
            <?php endif; ?>
            
            <section>
                <label for="new_password">New Password (leave empty to keep current)</label>
                <input type="password" id="new_password" name="new_password" placeholder="••••••••">
            </section>
            
            <section>
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
            </section>
            
            <div class="button-group">
                <?php if ($isAdminEdit): ?>
                <a href="profile.php?id=<?= $userData['id'] ?>" class="btn-secondary">Cancel</a>
                <?php else: ?>
                <a href="profile.php" class="btn-secondary">Cancel</a>
                <?php endif; ?>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
<?php } ?>