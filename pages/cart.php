<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

requireLogin();

if (isAdmin()) {
    redirect_to('pages/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        foreach ($_POST['qty'] ?? [] as $productId => $qty) {
            updateCartItem((int) $productId, (int) $qty);
        }
        $_SESSION['flash_success'] = 'Shporta u përditësua.';
    } elseif ($action === 'remove') {
        removeFromCart((int) ($_POST['product_id'] ?? 0));
        $_SESSION['flash_success'] = 'Produkti u hoq nga shporta.';
    }

    redirect_to('pages/cart.php');
}

$lines = getCartLineItems();
$total = getCartTotal();

require_once __DIR__ . '/../includes/header.php';

$flashOk = $_SESSION['flash_success'] ?? null;
$flashErr = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="page-header">
    <h1>Shporta ime</h1>
    <a href="<?php echo e(base_url('pages/products.php')); ?>" class="back-link">&larr; Vazhdo blerjen</a>
</div>

<?php if ($flashOk): ?>
    <div class="alert alert-success"><?php echo e($flashOk); ?></div>
<?php endif; ?>
<?php if ($flashErr): ?>
    <div class="alert alert-danger"><?php echo e($flashErr); ?></div>
<?php endif; ?>

<?php if (empty($lines)): ?>
    <div class="card empty-cart">
        <p>Shporta juaj është bosh.</p>
        <a href="<?php echo e(base_url('pages/products.php')); ?>" class="btn btn-primary">Shiko produktet</a>
    </div>
<?php else: ?>
    <form method="POST" class="card">
        <input type="hidden" name="action" value="update">
        <table class="data-table cart-table">
            <thead>
                <tr>
                    <th>Produkti</th>
                    <th>Çmimi</th>
                    <th>Sasia</th>
                    <th>Nëntotali</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $line): ?>
                    <?php $p = $line['product']; ?>
                    <tr>
                        <td>
                            <div class="cart-product-cell">
                                <img src="<?php echo e($p->getImage()); ?>" alt="" class="cart-thumb">
                                <span><?php echo e($p->getName()); ?></span>
                            </div>
                        </td>
                        <td><?php echo e($p->getFormattedPrice()); ?></td>
                        <td>
                            <input type="number" name="qty[<?php echo $p->getId(); ?>]"
                                value="<?php echo $line['quantity']; ?>" min="1" max="99" class="qty-input">
                        </td>
                        <td>$<?php echo e(number_format($line['subtotal'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-footer">
            <p class="cart-total">Totali: <strong>$<?php echo e(number_format($total, 2)); ?></strong></p>
            <div class="cart-actions">
                <button type="submit" class="btn btn-outline">Rifresko sasitë</button>
                <a href="<?php echo e(base_url('pages/checkout.php')); ?>" class="btn btn-primary">Përfundo porosinë</a>
            </div>
        </div>
    </form>

    <p class="text-muted">Për të hequr një produkt, vendosni sasinë <strong>0</strong> dhe klikoni "Rifresko sasitë".</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
