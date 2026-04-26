<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/validators.php';

if (isLoggedIn()) {
    header("Location: /index.php");
    exit;
}

$error = '';

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