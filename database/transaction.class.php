<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/service.class.php');

class Transaction {
    public int $id;
    public int $service_id;
    public int $client_id;
    public int $freelancer_id;
    public string $status;
    public float $payment_amount;
    public string $custom_requirements;
    public string $created_at;
    public ?string $completed_at;
    
    public function __construct(
        int $id, 
        int $service_id, 
        int $client_id, 
        int $freelancer_id,
        string $status,
        float $payment_amount,
        string $custom_requirements = '',
        string $created_at = '',
        ?string $completed_at = null
    ) {
        $this->id = $id;
        $this->service_id = $service_id;
        $this->client_id = $client_id;
        $this->freelancer_id = $freelancer_id;
        $this->status = $status;
        $this->payment_amount = $payment_amount;
        $this->custom_requirements = $custom_requirements;
        $this->created_at = $created_at;
        $this->completed_at = $completed_at;
    }
    
    public static function create(
        int $service_id, 
        int $client_id, 
        int $freelancer_id, 
        float $payment_amount,
        string $custom_requirements = ''
    ): int {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            INSERT INTO transactions (
                service_id, client_id, freelancer_id, status, 
                payment_amount, custom_requirements, created_at
            ) 
            VALUES (?, ?, ?, \'pending\', ?, ?, CURRENT_TIMESTAMP)
        ');
        
        $stmt->execute([
            $service_id, $client_id, $freelancer_id, 
            $payment_amount, $custom_requirements
        ]);
        
        return $db->lastInsertId();
    }
    
    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT t.*, 
                   s.title as service_title, 
                   c.name as client_name, 
                   f.name as freelancer_name
            FROM transactions t
            JOIN services s ON t.service_id = s.id
            JOIN users c ON t.client_id = c.id
            JOIN users f ON t.freelancer_id = f.id
            WHERE t.id = ?
        ');
        $stmt->execute([$id]);
        
        return $stmt->fetch() ?: null;
    }
    
    public static function getByClientId(int $clientId): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT t.*, 
                   s.title as service_title, 
                   f.name as freelancer_name,
                   (SELECT image_path FROM service_images WHERE service_id = t.service_id AND is_primary = 1 LIMIT 1) as service_image
            FROM transactions t
            JOIN services s ON t.service_id = s.id
            JOIN users f ON t.freelancer_id = f.id
            WHERE t.client_id = ?
            ORDER BY t.created_at DESC
        ');
        $stmt->execute([$clientId]);
        
        return $stmt->fetchAll();
    }
    
    public static function getByFreelancerId(int $freelancerId): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT t.*, 
                   s.title as service_title, 
                   c.name as client_name,
                   (SELECT image_path FROM service_images WHERE service_id = t.service_id AND is_primary = 1 LIMIT 1) as service_image
            FROM transactions t
            JOIN services s ON t.service_id = s.id
            JOIN users c ON t.client_id = c.id
            WHERE t.freelancer_id = ?
            ORDER BY t.created_at DESC
        ');
        $stmt->execute([$freelancerId]);
        
        return $stmt->fetchAll();
    }
    
    public static function updateStatus(int $id, string $status): bool {
        $db = Database::getInstance();
        
        $completedAt = ($status === 'completed') ? ", completed_at = CURRENT_TIMESTAMP" : "";
        
        $stmt = $db->prepare("
            UPDATE transactions
            SET status = ?$completedAt
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $id]);
    }
    
    // Add review for a completed transaction
    public static function addReview(int $transactionId, int $clientId, int $serviceId, int $rating, string $comment = ''): int {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            INSERT INTO reviews (
                transaction_id, client_id, service_id, 
                rating, comment, created_at
            ) 
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');
        
        $stmt->execute([
            $transactionId, $clientId, $serviceId, $rating, $comment
        ]);
        
        return $db->lastInsertId();
    }
    
    // Get service average rating
    public static function getServiceRating(int $serviceId): ?float {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
            FROM reviews
            WHERE service_id = ?
        ');
        $stmt->execute([$serviceId]);
        
        $result = $stmt->fetch();
        
        if ($result && $result['review_count'] > 0) {
            return (float)$result['avg_rating'];
        }
        
        return null;
    }
    
    // Get service reviews
    public static function getServiceReviews(int $serviceId): array {
        $db = Database::getInstance();
        
        $stmt = $db->prepare('
            SELECT r.*, u.name as client_name
            FROM reviews r
            JOIN users u ON r.client_id = u.id
            WHERE r.service_id = ?
            ORDER BY r.created_at DESC
        ');
        $stmt->execute([$serviceId]);
        
        return $stmt->fetchAll();
    }
}
?>
