<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';

requireAdmin();

$orderRepo = app_repositories()['orders'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($orderId > 0 && $orderRepo->updateStatus($orderId, $status)) {
        $message = 'Statusi i porosisë #' . $orderId . ' u përditësua.';
    }
}

$orders = $orderRepo->findAllWithUser();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Porositë e klientëve</h1>
    <a href="<?php echo e(base_url('pages/dashboard.php')); ?>" class="back-link">&larr; Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo e($message); ?></div>
<?php endif; ?>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Klienti</th>
                <th>Data</th>
                <th>Artikuj</th>
                <th>Totali</th>
                <th>Statusi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo (int) $order['id']; ?></td>
                    <td><?php echo e($order['user_name']); ?><br><small><?php echo e($order['user_email']); ?></small></td>
                    <td><?php echo e(date('d/m/Y H:i', strtotime($order['created_at']))); ?></td>
                    <td><?php echo (int) $order['item_count']; ?></td>
                    <td>$<?php echo e(number_format((float) $order['total_amount'], 2)); ?></td>
                    <td>
                        <form method="POST" class="inline-form status-form">
                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <?php foreach (['pending', 'completed', 'cancelled', 'returned'] as $st): ?>
                                    <option value="<?php echo $st; ?>" <?php echo $order['status'] === $st ? 'selected' : ''; ?>>
                                        <?php echo $st; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
//orders