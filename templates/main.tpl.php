<?php
declare(strict_types=1);
?>

<?php function drawMainPage(array $userData) { ?>
    <section class="dashboard">
        <div class="container">
            <div class="welcome-card">
                <h1 class="welcome-title">Welcome back, <?= htmlspecialchars($userData['name']) ?>!</h1>
                <p>You're now logged in to your FlashMe dashboard.</p>
            </div>
            
            
        </div>
    </section>
<?php } ?>