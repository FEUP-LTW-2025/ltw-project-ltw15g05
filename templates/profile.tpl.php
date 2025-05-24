<?php
declare(strict_types=1);

/**
 * Format the timestamp for display in message list
 * @param string $timestamp The timestamp to format
 * @return string Formatted time string
 */
function formatMessageTime(string $timestamp): string {
    $messageTime = strtotime($timestamp);
    $now = time();
    $diff = $now - $messageTime;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . 'm ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) { // 7 days
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else {
        return date('M j', $messageTime);
    }
}
?>

<?php function drawProfile(array $userData, bool $isViewingOtherProfile = false, array $currentUser = null) { 
    $roles = $userData['roles'] ?? ['client'];
    $isFreelancer = in_array('freelancer', $roles);
    $isAdmin = in_array('admin', $roles);
    $viewerIsAdmin = $currentUser && in_array('admin', $currentUser['roles']);
    
    // Get any success or error messages
    $successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
    $errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
?>
    <section class="profile-section">
        <div class="container">
            <?php if ($successMessage): ?>
                <div class="alert alert-success" style="margin-bottom: 1rem;">
                    <?= $successMessage ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger" style="margin-bottom: 1rem;">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>
        
            <?php if ($isViewingOtherProfile && $viewerIsAdmin): ?>
                <div class="admin-navigation" style="margin-bottom: 1rem;">
                    <a href="../pages/admin.php" class="btn btn-outline btn-sm">← Back to Admin Panel</a>
                </div>
            <?php endif; ?>
            
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($userData['name'], 0, 1)) ?>
                    </div>
                    <h1><?= htmlspecialchars($userData['name']) ?></h1>
                    <p>@<?= htmlspecialchars($userData['username']) ?></p>
                    
                    <div class="profile-roles">
                        <?php if ($isFreelancer): ?>
                            <span class="role-badge freelancer">Freelancer</span>
                        <?php endif; ?>
                        
                        <span class="role-badge client">Client</span>
                        
                        <?php if ($isAdmin): ?>
                            <span class="role-badge admin">Admin</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="profile-body">
                    <div class="profile-detail">
                        <span class="detail-label">Account Created</span>
                        <span class="detail-value"><?= date('F j, Y', strtotime($userData['created_at'])) ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?= htmlspecialchars($userData['email'] ?? 'Not provided') ?></span>
                    </div>
                    
                    <div class="profile-actions">
                        <!-- Display Edit Profile button for all users viewing their own profile -->
                        <?php if (!$isViewingOtherProfile): ?>
                            <a href="edit_profile.php" class="btn btn-outline">Edit Profile</a>
                        <?php endif; ?>
                        
                        <!-- Additional actions for user's own profile -->
                        <?php if (!$isViewingOtherProfile): ?>
                            <?php if (!$isFreelancer): ?>
                                <form action="../actions/action_become_freelancer.php" method="post">
                                    <button type="submit" class="btn btn-primary">Become a Freelancer</button>
                                </form>
                            <?php else: ?>
                                <a href="new_service.php" class="btn btn-primary">Create New Service</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs for different sections -->
        <div class="profile-tabs">
            <div class="tab-header">
                <button class="tab-btn active" data-tab="services">My Services</button>
                <button class="tab-btn" data-tab="orders">My Orders</button>
                <button class="tab-btn" data-tab="messages">Messages</button>
                <?php if ($isFreelancer): ?>
                    <button class="tab-btn" data-tab="earnings">Earnings</button>
                <?php endif; ?>
            </div>
            
            <div class="tab-content active" id="services">
                <?php
                require_once(__DIR__ . '/../database/service.class.php');
                $services = Service::getByFreelancerId((int)$userData['id']);
                ?>

                <link rel="stylesheet" href="../css/profile.css">

                <?php if (!empty($services)): ?>
                    <div class="service-grid">
                        <?php foreach ($services as $service): ?>
                            <div class="service-card">
                                <div class="service-info">
                                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                                    <p class="service-category"><?= htmlspecialchars($service['category_name']) ?></p>
                                    <p class="service-price"><?= number_format(floatval($service['price']), 2) ?>€</p>
                                </div>
                                <div class="service-actions">
                                    <a href="service.php?id=<?= $service['id'] ?>" class="btn btn-sm">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No services yet</h3>
                        <p>Start offering your skills by creating your first service</p>
                        <a href="new_service.php" class="btn btn-primary">Create Service</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="orders">
                <?php
                require_once(__DIR__ . '/../database/purchase.class.php');
                // Use both purchases and transactions
                $purchases = Purchase::getUserPurchases((int)$userData['id']);
                $transactions = $clientTransactions ?? [];
                ?>

                <?php if (!empty($purchases) || !empty($transactions)): ?>
                    <div class="orders-list">
                        <?php if (!empty($purchases)): ?>
                            <?php foreach ($purchases as $purchase): ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <h3><?= htmlspecialchars($purchase['title']) ?></h3>
                                        <p><strong>Price:</strong> <?= number_format(floatval($purchase['price']), 2) ?>€</p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($purchase['description']) ?></p>
                                        <p><strong>Purchase Date:</strong> <?= date('d/m/Y', strtotime($purchase['purchase_date'])) ?></p>
                                        <p><strong>Payment Method:</strong> <?= htmlspecialchars(str_replace('_', ' ', ucfirst($purchase['payment_method']))) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <h3><?= htmlspecialchars($transaction['service_title']) ?></h3>
                                        <p><strong>Price:</strong> <?= number_format(floatval($transaction['payment_amount']), 2) ?>€</p>
                                        <p><strong>Status:</strong> <?= htmlspecialchars(str_replace('_', ' ', ucfirst($transaction['status']))) ?></p>
                                        <p><strong>Freelancer:</strong> <?= htmlspecialchars($transaction['freelancer_name'] ?? 'Unknown') ?></p>
                                        <p><strong>Order Date:</strong> <?= date('d/m/Y', strtotime($transaction['created_at'])) ?></p>
                                        <?php if (!empty($transaction['completed_at'])): ?>
                                            <p><strong>Completed Date:</strong> <?= date('d/m/Y', strtotime($transaction['completed_at'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No orders yet</h3>
                        <p>Browse our services and make your first order</p>
                        <a href="../index.php" class="btn btn-primary">Browse Services</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="messages">
                <?php if (!empty($conversations)): ?>
                    <div class="messages-list">
                        <?php foreach ($conversations as $conversation): ?>
                            <a href="conversation.php?id=<?= $conversation['id'] ?>" class="conversation-item <?= $conversation['unread'] ? 'unread' : '' ?>">
                                <div class="conversation-avatar">
                                    <?= strtoupper(substr($conversation['other_user_name'], 0, 1)) ?>
                                </div>
                                <div class="conversation-info">
                                    <h3><?= htmlspecialchars($conversation['other_user_name']) ?></h3>
                                    <p class="last-message"><?= htmlspecialchars($conversation['last_message']) ?></p>
                                    <span class="message-time"><?= formatMessageTime($conversation['updated_at']) ?></span>
                                </div>
                                <?php if ($conversation['unread']): ?>
                                    <div class="unread-badge"><?= $conversation['unread'] ?></div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No messages yet</h3>
                        <p>Contact freelancers to discuss your projects</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($isFreelancer): ?>
                <div class="tab-content" id="earnings">
                    <?php if (!empty($freelancerTransactions)): ?>
                        <div class="earnings-summary">
                            <div class="earnings-card">
                                <h3>Total Earnings</h3>
                                <div class="earnings-amount">$<?= number_format($totalEarnings, 2) ?></div>
                            </div>
                            <div class="earnings-card">
                                <h3>Completed Orders</h3>
                                <div class="earnings-amount"><?= $completedOrders ?></div>
                            </div>
                            <div class="earnings-card">
                                <h3>Pending Orders</h3>
                                <div class="earnings-amount"><?= $pendingOrders ?></div>
                            </div>
                        </div>
                        
                        <h3 class="section-title">Recent Orders</h3>
                        <div class="orders-list">
                            <?php foreach ($freelancerTransactions as $transaction): ?>
                                <div class="order-item">
                                    <div class="order-image">
                                        <?php if (!empty($transaction['service_image'])): ?>
                                            <img src="<?= htmlspecialchars($transaction['service_image']) ?>" alt="Service">
                                        <?php else: ?>
                                            <div class="placeholder-image">No Image</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-info">
                                        <h3><?= htmlspecialchars($transaction['service_title']) ?></h3>
                                        <p>Client: <?= htmlspecialchars($transaction['client_name']) ?></p>
                                        <p class="order-date">Ordered: <?= date('M j, Y', strtotime($transaction['created_at'])) ?></p>
                                        <div class="order-status status-<?= strtolower($transaction['status']) ?>">
                                            <?= ucfirst($transaction['status']) ?>
                                        </div>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order_details.php?id=<?= $transaction['id'] ?>" class="btn btn-sm">View Details</a>
                                        <?php if ($transaction['status'] === 'pending'): ?>
                                            <form action="../actions/action_update_order.php" method="post">
                                                <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="btn btn-sm btn-primary">Accept Order</button>
                                            </form>
                                        <?php elseif ($transaction['status'] === 'in_progress'): ?>
                                            <form action="../actions/action_update_order.php" method="post">
                                                <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success">Mark as Complete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <h3>No earnings yet</h3>
                            <p>Create attractive services to start earning</p>
                            <a href="new_service.php" class="btn btn-primary">Create Service</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php } ?>


<?php function drawProfilePage(array $user, array $messages = []) { ?>
    <div class="container">
        <section class="profile-section">
            <h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>

            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?>">
                        <?= $message['content'] ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Informações do usuário -->
            <div class="user-info">
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Member since:</strong> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            </div>

            <!-- Adicione aqui a seção de compras -->
            <?php
            require_once(__DIR__ . '/../database/purchase.class.php');

            $purchases = Purchase::getUserPurchases($user['id']);
            ?>

            <div class="purchases-section">
                <h2>Your Purchases</h2>
                <?php if (!empty($purchases)): ?>
                    <ul class="purchase-list">
                        <?php foreach ($purchases as $purchase): ?>
                            <li class="purchase-item">
                                <h3><?= htmlspecialchars($purchase['title']) ?></h3>
                                <p><strong>Price:</strong> <?= number_format($purchase['price'], 2) ?>€</p>
                                <p><strong>Description:</strong> <?= htmlspecialchars($purchase['description']) ?></p>
                                <p><strong>Purchase Date:</strong> <?= date('d/m/Y', strtotime($purchase['purchase_date'])) ?></p>
                                <p><strong>Payment Method:</strong> <?= htmlspecialchars(str_replace('_', ' ', ucfirst($purchase['payment_method']))) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>You have no purchases yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
<?php } ?>