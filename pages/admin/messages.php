<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/messaging.php';

requireAdmin();

$repo = app_repositories()['contact_messages'];
$message = '';
$error = '';
$viewId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$viewMsg = $viewId > 0 ? $repo->findById($viewId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    $id = (int) ($_POST['message_id'] ?? 0);
    $reply = cleanInput($_POST['admin_reply'] ?? '');
    $msg = $repo->findById($id);

    if (!$msg || $reply === '') {
        $error = 'Mesazhi ose përgjigja mungon.';
    } else {
        $subject = 'Re: ' . $msg['subject'];
        $sent = sendEmailToCustomer($msg['email'], $msg['name'], $subject, $reply);

        if ($sent) {
            $repo->saveReply($id, $reply);
            $message = 'Përgjigja u dërgua me email te ' . $msg['email'];
            $viewMsg = $repo->findById($id);
        } else {
            $error = 'Email nuk u dërgua. Kontrolloni konfigurimin SMTP.';
        }
    }
}

$allMessages = $repo->findAll();
$newCount = $repo->countNew();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Mesazhet e klientëve</h1>
    <p class="page-subtitle">Lexoni kërkesat dhe përgjigjuni me email.</p>
    <a href="<?php echo e(base_url('pages/dashboard.php')); ?>" class="back-link">&larr; Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="admin-grid">
    <div class="card">
        <h3>Inbox <?php if ($newCount > 0): ?><span class="cart-badge"><?php echo $newCount; ?></span><?php endif; ?></h3>
        <?php if (empty($allMessages)): ?>
            <p class="text-muted">Nuk ka mesazhe.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Emri</th>
                        <th>Tema</th>
                        <th>Statusi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allMessages as $m): ?>
                        <tr>
                            <td><?php echo e(date('d/m/Y', strtotime($m['created_at']))); ?></td>
                            <td><?php echo e($m['name']); ?></td>
                            <td><?php echo e($m['subject']); ?></td>
                            <td>
                                <span class="badge <?php echo ($m['status'] ?? 'new') === 'replied' ? 'badge-completed' : 'badge-pending'; ?>">
                                    <?php echo e($m['status'] ?? 'new'); ?>
                                </span>
                            </td>
                            <td><a href="?id=<?php echo (int) $m['id']; ?>" class="btn btn-outline btn-sm">Hap</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if ($viewMsg): ?>
        <div class="card">
            <h3><?php echo e($viewMsg['subject']); ?></h3>
            <p><strong>Nga:</strong> <?php echo e($viewMsg['name']); ?> &lt;<?php echo e($viewMsg['email']); ?>&gt;</p>
            <?php if (!empty($viewMsg['phone'])): ?>
                <p><strong>Tel:</strong> <?php echo e($viewMsg['phone']); ?></p>
            <?php endif; ?>
            <p><strong>Data:</strong> <?php echo e(date('d/m/Y H:i', strtotime($viewMsg['created_at']))); ?></p>
            <div class="message-box"><?php echo nl2br(e($viewMsg['message'])); ?></div>

            <?php if (!empty($viewMsg['admin_reply'])): ?>
                <div class="message-box message-reply">
                    <strong>Përgjigja juaj (<?php echo e(date('d/m/Y H:i', strtotime($viewMsg['replied_at']))); ?>):</strong>
                    <p><?php echo nl2br(e($viewMsg['admin_reply'])); ?></p>
                </div>
            <?php else: ?>
                <form method="POST" class="admin-form mt-3">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="message_id" value="<?php echo (int) $viewMsg['id']; ?>">
                    <div class="form-group">
                        <label>Përgjigju me email</label>
                        <textarea name="admin_reply" rows="4" required placeholder="Shkruani përgjigjen për klientin..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Dërgo përgjigjen</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
//messages