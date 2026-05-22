<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/messaging.php';

requireAdmin();

$returnRepo = app_repositories()['return_requests'];
$orderRepo = app_repositories()['orders'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['request_id'] ?? 0);
    $decision = $_POST['decision'] ?? '';
    $note = cleanInput($_POST['admin_note'] ?? '');

    $request = $returnRepo->findById($id);

    if (!$request || $request['status'] !== 'pending') {
        $message = 'Kërkesa nuk u gjet ose është përpunuar.';
    } elseif ($decision === 'approve') {
        $returnRepo->resolve($id, 'approved', $note ?: null);
        $orderRepo->markReturned((int) $request['order_id']);
        sendEmailToCustomer(
            $request['user_email'],
            $request['user_name'],
            'Porosia #' . $request['order_id'] . ' u kthye',
            "Kërkesa juaj për kthimin e porosisë #" . $request['order_id'] . " u miratua. Porosia është shënuar si e kthyer." . ($note ? "\n\nShënim: $note" : '')
        );
        $message = 'Kërkesa u miratua. Email u dërgua te klienti.';
    } elseif ($decision === 'reject') {
        $returnRepo->resolve($id, 'rejected', $note ?: null);
        sendEmailToCustomer(
            $request['user_email'],
            $request['user_name'],
            'Kërkesa për kthim u refuzua',
            "Kërkesa për kthimin e porosisë #" . $request['order_id'] . " nuk u miratua." . ($note ? "\n\nArsyeja: $note" : "\n\nNa kontaktoni nëse keni pyetje.")
        );
        $message = 'Kërkesa u refuzua. Email u dërgua te klienti.';
    }
}

$pending = $returnRepo->findAllPending();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Kërkesat për kthim porosish</h1>
    <a href="<?php echo e(base_url('pages/dashboard.php')); ?>" class="back-link">&larr; Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo e($message); ?></div>
<?php endif; ?>

<?php if (empty($pending)): ?>
    <div class="card"><p>Nuk ka kërkesa në pritje.</p></div>
<?php else: ?>
    <?php foreach ($pending as $req): ?>
        <div class="card return-request-card">
            <h3>Porosia #<?php echo (int) $req['order_id']; ?> — <?php echo e($req['user_name']); ?></h3>
            <p><strong>Email:</strong> <?php echo e($req['user_email']); ?></p>
            <p><strong>Data porosisë:</strong> <?php echo e(date('d/m/Y H:i', strtotime($req['order_date']))); ?></p>
            <p><strong>Totali:</strong> $<?php echo e(number_format((float) $req['total_amount'], 2)); ?></p>
            <p><strong>Arsyeja:</strong> <?php echo nl2br(e($req['reason'])); ?></p>
            <p class="text-muted">Kërkesa: <?php echo e(date('d/m/Y H:i', strtotime($req['created_at']))); ?></p>

            <form method="POST" class="return-admin-form">
                <input type="hidden" name="request_id" value="<?php echo (int) $req['id']; ?>">
                <div class="form-group">
                    <label>Shënim admin (opsional)</label>
                    <input type="text" name="admin_note" maxlength="255" placeholder="Mesazh për klientin">
                </div>
                <button type="submit" name="decision" value="approve" class="btn btn-primary">Mirato kthimin</button>
                <button type="submit" name="decision" value="reject" class="btn btn-danger"
                    onclick="return confirm('Refuzoni kërkesën?');">Refuzo</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
