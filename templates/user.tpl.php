<?php
declare(strict_types=1);
?>

<?php function drawLoginForm() { ?>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Enter your credentials to access your account</p>
            </div>
            <form action="../actions/action_login.php" method="post">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 13L9 17L19 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Sign In
                </button>
            </form>
            <p class="text-center mt-3" style="color: var(--gray); font-size: 0.875rem;">
                Don't have an account? <a href="form_register.php" style="color: var(--primary); font-weight: 500;">Create one</a>
            </p>
        </div>
    </div>
<?php } ?>

<?php function drawRegisterForm(array $messages = []) { ?>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Get started with your free account</p>
            </div>
            
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?>">
                        <?= $message['content'] ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <form action="../actions/action_register.php" method="POST">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="johndoe" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Create Account
                </button>
            </form>
            <p class="text-center mt-3" style="color: var(--gray); font-size: 0.875rem;">
                Already have an account? <a href="form_login.php" style="color: var(--primary); font-weight: 500;">Sign in</a>
            </p>
        </div>
    </div>
<?php } ?>