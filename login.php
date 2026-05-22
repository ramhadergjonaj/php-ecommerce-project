<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/google_auth.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
$googleUrl = googleAuthUrl();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(cleanInput($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Plotësoni email-in dhe fjalëkalimin.';
    } elseif (!validateEmail($email)) {
        $error = 'Formati i email-it nuk është i saktë.';
    } else {
        try {
            $user = getUserByEmail($email);

            if ($user && $user->verifyPassword($password)) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getName();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_role'] = $user->getRole();

                setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), base_url() ?: '/');

                if ($user->getRole() === 'admin') {
                    redirect_to('pages/dashboard.php');
                }

                redirect_to('index.php');
            }

            $error = 'Email ose fjalëkalim i gabuar.';
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'Shërbimi nuk është i disponueshëm për momentin. Provoni përsëri.';
        }
    }
}

$pageTitle = 'Hyrje — ' . shopName();
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Hyr në llogari</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(base_url('login.php')); ?>" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Hyr</button>
        </form>

        <?php if ($googleUrl): ?>
            <div class="auth-divider"><span>ose</span></div>
            <a href="<?php echo e(base_url('auth/google.php')); ?>" class="btn btn-google w-100">Vazhdo me Google</a>
        <?php endif; ?>

        <p class="auth-link mt-3">
            Nuk keni llogari? <a href="<?php echo e(base_url('register.php')); ?>">Krijoni një</a><br>
            <a href="<?php echo e(base_url('forgot-password.php')); ?>">Keni harruar fjalëkalimin?</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
