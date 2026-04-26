<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if (!$product) {
    require_once __DIR__ . '/../includes/header.php';
    echo "<div class='alert alert-danger'>Produkti nuk u gjet! <a href='/pages/products.php'>Kthehu te produktet</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

setcookie('last_visited_product', $product->getName(), time() + (86400 * 7), "/");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="product-details-container">
    <a href="/pages/products.php" class="back-link">&larr; Kthehu te Produktet</a>

    <div class="product-details-grid">
        <div class="product-details-image">
            <img
                src="<?php echo htmlspecialchars($product->getImage()); ?>"
                alt="<?php echo htmlspecialchars($product->getName()); ?>"
            >
        </div>

        <div class="product-details-info">
            <span class="badge">
                <?php echo htmlspecialchars($product->getCategory()); ?>
            </span>

            <h1><?php echo htmlspecialchars($product->getName()); ?></h1>

            <p class="price-large">
                <?php echo htmlspecialchars($product->getFormattedPrice()); ?>
            </p>

            <div class="product-description">
                <h3>Përshkrimi</h3>
                <p><?php echo nl2br(htmlspecialchars($product->getDescription())); ?></p>
            </div>

            <?php if (isLoggedIn()): ?>
                <button
                    class="btn btn-primary btn-lg w-100 mt-4"
                    onclick="alert('Funksionaliteti i shportës nuk është implementuar akoma.')"
                >
                    Shto në Shportë
                </button>
            <?php else: ?>
                <a href="/login.php" class="btn btn-outline btn-lg w-100 mt-4">
                    Logohu për të Blerë
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>