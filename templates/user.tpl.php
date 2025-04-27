<?php
declare(strict_types=1);
?>

<?php function drawRegisterForm() { ?>
    <h2>Register</h2>
    <form action="../actions/action_register.php" method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="form_login.php">Login</a></p>
<?php } ?>

<?php function drawLoginForm() { ?>
    <h2>Login</h2>
    <form action="../actions/action_login.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="form_register.php">Register</a></p>
<?php } ?>