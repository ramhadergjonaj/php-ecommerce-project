<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../includes/header.php';

$products = getProducts();
$sortOrder = $_GET['sort'] ?? 'asc';
$products = sortProductsByPrice($products, $sortOrder === 'desc' ? 'desc' : 'asc');
$eurRate = fetchUsdToEurRate();
?>

<div class="page-header">
    <h1>Të Gjitha Produktet</h1>

    <div class="sort-controls">
        <label for="sort">Rendit sipas çmimit: </label>
        <select id="sort" onchange="window.location.href='?sort=' + this.value">
            <option value="asc" <?php echo $sortOrder === 'asc' ? 'selected' : ''; ?>>Më i liri fillimisht</option>
            <option value="desc" <?php echo $sortOrder === 'desc' ? 'selected' : ''; ?>>Më i shtrenjti fillimisht</option>
        </select>
    </div>
</div>

<?php if ($eurRate): ?>
    <p class="api-note text-muted">Çmimet në EUR sipas kursit: <?php echo e(number_format($eurRate, 4)); ?></p>
<?php endif; ?>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <?php $eur = convertUsdToEur($product->getPrice(), $eurRate); ?>
        <div class="product-card">
            <img src="<?php echo e($product->getImage()); ?>"
                alt="<?php echo e($product->getName()); ?>" class="product-img">

            <div class="product-info">
                <span class="product-category"><?php echo e($product->getCategory()); ?></span>
                <h3 class="product-name"><?php echo e($product->getName()); ?></h3>
                <p class="product-price"><?php echo e($product->getFormattedPrice()); ?></p>
                <?php if ($eur !== null): ?>
                    <p class="product-price-eur">≈ <?php echo e(number_format($eur, 2)); ?> EUR</p>
                <?php endif; ?>
                <a href="<?php echo e(base_url('pages/product_details.php?id=' . $product->getId())); ?>"
                    class="btn btn-outline w-100">Shiko Detajet</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
