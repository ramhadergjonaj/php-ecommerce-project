<?php
if (isLoggedIn() && !isAdmin()) {
    require_once __DIR__ . '/cart.php';
    $cartCount = getCartCount();
}
?>
<header class="navbar">
    <div class="navbar-container">
        <a href="<?php echo e(base_url('index.php')); ?>" class="brand">PHP<span>Shop</span></a>

        <nav class="nav-links">
            <a href="<?php echo e(base_url('pages/products.php')); ?>">Produktet</a>
            <?php if (!isAdmin()): ?>
                <a href="<?php echo e(base_url('pages/contact.php')); ?>">Kontakti</a>
            <?php endif; ?>

            <?php if (isLoggedIn() && !isAdmin()): ?>
                <a href="<?php echo e(base_url('pages/cart.php')); ?>" class="nav-cart">
                    Shporta
                    <?php if (!empty($cartCount)): ?>
                        <span class="cart-badge"><?php echo (int) $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(base_url('pages/my-orders.php')); ?>">Porositë</a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <a href="<?php echo e(base_url('pages/dashboard.php')); ?>" class="nav-admin">Dashboard</a>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <span class="user-greeting">
                    Përshëndetje, <?php echo e($_SESSION['user_name']); ?>
                </span>
                <a href="<?php echo e(base_url('logout.php')); ?>" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="<?php echo e(base_url('register.php')); ?>">Regjistrohu</a>
                <a href="<?php echo e(base_url('login.php')); ?>" class="btn-login">Login</a>
            <?php endif; ?>

            <button id="theme-toggle" class="btn-theme" type="button">
                <?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '☀️' : '🌙'; ?>
            </button>
        </nav>
    </div>
</header>

<script>
    document.getElementById('theme-toggle').addEventListener('click', function () {
        let currentTheme = document.documentElement.getAttribute('data-theme');
        let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        let base = <?php echo json_encode(base_url() ?: ''); ?>;

        document.documentElement.setAttribute('data-theme', newTheme);
        document.cookie = "theme=" + newTheme + "; path=" + (base || '/') + "; max-age=" + (60 * 60 * 24 * 30);
        this.innerText = newTheme === 'dark' ? '☀️' : '🌙';
    });
</script>
