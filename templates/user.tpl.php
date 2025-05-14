<?php
declare(strict_types=1);
?>

<?php function drawLoginForm() { ?>
    <head>
        <link rel="stylesheet" href="../css/frontpage.css">
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
        <link rel="stylesheet" href="../css/frontpage.css">
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
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </section>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account?
            <a href="form_login.php">Sign In</a>
        </p>
>>>>>>> frontpages
    </div>

<?php } ?>