<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/mailer.php';
require_once __DIR__ . '/functions/messaging.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(cleanInput($_POST['email'] ?? ''));

    if ($email === '' || !validateEmail($email)) {
        $error = 'Vendosni një adresë email të vlefshme.';
    } else {
        try {
            $userRepo = app_repositories()['users'];
            $user = $userRepo->findByEmail($email);

            $success = 'Nëse ekziston një llogari me këtë email, do të merrni një kod brenda pak minutash.';

            if ($user && $user->getRole() === 'customer') {
                $code = app_repositories()['password_reset']->createCode($user->getId());
                $resetUrl = (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '') . base_url('reset-password.php');

                $body = "Përshëndetje " . $user->getName() . ",\n\n";
                $body .= "Kodi për rivendosjen e fjalëkalimit: $code\n";
                $body .= "Skadon pas 15 minutash.\n\nVendoseni këtu: $resetUrl\n";

                if (sendAppEmail($email, emailSubject('Rivendosje fjalëkalimi'), $body)) {
                    $_SESSION['reset_email'] = $email;
                } elseif (defined('APP_DEBUG') && APP_DEBUG) {
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_code_dev'] = $code;
                }
            }
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'Kërkesa nuk u përpunua. Provoni përsëri më vonë.';
        }
    }
}

$pageTitle = 'Rivendos fjalëkalimin — ' . shopName();
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Rivendos fjalëkalimin</h2>
        <p class="text-muted">Do t'ju dërgojmë një kod me 6 shifra në email.</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php if (!empty($_SESSION['reset_code_dev'])): ?>
                <p class="text-muted" style="font-size:0.85rem">[Debug] Kodi: <?php echo e($_SESSION['reset_code_dev']); ?></p>
                <?php unset($_SESSION['reset_code_dev']); ?>
            <?php endif; ?>
            <a href="<?php echo e(base_url('reset-password.php')); ?>" class="btn btn-primary w-100">Vazhdo</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email i llogarisë</label>
                    <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary w-100">Dërgo kodin</button>
            </form>
        <?php endif; ?>

        <p class="auth-link mt-3"><a href="<?php echo e(base_url('login.php')); ?>">&larr; Kthehu</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>