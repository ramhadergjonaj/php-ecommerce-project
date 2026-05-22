<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($id);

if (!$product) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Produkti nuk u gjet! <a href="' . e(base_url('pages/products.php')) . '">Kthehu te produktet</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

setcookie('last_visited_product', $product->getName(), time() + (86400 * 7), base_url() ?: '/');
$eur = convertUsdToEur($product->getPrice());

require_once __DIR__ . '/../includes/header.php';
?>

<div class="product-details-container">
    <a href="<?php echo e(base_url('pages/products.php')); ?>" class="back-link">&larr; Kthehu te Produktet</a>

    <div class="product-details-grid">
        <div class="product-details-image">
            <img src="<?php echo e($product->getImage()); ?>" alt="<?php echo e($product->getName()); ?>">
        </div>

        <div class="product-details-info">
            <span class="badge"><?php echo e($product->getCategory()); ?></span>
            <h1><?php echo e($product->getName()); ?></h1>
            <p class="price-large"><?php echo e($product->getFormattedPrice()); ?></p>
            <?php if ($eur !== null): ?>
                <p class="product-price-eur">≈ <?php echo e(number_format($eur, 2)); ?> EUR</p>
            <?php endif; ?>

            <div class="product-description">
                <h3>Përshkrimi</h3>
                <p><?php echo nl2br(e($product->getDescription())); ?></p>
            </div>

            <?php if (isLoggedIn() && !isAdmin()): ?>
                <form method="POST" action="<?php echo e(base_url('actions/cart-add.php')); ?>" class="add-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $product->getId(); ?>">
                    <input type="hidden" name="redirect" value="pages/product_details.php?id=<?php echo $product->getId(); ?>">
                    <div class="form-group">
                        <label for="quantity">Sasia</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" class="qty-input">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Shto në Shportë</button>
                </form>
                <a href="<?php echo e(base_url('pages/cart.php')); ?>" class="btn btn-outline w-100 mt-2">Shiko shportën</a>
            <?php elseif (isAdmin()): ?>
                <p class="text-muted mt-4">Si admin, menaxhoni produktet nga Dashboard.</p>
            <?php else: ?>
                <a href="<?php echo e(base_url('login.php')); ?>" class="btn btn-outline btn-lg w-100 mt-4">
                    Logohu për të Blerë
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
