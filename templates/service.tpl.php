<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/service.class.php')
?>



<?php function drawServicePage(Service $service) { ?>
    <header> 
        <link rel="stylesheet" href="/css/style.css">
    </header>
    <section class="service-page">
    <h1><?= htmlspecialchars($service->title) ?></h1>

    <div class="service-details">
      <!-- Supondo que vais adicionar um campo de imagem mais tarde -->
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

    <!-- Placeholder para nome do freelancer -->
    <?php
      // Vais precisar de buscar o nome com base no freelancer_id
      require_once(__DIR__ . '/../database/user.class.php');
      $freelancer = User::getUserById($service->freelancer_id); 
    ?>
    <p><strong>Freelancer:</strong> <?= htmlspecialchars($freelancer->name) ?></p>
  </section>
<?php } ?>
