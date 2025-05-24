<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    header('Location: form_login.php');
    exit();
}

if (!isset($_GET['service_id'])) {
    echo "Service ID not provided.";
    exit();
}

$serviceId = (int)$_GET['service_id'];
$service = Service::getServiceById($serviceId);

if (!$service) {
    echo "Service not found.";
    exit();
}

drawHeader(true);
?>

<link rel="stylesheet" href="../css/checkout.css">

<div class="container">
    <h1>Finalize Your Purchase</h1>
    <div class="service-summary">
        <h2><?= htmlspecialchars($service['title']) ?></h2>
        <p><strong>Price:</strong> <?= number_format(floatval($service['price']), 2) ?>€</p>
        <p><strong>Description:</strong> <?= htmlspecialchars($service['description']) ?></p>
        <p><strong>Delivery Time:</strong> <?= $service['delivery_time'] ?> days</p>
        <p><strong>Freelancer:</strong> <?= htmlspecialchars($service['freelancer_name']) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($service['category_name']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($service['location'] ?? 'Not specified') ?></p>
    </div>

    <h2>Payment Options</h2>
    <form action="../actions/action_finalize_purchase.php" method="post">
        <input type="hidden" name="service_id" value="<?= $serviceId ?>">

        <div class="payment-methods">
            <label>
                <input type="radio" name="payment_method" value="mbway" required>
                MBWay
            </label>
            <label>
                <input type="radio" name="payment_method" value="credit_card" required>
                Credit Card
            </label>
            <label>
                <input type="radio" name="payment_method" value="paypal" required>
                PayPal
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Confirm Payment</button>
    </form>
</div>

<?php
drawFooter();   
?>

