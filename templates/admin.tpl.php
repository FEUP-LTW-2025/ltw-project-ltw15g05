<?php
declare(strict_types=1);

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
                                    <a href="../pages/profile.php?id=<?= $user['id'] ?>" class="btn btn-sm" title="View Profile">
                                        <i class="fas fa-eye"></i> View
                                    </a>
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
                                        <?php if ($_SESSION['user_id'] != $user['id']): ?>
                                            <a action="../actions/action_delete_user.php" method="get" style="width: 100%;">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" 
                                                class="btn btn-sm btn-danger delete-user-btn" 
                                                title="Delete User"
                                                style="width: 100%;"
                                                onclick="return confirm('WARNING: This will permanently delete this user and ALL their data including services, transactions, and messages. This action cannot be undone. Are you sure?');">
                                                <i class="fas fa-trash">Delete User</i> 
                                                </button>
                                        </a>
                                        <?php endif; ?>
                                    </div>
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

<?php function drawCategoryManagement(array $categories) { ?>
    <section class="admin-section">
        <h2>Category Management</h2>
        
        <div class="category-actions">
            <a href="#" class="btn btn-primary" id="add-category-btn">
                <i class="fas fa-plus"></i> Add New Category
            </a>
        </div>
        
        <div id="add-category-form" class="form-container" style="display: none;">
            <form action="../actions/action_add_category.php" method="post" class="admin-form">
                <div class="form-group">
                    <label for="category-name" class="form-label">Category Name</label>
                    <input type="text" id="category-name" name="name" class="form-control" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Save Category</button>
                    <button type="button" class="btn btn-secondary" id="cancel-add-category">Cancel</button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Services</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="no-data">No categories found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td>No description</td>
                                <td><?= Service::countServicesInCategory((int)$category['id']) ?></td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm edit-category" data-id="<?= $category['id'] ?>" 
                                       data-name="<?= htmlspecialchars($category['name']) ?>" 
                                       data-description="">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="../actions/action_delete_category.php?id=<?= $category['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                        <i class="fas fa-trash">Delete</i> 
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div id="edit-category-form" class="form-container" style="display: none;">
            <form action="../actions/action_update_category.php" method="post" class="admin-form">
                <input type="hidden" id="edit-category-id" name="id" value="">
                <div class="form-group">
                    <label for="edit-category-name" class="form-label">Category Name</label>
                    <input type="text" id="edit-category-name" name="name" class="form-control" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Update Category</button>
                    <button type="button" class="btn btn-secondary" id="cancel-edit-category">Cancel</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('add-category-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('add-category-form').style.display = 'block';
        });
          document.getElementById('cancel-add-category').addEventListener('click', function() {
            document.getElementById('add-category-form').style.display = 'none';
            document.getElementById('category-name').value = '';
        });
        const editButtons = document.querySelectorAll('.edit-category');
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                document.getElementById('edit-category-id').value = id;
                document.getElementById('edit-category-name').value = name;
                document.getElementById('edit-category-form').style.display = 'block';
                
                document.getElementById('edit-category-form').scrollIntoView({ behavior: 'smooth' });
            });
        });
        
        document.getElementById('cancel-edit-category').addEventListener('click', function() {
            document.getElementById('edit-category-form').style.display = 'none';
        });
    </script>
<?php } ?>
