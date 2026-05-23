<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/orders.php';

requireLogin();

if (isAdmin()) {
    redirect_to('pages/dashboard.php');
}

$orderRepo = app_repositories()['orders'];
$returnRepo = app_repositories()['return_requests'];
$orders = $orderRepo->findByUserId((int) $_SESSION['user_id']);

require_once __DIR__ . '/../includes/header.php';

$flashOk = $_SESSION['flash_success'] ?? null;
$flashErr = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="page-header">
    <h1>Porositë e mia</h1>
    <p class="page-subtitle">Kthim i drejtpërdrejtë brenda <?php echo ORDER_RETURN_HOURS; ?> orëve; pas kësaj kërkesë te admini.</p>
</div>

<?php if ($flashOk): ?>
    <div class="alert alert-success"><?php echo e($flashOk); ?></div>
<?php endif; ?>
<?php if ($flashErr): ?>
    <div class="alert alert-danger"><?php echo e($flashErr); ?></div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="card">
        <p>Nuk keni porosi ende.</p>
        <a href="<?php echo e(base_url('pages/products.php')); ?>" class="btn btn-primary">Bli tani</a>
    </div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <?php
        $pendingReturn = $returnRepo->findPendingByOrder((int) $order['id']);
        $canDirect = canDirectReturnOrder($order);
        $canRequest = canRequestAdminReturn($order, $pendingReturn);
        ?>
        <div class="card order-card">
            <div class="order-card-header">
                <h3>Porosia #<?php echo (int) $order['id']; ?></h3>
                <span class="badge badge-<?php echo e($order['status']); ?>"><?php echo e($order['status']); ?></span>
            </div>
            <p><strong>Data:</strong> <?php echo e(date('d/m/Y H:i', strtotime($order['created_at']))); ?></p>
            <p><strong>Artikuj:</strong> <?php echo (int) $order['item_count']; ?> |
                <strong>Totali:</strong> $<?php echo e(number_format((float) $order['total_amount'], 2)); ?></p>

            <?php if (in_array($order['status'], ['pending', 'completed'], true)): ?>
                <p class="return-timer"><?php echo e(orderReturnTimeLeftLabel($order['created_at'])); ?></p>
            <?php endif; ?>

            <?php if ($canDirect): ?>
                <form method="POST" action="<?php echo e(base_url('actions/order-return.php')); ?>" class="inline-return-form"
                    onsubmit="return confirm('Kthe porosinë #<?php echo (int) $order['id']; ?>?');">
                    <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                    <input type="hidden" name="action" value="direct_return">
                    <button type="submit" class="btn btn-danger btn-sm">Kthe porosinë (brenda 7 orëve)</button>
                </form>
            <?php elseif ($pendingReturn): ?>
                <p class="text-muted">Kërkesa për kthim është <strong>në pritje</strong> të miratimit nga admini.</p>
            <?php elseif ($canRequest): ?>
                <form method="POST" action="<?php echo e(base_url('actions/order-return.php')); ?>" class="return-request-form">
                    <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                    <input type="hidden" name="action" value="request_return">
                    <div class="form-group">
                        <label>Arsyeja e kthimit (për adminin)</label>
                        <textarea name="reason" rows="2" required placeholder="P.sh. produkt i dëmtuar..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm">Kërko kthim nga admini</button>
                </form>
                <p class="text-muted mt-2">
                    Ose <a href="<?php echo e(base_url('pages/contact.php?order=' . (int) $order['id'])); ?>">kontaktoni adminin</a>.
                </p>
            <?php elseif ($order['status'] === 'returned'): ?>
                <p class="text-muted">Kjo porosi është kthyer.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
