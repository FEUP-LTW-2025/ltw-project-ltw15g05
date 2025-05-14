<?php
declare(strict_types=1);
?>

<?php function drawFrontPage() { ?>
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="../css/frontpage.css">
</head>
<body>
    <div class="front-page">
        <div class="left-section">
            <h1>Welcome to</h1>
            <p class="hero-subtitle">Where Photographers & Clients Connect.</p>
            <img src="../images/logo_short.png" alt="Logo" class="logo">
            <div class="auth-buttons">
                <a href="../pages/form_login.php" class="btn btn-login">Login</a>
                <a href="../pages/form_register.php" class="btn btn-signup">Sign Up</a>
            </div>
        </div>

        <div class="right-section">
            <p>
                Discover a vibrant platform where talented photographers connect with clients looking for stunning visual content.
            </p>
    </div>
</body>
<?php } ?>
