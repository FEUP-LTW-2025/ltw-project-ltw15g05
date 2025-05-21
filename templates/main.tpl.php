<?php
declare(strict_types=1);
?>

<?php function drawFilterNavigation(array $categories, array $priceRanges, array $deliveryTimeRanges, array $activeFilters) { ?>
    <div class="filter-navigation">
        <nav>
            <ul class="filter-tabs">
                <li class="filter-dropdown">
                    <a href="#" class="filter-tab<?= $activeFilters['category'] !== null ? ' active' : '' ?>">Categories</a>
                    <div class="dropdown-content">
                        <a href="main.php" class="<?= $activeFilters['category'] === null && $activeFilters['price'] === null && $activeFilters['delivery_time'] === null ? 'active' : '' ?>">All Categories</a>
                        <?php foreach ($categories as $category): ?>
                        <a href="main.php?category=<?= $category['id'] ?>" class="<?= $activeFilters['category'] === (int)$category['id'] ? 'active' : '' ?>"><?= htmlspecialchars($category['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li class="filter-dropdown">
                    <a href="#" class="filter-tab<?= $activeFilters['price'] !== null ? ' active' : '' ?>">Price</a>
                    <div class="dropdown-content">
                        <?php foreach ($priceRanges as $range): ?>
                        <a href="main.php?min_price=<?= $range['min'] ?>&max_price=<?= $range['max'] ?>" class="<?= ($activeFilters['price'] !== null && $activeFilters['price']['min'] == $range['min'] && $activeFilters['price']['max'] == $range['max']) ? 'active' : '' ?>"><?= htmlspecialchars($range['label']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li class="filter-dropdown">
                    <a href="#" class="filter-tab<?= $activeFilters['delivery_time'] !== null ? ' active' : '' ?>">Delivery Time</a>
                    <div class="dropdown-content">
                        <?php foreach ($deliveryTimeRanges as $range): ?>
                        <a href="main.php?delivery_time=<?= $range['max'] ?>" class="<?= $activeFilters['delivery_time'] === $range['max'] ? 'active' : '' ?>"><?= htmlspecialchars($range['label']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
<?php } ?>

<?php function drawServiceList(array $services) { ?>
    <section id="services">
        <?php if (empty($services)): ?>
            <div class="no-services">No services found matching your filters.</div>
        <?php else: ?>
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
                                <li><strong>Delivery Time:</strong> <?=intval($service->delivery_time)?> days</li>
                                <li><strong>Photo Style:</strong> <?=htmlspecialchars($service->photo_style)?></li>
                                <li><strong>Equipment Provided:</strong> <?=($service->equipment_provided ? 'Yes' : 'No')?></li>
                                <li><strong>Location:</strong> <?=htmlspecialchars($service->location ?? 'Remote')?></li>
                                <p class="price">$<?=number_format($service->price, 2)?></p>
                            </ul>
                        </article>
                    </li>
                <?php } ?>
            </ul>
        <?php endif; ?>
    </section>
<?php }