<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $email = strtolower(cleanInput($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Plotësoni të gjitha fushat.';
    } elseif (!validateEmail($email)) {
        $error = 'Email-i nuk është valid.';
    } elseif (!validatePassword($password)) {
        $error = 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.';
    } elseif ($password !== $confirm) {
        $error = 'Fjalëkalimet nuk përputhen.';
    } else {
        try {
            $userRepo = app_repositories()['users'];

            if ($userRepo->emailExists($email)) {
                $error = 'Ky email përdoret tashmë. <a href="' . e(base_url('login.php')) . '">Hyni këtu</a>.';
            } else {
                $userRepo->create($name, $email, 'customer', $password);
                $success = 'Llogaria u krijua me sukses.';
            }
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'Regjistrimi nuk u përfundua. Provoni përsëri.';
        }
    }
}

$pageTitle = 'Regjistrim — ' . shopName();
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Krijo llogari</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
            <a href="<?php echo e(base_url('login.php')); ?>" class="btn btn-primary w-100">Hyr tani</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">Emri i plotë</label>
                    <input type="text" id="name" name="name" required maxlength="100"
                        value="<?php echo e($_POST['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required
                        value="<?php echo e($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Fjalëkalimi</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Përsërit fjalëkalimin</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Regjistrohu</button>
            </form>

            <p class="auth-link mt-3">
                Keni llogari? <a href="<?php echo e(base_url('login.php')); ?>">Hyni këtu</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
