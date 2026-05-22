<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$error = '';
$success = '';
$prefillEmail = $_SESSION['reset_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(cleanInput($_POST['email'] ?? ''));
    $code = cleanInput($_POST['code'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if ($email === '' || $code === '' || $password === '') {
        $error = 'Plotësoni të gjitha fushat.';
    } elseif (!validateEmail($email)) {
        $error = 'Email-i nuk është valid.';
    } elseif (!validateResetCode($code)) {
        $error = 'Kodi duhet të jetë 6 shifra.';
    } elseif (!validatePassword($password)) {
        $error = 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.';
    } elseif ($password !== $confirm) {
        $error = 'Fjalëkalimet nuk përputhen.';
    } else {
        try {
            $userRepo = app_repositories()['users'];
            $user = $userRepo->findByEmail($email);

            if (!$user || $user->getRole() !== 'customer') {
                $error = 'Kodi ose email-i nuk është i saktë.';
            } elseif (!app_repositories()['password_reset']->verifyCode($user->getId(), $code)) {
                $error = 'Kodi është i pavlefshëm ose ka skaduar (15 min). Kërkoni kod të ri.';
            } else {
                $userRepo->updatePassword($user->getId(), $password);
                unset($_SESSION['reset_email']);
                $success = 'Fjalëkalimi u ndryshua! Tani mund të hyni.';
            }
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'Ndodhi një gabim. Kontrolloni databazën.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Rivendos fjalëkalimin</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
            <a href="<?php echo e(base_url('login.php')); ?>" class="btn btn-primary w-100">Login</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required
                        value="<?php echo e($_POST['email'] ?? $prefillEmail); ?>">
                </div>

                <div class="form-group">
                    <label for="code">Kodi nga email (6 shifra)</label>
                    <input type="text" id="code" name="code" required maxlength="6" pattern="\d{6}"
                        placeholder="123456" inputmode="numeric">
                </div>

                <div class="form-group">
                    <label for="password">Fjalëkalimi i ri</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Përsërit fjalëkalimin</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary w-100">Ndrysho fjalëkalimin</button>
            </form>

            <p class="auth-link mt-3">
                <a href="<?php echo e(base_url('forgot-password.php')); ?>">Kërko kod të ri</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
