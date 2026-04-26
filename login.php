<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/validators.php';

if (isLoggedIn()) {
    header("Location: /index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = cleanInput($_POST['email']);
    $password = cleanInput($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Të gjitha fushat janë të detyrueshme.";
    } elseif (!validateEmail($email)) {
        $error = "Formati i email-it nuk është i saktë.";
    } else {
        $user = getUserByEmail($email);

        if ($user && $user->verifyPassword($password)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_role'] = $user->getRole();

            setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), "/");

            if ($user->getRole() === 'admin') {
                header("Location: /pages/dashboard.php");
            } else {
                header("Location: /index.php");
            }

            exit;
        }

        $error = "Kredencialet janë të gabuara.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Hyr në Llogari</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login.php" class="auth-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    placeholder="admin@shop.com ose user@shop.com"
                >
            </div>

            <div class="form-group">
                <label for="password">Fjalëkalimi:</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="admin123 ose user123"
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>
        </form>

        <div class="auth-hints">
            <p><strong>Admin:</strong> admin@shop.com / admin123</p>
            <p><strong>User:</strong> user@shop.com / user123</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>