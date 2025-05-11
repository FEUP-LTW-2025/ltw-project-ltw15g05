<?php
declare(strict_types=1);
?>

<?php function drawFrontPage() { ?>
    <div class="front-page">
        <h1>Welcome to FlashMe</h1>
        <div class="auth-buttons">
            <a href="../pages/form_login.php" class="btn btn-login">Login</a>
            <a href="../pages/form_register.php" class="btn btn-signup">Sign Up</a>
        </div>
    </div>
<?php } ?>