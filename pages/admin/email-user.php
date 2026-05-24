<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/messaging.php';

requireAdmin();

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
$user = $userId > 0 ? app_repositories()['users']->findById($userId) : null;

if (!$user || $user->getRole() === 'admin') {
    redirect_to('pages/admin/users.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = cleanInput($_POST['subject'] ?? '');
    $body = cleanInput($_POST['body'] ?? '');

    if ($subject === '' || $body === '') {
        $error = 'Tema dhe mesazhi janë të detyrueshme.';
    } elseif (sendEmailToCustomer($user->getEmail(), $user->getName(), $subject, $body)) {
        $message = 'Email u dërgua te ' . $user->getEmail();
    } else {
        $error = 'Dërgimi dështoi. Verifikoni SMTP.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Dërgo email klientit</h1>
    <p class="page-subtitle"><?php echo e($user->getName()); ?> — <?php echo e($user->getEmail()); ?></p>
    <a href="<?php echo e(base_url('pages/admin/users.php')); ?>" class="back-link">&larr; Përdoruesit</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="card contact-card">
    <form method="POST" class="admin-form">
        <div class="form-group">
            <label>Tema</label>
            <input type="text" name="subject" required maxlength="200"
                value="<?php echo e($_POST['subject'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Mesazhi</label>
            <textarea name="body" rows="6" required><?php echo e($_POST['body'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Dërgo email</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
//email-user.php