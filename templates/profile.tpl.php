<?php
declare(strict_types=1);
?>

<?php function drawProfile(array $userData) { ?>
    <h1>You are logged in, <?= $userData['name'] ?></h1>
    <p>Username: <?= $userData['username'] ?></p>
    <p>account creation date: <?= $userData['created_at'] ?></p>
    <a href="../actions/action_logout.php">Logout</a>
<?php } ?>