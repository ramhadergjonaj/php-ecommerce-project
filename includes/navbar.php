<header class="navbar">
    <div class="navbar-container">
        <a href="/index.php" class="brand">PHP<span>Shop</span></a>

        <nav class="nav-links">
            <a href="/pages/products.php">Produktet</a>

            <?php if (isAdmin()): ?>
                <a href="/pages/dashboard.php" class="nav-admin">Dashboard</a>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <span class="user-greeting">
                    Përshëndetje, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="/logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="/login.php" class="btn-login">Login</a>
            <?php endif; ?>

            <button id="theme-toggle" class="btn-theme">
                <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? '☀️' : '🌙'; ?>
            </button>
        </nav>
    </div>
</header>

<script>
    document.getElementById('theme-toggle').addEventListener('click', function () {
        let currentTheme = document.documentElement.getAttribute('data-theme');
        let newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', newTheme);
        document.cookie = "theme=" + newTheme + "; path=/; max-age=" + (60 * 60 * 24 * 30);

        this.innerText = newTheme === 'dark' ? '☀️' : '🌙';
    });
</script>