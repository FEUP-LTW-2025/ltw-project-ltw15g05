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


        
    public static function create(
        int $freelancer_id,
        string $title,
        string $description,
        int $category_id,
        float $price,
        int $delivery_time,
        array $images,
        bool $featured, // Se estiveres a usar este campo numa tabela separada
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
                image,
                location
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            $image, 
            $location
        ]);
    
        $service_id = (int)$db->lastInsertId();
    
        // Inserir as imagens se existir tabela ServiceImage
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

        $stmt = $db->prepare('SELECT * FROM services');
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
                $service['image'],
                $service['location'],
                $service['created_at']
            );
        }

        return $services;
    }

    public static function getAllCategories() : array {
        $db = Database::getInstance();
    
        $stmt = $db->prepare('SELECT id, name FROM categories');
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}