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
    public string $photo_style;
    public bool $equipment_provided;
    public ?string $location;
    public string $created_at;

    public function __construct(
        int $id,
        int $freelancer_id,
        string $title,
        string $description,
        int $category_id,
        float $price,
        int $delivery_time,
        string $photo_style,
        bool $equipment_provided,
        ?string $location,
        string $created_at
    ) {
        $this->id = $id;
        $this->freelancer_id = $freelancer_id;
        $this->title = $title;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->price = $price;
        $this->delivery_time = $delivery_time;
        $this->photo_style = $photo_style;
        $this->equipment_provided = $equipment_provided;
        $this->location = $location;
        $this->created_at = $created_at;
    }

    public static function getService(int $id): Service {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);

        $service = $stmt->fetch();

        if (!$service) {
            throw new Exception('Service not found.');
        }

        return new Service(
            (int)$service['id'],
            (int)$service['freelancer_id'],
            $service['title'],
            $service['description'],
            (int)$service['category_id'],
            (float)$service['price'],
            (int)$service['delivery_time'],
            $service['photo_style'],
            (bool)$service['equipment_provided'],
            $service['location'],
            $service['created_at']
        );
    }

    public static function getFreelancerServices(int $freelancer_id): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM services WHERE freelancer_id = ?');
        $stmt->execute([$freelancer_id]);

        $services = [];

        while ($service = $stmt->fetch()) {
            $services[] = new Service(
                (int)$service['id'],
                (int)$service['freelancer_id'],
                $service['title'],
                $service['description'],
                (int)$service['category_id'],
                (float)$service['price'],
                (int)$service['delivery_time'],
                $service['photo_style'],
                (bool)$service['equipment_provided'],
                $service['location'],
                $service['created_at']
            );
        }

        return $services;
    }

    public static function getByFreelancerId(int $freelancerId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT 
                services.id,
                services.title,
                services.price,
                categories.name AS category_name
            FROM services
            JOIN categories ON services.category_id = categories.id
            WHERE services.freelancer_id = ?
        ');
        $stmt->execute([$freelancerId]);
        return $stmt->fetchAll();
    }


        
    public static function create(
        int $freelancer_id,
        string $title,
        string $description,
        int $category_id,
        float $price,
        int $delivery_time,
        array $images,
        string $photo_style,
        bool $equipment_provided,
        ?string $location
    ): int {
        $db = Database::getInstance();
    
        $stmt = $db->prepare('
            INSERT INTO services (
                freelancer_id,
                title,
                description,
                category_id,
                price,
                delivery_time,
                photo_style,
                equipment_provided,
                location,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, DATETIME("now"));
        ');
    
        $stmt->execute([
            $freelancer_id,
            $title,
            $description,
            $category_id,
            $price,
            $delivery_time,
            $photo_style,
            $equipment_provided,
            $location
        ]);
    
        $service_id = (int)$db->lastInsertId();
    
        if (!empty($images)) {
            $stmtImage = $db->prepare('INSERT INTO ServiceImage (service_id, path) VALUES (?, ?)');
            foreach ($images as $imgPath) {
                $stmtImage->execute([$service_id, $imgPath]);
            }
        }
    
        return $service_id;
    }
    

    public static function getAllServices(): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM services ORDER BY created_at DESC');
        $stmt->execute();

        $services = [];

        while ($service = $stmt->fetch()) {
            $services[] = new Service(
                (int)$service['id'],
                (int)$service['freelancer_id'],
                $service['title'],
                $service['description'],
                (int)$service['category_id'],
                (float)$service['price'],
                (int)$service['delivery_time'],
                $service['photo_style'],
                (bool)$service['equipment_provided'],
                $service['location'],
                $service['created_at']
            );
        }

        return $services;
    }

    public static function getAllCategories(): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT id, name FROM categories ORDER BY name');
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public static function getServicesByCategory(int $category_id): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM services WHERE category_id = ?');
        $stmt->execute([$category_id]);
        
        $services = [];
        
        while ($service = $stmt->fetch()) {
            $services[] = new Service(
                (int)$service['id'],
                (int)$service['freelancer_id'],
                $service['title'],
                $service['description'],
                (int)$service['category_id'],
                (float)$service['price'],
                (int)$service['delivery_time'],
                $service['photo_style'],
                (bool)$service['equipment_provided'],
                $service['location'],
                $service['created_at']
            );
        }
        
        return $services;
    }
    
    public static function getServicesByPrice(float $min_price, float $max_price): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM services WHERE price BETWEEN ? AND ?');
        $stmt->execute([$min_price, $max_price]);
        
        $services = [];
        
        while ($service = $stmt->fetch()) {
            $services[] = new Service(
                (int)$service['id'],
                (int)$service['freelancer_id'],
                $service['title'],
                $service['description'],
                (int)$service['category_id'],
                (float)$service['price'],
                (int)$service['delivery_time'],
                $service['photo_style'],
                (bool)$service['equipment_provided'],
                $service['location'],
                $service['created_at']
            );
        }
        
        return $services;
    }


    public static function getServiceById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT 
                services.id, 
                services.title, 
                services.description, 
                services.price, 
                services.delivery_time, 
                services.photo_style, 
                services.equipment_provided, 
                services.location, 
                services.created_at,
                categories.name AS category_name,
                users.username AS freelancer_name
            FROM services
            JOIN categories ON services.category_id = categories.id
            JOIN users ON services.freelancer_id = users.id
            WHERE services.id = ?
        ');
        $stmt->execute([$id]);
        $service = $stmt->fetch();
    
        return $service ?: null;
    }
    
    public static function getServicesByDeliveryTime(int $max_days): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT * FROM services WHERE delivery_time <= ?');
        $stmt->execute([$max_days]);
        
        $services = [];
        
        while ($service = $stmt->fetch()) {
            $services[] = new Service(
                (int)$service['id'],
                (int)$service['freelancer_id'],
                $service['title'],
                $service['description'],
                (int)$service['category_id'],
                (float)$service['price'],
                (int)$service['delivery_time'],
                $service['photo_style'],
                (bool)$service['equipment_provided'],
                $service['location'],
                $service['created_at']
            );
        }
        
        return $services;
    }
    
    public static function getPriceRanges(): array {
        return [
            ['min' => 0, 'max' => 50, 'label' => 'Under $50'],
            ['min' => 50, 'max' => 100, 'label' => '$50 - $100'],
            ['min' => 100, 'max' => 250, 'label' => '$100 - $250'],
            ['min' => 250, 'max' => 500, 'label' => '$250 - $500'],
            ['min' => 500, 'max' => 1000, 'label' => '$500 - $1000'],
            ['min' => 1000, 'max' => 100000, 'label' => 'Over $1000'],
        ];
    }
    
    public static function getDeliveryTimeRanges(): array {
        return [
            ['max' => 1, 'label' => '24 Hours'],
            ['max' => 3, 'label' => 'Up to 3 days'],
            ['max' => 7, 'label' => 'Up to 1 week'],
            ['max' => 14, 'label' => 'Up to 2 weeks'],
            ['max' => 30, 'label' => 'Up to 1 month'],
            ['max' => 9999, 'label' => 'Over 1 month'],
        ];
    }

    
    public static function addCategory(string $name, ?string $description = null): int {
        $db = Database::getInstance();
        
        $checkStmt = $db->prepare('SELECT id FROM categories WHERE name = ?');
        $checkStmt->execute([$name]);
        
        if ($checkStmt->fetch()) {
            throw new Exception('A category with this name already exists.');
        }
        
        $stmt = $db->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        
        return (int)$db->lastInsertId();
    }
    

    public static function updateCategory(int $id, string $name, ?string $description = null): bool {
        $db = Database::getInstance();
        
        $checkStmt = $db->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
        $checkStmt->execute([$name, $id]);
        
        if ($checkStmt->fetch()) {
            throw new Exception('Another category with this name already exists.');
        }
        
        $stmt = $db->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
        
        return $stmt->rowCount() > 0;
    }

    public static function deleteCategory(int $id): bool {
        $db = Database::getInstance();
        
        $checkStmt = $db->prepare('SELECT COUNT(*) as count FROM services WHERE category_id = ?');
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();
        
        if ($result && $result['count'] > 0) {
            throw new Exception('This category cannot be deleted because it is being used by one or more services.');
        }
        
        $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    }

    public static function getCategoryById(int $id): ?array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT id, name FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        
        $category = $stmt->fetch();
        return $category ?: null;
    }
    

    public static function countServicesInCategory(int $categoryId): int {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM services WHERE category_id = ?');
        $stmt->execute([$categoryId]);
        
        $result = $stmt->fetch();
        return ($result) ? (int)$result['count'] : 0;
    }
}