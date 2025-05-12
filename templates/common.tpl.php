<?php
declare(strict_types=1);
?>

<?php function drawHeader(bool $isLoggedIn = false) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlashMe - Flashcard Learning</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header-footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../favicon.ico">
</head>
<body>
    <header class="site-header">
        <div class="header-left">
            <a href="../pages/main.php" class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="website-icon">
                    <path d="M12 6.25278V19.2528M12 6.25278C10.8324 5.47686 9.24649 5 7.5 5C5.75351 5 4.16756 5.47686 3 6.25278V19.2528C4.16756 18.4769 5.75351 18 7.5 18C9.24649 18 10.8324 18.4769 12 19.2528M12 6.25278C13.1676 5.47686 14.7535 5 16.5 5C18.2465 5 19.8324 5.47686 21 6.25278V19.2528C19.8324 18.4769 18.2465 18 16.5 18C14.7535 18 13.1676 18.4769 12 19.2528" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="website-title">FlashMe</span>
            </a>
        </div>
        
        <div class="header-right">
            <?php if ($isLoggedIn): ?>
                <a href="../pages/profile.php" class="profile-icon" title="View Profile">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </a>
                <a href="../actions/action_logout.php" class="btn btn-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 16L21 12M21 12L17 8M21 12H7M13 16V17C13 18.6569 11.6569 20 10 20H6C4.34315 20 3 18.6569 3 17V7C3 5.34315 4.34315 4 6 4H10C11.6569 4 13 5.34315 13 7V8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Logout
                </a>
            <?php else: ?>
                <a href="form_login.php" class="btn btn-outline">Login</a>
                <a href="form_register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>
    <main>
<?php } ?>

<?php function drawFooter() { ?>
    </main>
    
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <span class="copyright">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M15 9.5C14.0807 8.57972 12.8254 8 11.4615 8C8.97709 8 7 9.9533 7 12.3846C7 14.8159 8.97709 16.7692 11.4615 16.7692C12.8254 16.7692 14.0807 16.1895 15 15.2692" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <?php echo date('Y'); ?> FlashMe. All rights reserved.
                    </span>
                </div>
                <div class="footer-right">
                    <a href="../help" class="help-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 18V18.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 14.5V14C12 12.6193 13.1193 11.5 14.5 11.5C15.8807 11.5 17 12.6193 17 14C17 15.3807 15.8807 16.5 14.5 16.5H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M11 10C11.6667 8 13.5 7 15.5 8.5C17.5 10 16 12.5 14 11.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Help
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Add subtle button hover effects
        document.querySelectorAll('.btn, .profile-icon, .help-link').forEach(element => {
            element.addEventListener('mouseenter', () => {
                element.style.transform = 'translateY(-2px)';
                element.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            });
            element.addEventListener('mouseleave', () => {
                element.style.transform = 'translateY(0)';
                element.style.boxShadow = 'none';
            });
            
            // Add click effect for profile icon
            if (element.classList.contains('profile-icon')) {
                element.addEventListener('click', (e) => {
                    // Visual feedback on click
                    element.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        element.style.transform = 'translateY(-2px)';
                    }, 150);
                });
            }
        });
    </script>
</body>
</html>
<?php } ?>