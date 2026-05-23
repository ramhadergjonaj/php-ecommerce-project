<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

if (isAdmin()) {
    redirect_to('pages/dashboard.php');
}

$message = '';
$error = '';
$orderRef = isset($_GET['order']) ? (int) $_GET['order'] : 0;
$defaultSubject = $orderRef > 0 ? 'Kthim porosie #' . $orderRef : '';
$defaultBody = $orderRef > 0 ? "Dëshiroj informacion për kthimin e porosisë #$orderRef.\n" : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $subject = cleanInput($_POST['subject'] ?? '');
    $body = cleanInput($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $body === '') {
        $error = 'Plotësoni fushat e detyrueshme.';
    } elseif (!validateEmail($email)) {
        $error = 'Adresa e email-it nuk është e vlefshme.';
    } elseif ($phone !== '' && !validatePhoneNumber($phone)) {
        $error = 'Numri i telefonit nuk është i vlefshëm.';
    } else {
        $userId = isLoggedIn() ? (int) $_SESSION['user_id'] : null;
        $result = sendContactEmailAndSave($name, $email, $phone ?: null, $subject, $body, $userId);

        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['error'];
        }
    }
}

$pageTitle = 'Kontakt — ' . shopName();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Na kontaktoni</h1>
    <p class="page-subtitle">Dërgoni pyetje ose kërkesa — përgjigjemi me email.</p>
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
            <label>Emri</label>
            <input type="text" name="name" required value="<?php echo e(isLoggedIn() ? $_SESSION['user_name'] : ($_POST['name'] ?? '')); ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo e(isLoggedIn() ? $_SESSION['user_email'] : ($_POST['email'] ?? '')); ?>">
        </div>
        <div class="form-group">
            <label>Telefoni</label>
            <input type="text" name="phone" value="<?php echo e($_POST['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Tema</label>
            <input type="text" name="subject" required maxlength="200"
                value="<?php echo e($_POST['subject'] ?? $defaultSubject); ?>">
        </div>
        <div class="form-group">
            <label>Mesazhi</label>
            <textarea name="message" rows="5" required><?php echo e($_POST['message'] ?? $defaultBody); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Dërgo</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
