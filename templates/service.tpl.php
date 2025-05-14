<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/user.class.php');
?>

<?php function drawNewServiceForm(array $categories, array $messages = []) { ?>
    <div class="container">
        <section class="form-section">
            <h1 class="section-title">Create New Service</h1>
            
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?>">
                        <?= $message['content'] ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="form-container">
                <form action="../actions/action_create_service.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title" class="form-label">Service Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Professional Portrait Photography" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="5" placeholder="Describe your service in detail..." required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category_id" class="form-control" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group col">
                            <label for="price" class="form-label">Price (€)</label>
                            <input type="number" id="price" name="price" class="form-control" min="1" step="0.01" placeholder="99.99" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="delivery_time" class="form-label">Delivery Time (days)</label>
                            <input type="number" id="delivery_time" name="delivery_time" class="form-control" min="1" placeholder="3" required>
                        </div>
                        
                        <div class="form-group col">
                            <label for="featured" class="form-label">Featured Service</label>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="featured" name="featured" class="form-checkbox">
                                <label for="featured">Mark as featured (appears on homepage)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="images" class="form-label">Service Images</label>
                        <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                        <small class="form-text">Upload up to 5 images (JPEG, PNG). First image will be the primary image.</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="profile.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Service</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
<?php } ?>

<?php function drawEditServiceForm(array $service, array $categories, array $messages = []) { ?>
    <div class="container">
        <section class="form-section">
            <h1 class="section-title">Edit Service</h1>
            
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?>">
                        <?= $message['content'] ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="form-container">
                <form action="../actions/action_update_service.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Service Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($service['title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= htmlspecialchars($service['description']) ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category_id" class="form-control" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $service['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group col">
                            <label for="price" class="form-label">Price (€)</label>
                            <input type="number" id="price" name="price" class="form-control" min="1" step="0.01" value="<?= $service['price'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="delivery_time" class="form-label">Delivery Time (days)</label>
                            <input type="number" id="delivery_time" name="delivery_time" class="form-control" min="1" value="<?= $service['delivery_time'] ?>" required>
                        </div>
                        
                        <div class="form-group col">
                            <label for="featured" class="form-label">Featured Service</label>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="featured" name="featured" class="form-checkbox" <?= $service['featured'] ? 'checked' : '' ?>>
                                <label for="featured">Mark as featured (appears on homepage)</label>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($service['images'])): ?>
                        <div class="form-group">
                            <label class="form-label">Current Images</label>
                            <div class="image-preview-grid">
                                <?php foreach ($service['images'] as $index => $image): ?>
                                    <div class="image-preview">
                                        <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Service Image">
                                        <div class="image-actions">
                                            <input type="checkbox" id="delete_image_<?= $image['id'] ?>" name="delete_images[]" value="<?= $image['id'] ?>">
                                            <label for="delete_image_<?= $image['id'] ?>">Delete</label>
                                            
                                            <input type="radio" id="primary_image_<?= $image['id'] ?>" name="primary_image" value="<?= $image['id'] ?>" <?= $image['is_primary'] ? 'checked' : '' ?>>
                                            <label for="primary_image_<?= $image['id'] ?>">Primary</label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="images" class="form-label">Add New Images</label>
                        <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                        <small class="form-text">Upload up to 5 images (JPEG, PNG).</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="profile.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
<?php } ?>


<?php function drawServicePage(Service $service, $freelancer) { ?>
    <header> 
        <link rel="stylesheet" href="/css/service.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    </header>
    <section class="service-page">
    <h1><?= htmlspecialchars($service->title) ?></h1>

    <div class="service-details">      
      <img src="/images/services/<?= $service->id ?>.jpg" alt="Imagem do serviço <?= htmlspecialchars($service->title) ?>" class="service-image">
      
      <div class="service-meta">
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($service->description)) ?></p>
        <p><strong>Preço:</strong> <?= number_format($service->price, 2) ?>€</p>
        <p><strong>Tempo de entrega:</strong> <?= $service->delivery_time ?> dias</p>
        <p><strong>Estilo Fotográfico:</strong> <?= htmlspecialchars($service->photo_style) ?></p>
        <p><strong>Equipamento fornecido:</strong> <?= $service->equipment_provided ? 'Sim' : 'Não' ?></p>
        <?php if (!empty($service->location)) : ?>
          <p><strong>Localização:</strong> <?= htmlspecialchars($service->location) ?></p>
        <?php endif; ?>
        <p><strong>Data de criação:</strong> <?= date('d/m/Y', strtotime($service->created_at)) ?></p>
      </div>
    </div>

    <div class="freelancer-info">
        <img src="/images/user/<?= $freelancer['username']?>.jpg" alt="Imagem do user" class="user-image">
        <div class="freelancer-meta">
          <p class="freelancer-name"><strong><?= htmlspecialchars($freelancer['username']) ?></strong></p>
          <!-- Aqui podes adicionar a avaliação depois -->
          <p class="freelancer-rating">⭐ 4.8 (52 reviews)</p>
        </div>
    </div>


    <div class="buy-buttons">
      <button class="buy-now">Comprar</button>
      <button class="save-later">Guardar para depois</button>
    </div>

  </section>
<?php } ?>
