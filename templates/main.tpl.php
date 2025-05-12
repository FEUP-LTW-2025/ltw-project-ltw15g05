<?php
declare(strict_types=1);
?>

<?php function drawServiceList(array $services) { ?>
    <section id="services">
        <ul class="flex square">
            <?php foreach ($services as $service) { ?>
                <li>
                    <article>
                        <a href="service.php?id=<?=$service->id?>">
                            <img src="https://picsum.photos/200?service=<?=$service->id?>" width="200" height="200" alt="Service image">
                            <h3><?=htmlspecialchars($service->title)?></h3>
                        </a>
                        <p><?=htmlspecialchars($service->description)?></p>
                        <ul>
                            <li><strong>Price:</strong> $<?=number_format($service->price, 2)?></li>
                            <li><strong>Delivery Time:</strong> <?=intval($service->delivery_time)?> days</li>
                            <li><strong>Photo Style:</strong> <?=htmlspecialchars($service->photo_style)?></li>
                            <li><strong>Equipment Provided:</strong> <?=($service->equipment_provided ? 'Yes' : 'No')?></li>
                            <li><strong>Location:</strong> <?=htmlspecialchars($service->location ?? 'Remote')?></li>
                        </ul>
                    </article>
                </li>
            <?php } ?>
        </ul>
    </section>
<?php } 