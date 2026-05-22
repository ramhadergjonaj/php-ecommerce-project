<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

$pageTitle = shopName() . ' — Kryefaqja';

require_once __DIR__ . '/../includes/header.php';

try {
    $allProducts = getProducts();
    $featuredProducts = array_slice($allProducts, 0, 3);
    $eurRate = fetchUsdToEurRate();
} catch (Throwable $e) {
    $featuredProducts = [];
    $eurRate = null;
    echo '<div class="alert alert-danger">Katalogu nuk është i disponueshëm për momentin.</div>';
}
?>

<section class="hero">
    <div class="hero-content">
        <h1>Mirësevini në <?php echo e(shopName()); ?></h1>
        <p>Elektronikë, aksesorë dhe pajisje zyre — blerje të sigurta online.</p>
        <a href="<?php echo e(base_url('pages/products.php')); ?>" class="btn btn-primary btn-lg">Shfleto produktet</a>
    </div>
</section>

<?php if ($eurRate !== null): ?>
<section class="api-banner card">
    <h3>Çmimet në euro</h3>
    <p>Kursi i ditës: 1 USD = <strong><?php echo e(number_format($eurRate, 4)); ?> EUR</strong></p>
</section>
<?php endif; ?>

<section class="featured-products">
    <h2 class="section-title">Produkte të zgjedhura</h2>
    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
            <?php $eur = convertUsdToEur($product->getPrice(), $eurRate); ?>
            <div class="product-card">
                <img src="<?php echo e($product->getImage()); ?>" alt="<?php echo e($product->getName()); ?>" class="product-img">
                <div class="product-info">
                    <span class="product-category"><?php echo e($product->getCategory()); ?></span>
                    <h3 class="product-name"><?php echo e($product->getName()); ?></h3>
                    <p class="product-price"><?php echo e($product->getFormattedPrice()); ?></p>
                    <?php if ($eur !== null): ?>
                        <p class="product-price-eur">≈ <?php echo e(number_format($eur, 2)); ?> EUR</p>
                    <?php endif; ?>
                    <a href="<?php echo e(base_url('pages/product_details.php?id=' . $product->getId())); ?>"
                        class="btn btn-outline w-100">Detajet</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
