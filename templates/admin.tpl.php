<?php
declare(strict_types=1);

/**
 * Draw the admin panel with user management features
 * 
 * @param array $users List of all users with their details
 */
function drawAdminPanel(array $users) { ?>
    <main class="admin-panel">
        <div class="container">
            <h1>Admin Panel</h1>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <section class="admin-section">
                <h2>User Management</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? 'Not set') ?></td>
                                <td>
                                    <?php foreach ($user['roles'] as $role): ?>
                                        <span class="role-badge <?= htmlspecialchars($role) ?>"><?= htmlspecialchars(ucfirst($role)) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                <td class="actions">
                                    <!-- View profile action -->
                                    <a href="../pages/profile.php?id=<?= $user['id'] ?>" class="btn btn-sm" title="View Profile">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <!-- Admin role actions -->
                                    <?php if (in_array('admin', $user['roles'])): ?>
                                        <?php if ($_SESSION['user_id'] != $user['id']): ?>
                                        <a href="admin.php?action=remove_admin&user_id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           title="Remove Admin Role"
                                           onclick="return confirm('Are you sure you want to remove the admin role from this user?');">
                                            <i class="fas fa-user-slash"></i> Remove Admin
                                        </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="admin.php?action=promote_to_admin&user_id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="Promote to Admin"
                                           onclick="return confirm('Are you sure you want to promote this user to admin? This will give them full access to the admin panel.');">
                                            <i class="fas fa-user-shield"></i> Make Admin
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
<?php } ?>
