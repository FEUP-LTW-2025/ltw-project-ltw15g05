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
            
            <div class="dashboard-content">
                <!-- Dashboard content can be added here -->
                <div class="card mt-3">
                    <h2>Your Flashcards</h2>
                    <p>Start creating and reviewing your flashcards to improve your learning experience.</p>
                    <div class="btn-group mt-2">
                        <a href="#" class="btn btn-primary">Create New Deck</a>
                        <a href="#" class="btn btn-outline">View All Decks</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>