<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/review.class.php');
require_once(__DIR__ . '/../database/user.class.php');


?>

<?php function drawNewServiceForm(array $categories, array $messages = []) { ?>
    <div class="container">
        <section class="form-section">                        
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
                        <input type="file" id="image" name="image" class="form-control" accept="image/jpeg, image/png" required>
                        <small class="form-text">Upload up to 5 images (JPEG, PNG). First image will be the primary image.</small>
                    </div>

                    <div class="form-group">
                        <label for="photo_style" class="form-label">Photo Style</label>
                        <select id="photo_style" name="photo_style" class="form-control" required>
                            <option value="">-- Select a style --</option>
                            <option value="Portrait">Portrait</option>
                            <option value="Landscape">Landscape</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter the service location" required>
                    </div>

                    <div class="form-group">
                        <label for="equipment_provided" class="form-label">Equipment Provided</label>
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="equipment_provided" name="equipment_provided" class="form-checkbox">
                            <label for="equipment_provided">Check if equipment is provided</label>
                        </div>
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
        <link rel="stylesheet" href="/css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </header>
    <section class="service-page">
    <h1><?= htmlspecialchars($service->title) ?></h1>

    <div class="service-details">  
        <?php
            $servicePath = "/images/services/$service->id.jpg";
            $absolutePath = __DIR__ . "/../images/services/$service->id.jpg";

            if (!file_exists($absolutePath)) {
                $servicePath = "/images/services/default.jpg";
            }
        ?>
        <img src="<?= $servicePath ?>" alt="Imagem do serviço <?= htmlspecialchars($service->title) ?>" class="service-image">

      <div class="service-meta">
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($service->description)) ?></p>
        <p><strong>Duration:</strong> <?= $service->delivery_time ?> days</p>        <p><strong>Photo Style:</strong> <?= htmlspecialchars($service->photo_style) ?></p>
        <p><strong>Equipment Provided:</strong> <?= $service->equipment_provided ? 'Yes' : 'No' ?></p>
        <?php if (!empty($service->location)) : ?>
          <p><strong>Location:</strong> <?= htmlspecialchars($service->location) ?></p>
        <?php endif; ?>
        <p><strong>Creation Date:</strong> <?= date('d/m/Y', strtotime($service->created_at)) ?></p>
      </div>
    </div>

    <div class="freelancer-info">

        <?php
            $username = $freelancer['username'];
            $profilePath = "/images/user/$username.jpg";
            $absolutePath = __DIR__ . "/../images/user/$username.jpg";

            if (!file_exists($absolutePath)) {
                $profilePath = "/images/user/default.jpg";
            }
        ?>
        <img src="<?= $profilePath ?>" alt="Imagem do user" class="user-image">

        <div class="freelancer-meta">
            <div class="freelancer-top">
                <p class="freelancer-name"><?= htmlspecialchars($freelancer['username']) ?></p>
                <a href="../pages/chat.php?chat_with=<?= $freelancer['id']?>" class="message-button">
                    Messages
                </a>
            </div>
        </div>
    </div>


    <div class="price-highlight">
      <span><?= number_format($service->price, 2) ?>€</span>
    </div>    
    <div class="buy-buttons">
        <a href="../pages/checkout.php?service_id=<?= $service->id ?>" class="btn-buy-now">Buy</a>
    </div>
    <div class="review-form">
        <h2>Leave a Review</h2>
        <form action="../actions/action_add_review.php" method="post">
            <input type="hidden" name="service_id" value="<?= $service->id ?>">

            <div class="form-group">
                <label for="rating">Rating:</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
                </div>
            </div>

            <div class="form-group">
                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
    <div class="reviews-section">
        <h2>Reviews</h2>
        <?php
        $reviews = Review::getReviewsByService($service->id);
        if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <strong class="review-author"><?= htmlspecialchars($review['client_name']) ?></strong>
                            <span class="review-date">• <?= date('d m Y, H:i', strtotime($review['created_at'])) ?></span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="star <?= $i < $review['rating'] ? 'filled' : '' ?>">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="review-comment"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-reviews">No reviews yet. Be the first to leave one!</p>
        <?php endif; ?>
        </div>
    </div>


  </section>
<?php } ?>
