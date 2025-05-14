<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/user.class.php');
?>

<?php function drawServicePage(Service $service, $freelancer) { ?>
    <header> 
        <link rel="stylesheet" href="/css/service.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </header>
    <section class="service-page">
    <h1><?= htmlspecialchars($service->title) ?></h1>

    <div class="service-details">      
      <img src="/images/services/<?= $service->id ?>.jpg" alt="Imagem do serviço <?= htmlspecialchars($service->title) ?>" class="service-image">
      
      <div class="service-meta">
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($service->description)) ?></p>
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
        <div class="freelancer-top">
          <p class="freelancer-name"><?= htmlspecialchars($freelancer['username']) ?></p>
          <button class="message-button" title="Enviar mensagem">
            <i class="fa-solid fa-envelope"></i>
          </button>
        </div>
        <div class="freelancer-rating">
            <i class="fa-solid fa-star" style="color: #fbbf24;"></i> 
            4.9 (120)
        </div>
      </div>
    </div>

    <div class="price-highlight">
      <span><?= number_format($service->price, 2) ?>€</span>
    </div>

    <div class="buy-buttons">
      <button class="buy-now">Comprar</button>
      <button class="save-later">Guardar para mais tarde</button>
    </div>


  </section>
<?php } ?>
