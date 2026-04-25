<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../includes/header.php';

$products = getProducts();

$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

if ($sortOrder === 'desc') {
    $products = sortProductsByPrice($products, 'desc');
} else {
    $products = sortProductsByPrice($products, 'asc');
}
?>

<div class="page-header">
    <h1>Të Gjitha Produktet</h1>

    <div class="sort-controls">
        <label for="sort">Rendit sipas çmimit: </label>
        <select id="sort" onchange="window.location.href='?sort=' + this.value">
            <option value="asc" <?php echo $sortOrder === 'asc' ? 'selected' : ''; ?>>
                Më i liri fillimisht
            </option>
            <option value="desc" <?php echo $sortOrder === 'desc' ? 'selected' : ''; ?>>
                Më i shtrenjti fillimisht
            </option>
        </select>
    </div>
</div>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>