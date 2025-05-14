<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/service.class.php');
require_once(__DIR__ . '/../database/user.class.php');
?>

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
