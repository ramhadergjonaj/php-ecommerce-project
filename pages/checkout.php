<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../functions/messaging.php';

requireLogin();

if (isAdmin()) {
    redirect_to('pages/dashboard.php');
}

$lines = getCartLineItems();

if (empty($lines)) {
    redirect_to('pages/cart.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $items = [];

        foreach ($lines as $line) {
            $items[] = [
                'product_id' => $line['product']->getId(),
                'quantity' => $line['quantity'],
                'unit_price' => $line['product']->getPrice(),
            ];
        }

        $orderRepo = new OrderRepository(getDB());
        $orderId = $orderRepo->createOrder((int) $_SESSION['user_id'], $items);
        $total = getCartTotal();

        $orderSummary = "Porosia #$orderId\nTotal: $" . number_format($total, 2) . "\n\n";
        foreach ($lines as $line) {
            $orderSummary .= '- ' . $line['product']->getName()
                . ' x' . $line['quantity']
                . ' = $' . number_format($line['subtotal'], 2) . "\n";
        }

        sendEmailToCustomer(
            $_SESSION['user_email'],
            $_SESSION['user_name'],
            'Konfirmim porosie #' . $orderId,
            "Porosia juaj u regjistrua me sukses:\n\n" . $orderSummary
        );

        clearCart();
        $_SESSION['flash_success'] = 'Porosia #' . $orderId . ' u krijua me sukses!';
        redirect_to('pages/my-orders.php');
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = 'Porosia nuk u ruajt. Provoni përsëri.';
    }
}

$total = getCartTotal();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Përfundimi i porosisë</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="checkout-grid">
    <div class="card">
        <h3>Përmbledhje</h3>
        <ul class="checkout-list">
            <?php foreach ($lines as $line): ?>
                <li>
                    <?php echo e($line['product']->getName()); ?>
                    × <?php echo $line['quantity']; ?>
                    — $<?php echo e(number_format($line['subtotal'], 2)); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="cart-total">Totali: <strong>$<?php echo e(number_format($total, 2)); ?></strong></p>
    </div>

    <div class="card">
        <h3>Konfirmo</h3>
        <p>Porosia do të ruhet në databazë (tabelat <code>orders</code> dhe <code>order_items</code>).</p>
        <p class="text-muted">Do të merrni email konfirmimi pas porosisë.</p>

        <form method="POST">
            <button type="submit" class="btn btn-primary w-100">Konfirmo porosinë</button>
            <a href="<?php echo e(base_url('pages/cart.php')); ?>" class="btn btn-outline w-100 mt-2">Kthehu te shporta</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
