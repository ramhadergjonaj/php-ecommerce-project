<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../includes/header.php';

$allProducts = getProducts();
$featuredProducts = array_slice($allProducts, 0, 3);
?>

<section class="hero">
    <div class="hero-content">
        <h1>Mirësevini në PHP Shop</h1>
        <p>Dyqani juaj i preferuar elektronik i ndërtuar me PHP OOP dhe pa databazë.</p>
        <a href="/pages/products.php" class="btn btn-primary btn-lg">Shiko Produktet</a>
    </div>
</section>

<section class="featured-products">
    <h2 class="section-title">Produkte të Spikatura</h2>

    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <img
                    src="<?php echo htmlspecialchars($product->getImage()); ?>"
                    alt="<?php echo htmlspecialchars($product->getName()); ?>"
                    class="product-img"
                >

                <div class="product-info">
                    <span class="product-category">
                        <?php echo htmlspecialchars($product->getCategory()); ?>
                    </span>

                    <h3 class="product-name">
                        <?php echo htmlspecialchars($product->getName()); ?>
                    </h3>

                    <p class="product-price">
                        <?php echo htmlspecialchars($product->getFormattedPrice()); ?>
                    </p>

                    <a href="/pages/product_details.php?id=<?php echo $product->getId(); ?>" class="btn btn-outline w-100">
                        Shiko Detajet
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>