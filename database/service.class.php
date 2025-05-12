<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Service {
    public int $id;
    public int $freelancer_id;
    public string $title;
    public string $description;
    public int $category_id;
    public float $price;
    public int $delivery_time;
    public bool $featured;
    public string $created_at;
    public string $updated_at;
    public array $images;
    
    public function __construct(
        int $id, 
        int $freelancer_id, 
        string $title, 
        string $description, 
        int $category_id, 
        float $price, 
        int $delivery_time,
        bool $featured = false,
        string $created_at = '',
        string $updated_at = '',
        array $images = []
    ) {
        $this->id = $id;
        $this->freelancer_id = $freelancer_id;
        $this->title = $title;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->price = $price;
        $this->delivery_time = $delivery_time;
        $this->featured = $featured;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->images = $images;
    }
    
    public static function create(
        int $freelancer_id, 
        string $title, 
        string $description, 
        int $category_id, 
        float $price, 
        int $delivery_time,
        array $images = []
    ): int {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            INSERT INTO services (
                freelancer_id, title, description, category_id, 
                price, delivery_time, featured, created_at, updated_at
            ) 
            VALUES (?, ?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ');
        
        $stmt->execute([
            $freelancer_id, $title, $description, $category_id, 
            $price, $delivery_time
        ]);
        
        $serviceId = $db->lastInsertId();
        
        // Process images if any
        if (!empty($images)) {
            foreach ($images as $index => $image) {
                $isPrimary = ($index === 0) ? 1 : 0; // First image is primary
                $stmt = $db->prepare('
                    INSERT INTO service_images (service_id, image_path, is_primary)
                    VALUES (?, ?, ?)
                ');
                $stmt->execute([$serviceId, $image, $isPrimary]);
            }
        }
        
        return $serviceId;
    }
    
    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        $service = $stmt->fetch();
        
        if ($service) {
            $service['images'] = self::getServiceImages($id);
        }
        
        return $service ?: null;
    }
    
    public static function getServiceImages(int $serviceId): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT * FROM service_images 
            WHERE service_id = ? 
            ORDER BY is_primary DESC, id ASC
        ');
        $stmt->execute([$serviceId]);
        
        return $stmt->fetchAll();
    }
    
    public static function getAll(
        int $limit = 10, 
        int $offset = 0, 
        ?int $categoryId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?int $minRating = null,
        ?string $sortBy = null
    ): array {
        $db = Database::getInstance();
        
        $conditions = [];
        $params = [];
        
        if ($categoryId !== null) {
            $conditions[] = "category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($minPrice !== null) {
            $conditions[] = "price >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $conditions[] = "price <= ?";
            $params[] = $maxPrice;
        }
        
        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        // Default sorting
        $orderBy = "created_at DESC";
        
        // Custom sorting
        if ($sortBy === 'price_asc') {
            $orderBy = "price ASC";
        } elseif ($sortBy === 'price_desc') {
            $orderBy = "price DESC";
        } elseif ($sortBy === 'rating') {
            // For rating sorting, we need a subquery or join to get average rating
            // This is a simplified version
            $orderBy = "featured DESC, created_at DESC";
        }
        
        $query = "
            SELECT s.*, 
                   u.name as freelancer_name, 
                   c.name as category_name,
                   (SELECT image_path FROM service_images WHERE service_id = s.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM services s
            JOIN users u ON s.freelancer_id = u.id
            JOIN categories c ON s.category_id = c.id
            $whereClause
            ORDER BY s.featured DESC, $orderBy
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public static function getByFreelancerId(int $freelancerId): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT s.*, 
                   c.name as category_name,
                   (SELECT image_path FROM service_images WHERE service_id = s.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM services s
            JOIN categories c ON s.category_id = c.id
            WHERE s.freelancer_id = ?
            ORDER BY s.created_at DESC
        ');
        $stmt->execute([$freelancerId]);
        
        return $stmt->fetchAll();
    }
    
    public static function update(
        int $id, 
        string $title, 
        string $description, 
        int $category_id, 
        float $price, 
        int $delivery_time,
        bool $featured = false
    ): bool {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            UPDATE services
            SET title = ?, 
                description = ?, 
                category_id = ?, 
                price = ?, 
                delivery_time = ?,
                featured = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        
        return $stmt->execute([
            $title, 
            $description, 
            $category_id, 
            $price, 
            $delivery_time,
            $featured ? 1 : 0,
            $id
        ]);
    }
    
    public static function delete(int $id): bool {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('DELETE FROM services WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public static function getCategories(): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM categories ORDER BY name');
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public static function getCategory(int $id): ?array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        
        return $stmt->fetch() ?: null;
    }
}
?>
