<?php
require_once __DIR__ . '/config/app.php';

$homeFile = __DIR__ . '/pages/home.php';

if (file_exists($homeFile)) {
    require_once $homeFile;
    exit;
}

require_once __DIR__ . '/includes/auth.php';
$pageTitle = shopName();
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1><?php echo e(shopName()); ?></h1>
    <p><a href="<?php echo e(base_url('pages/home.php')); ?>">Hyni në dyqan</a></p>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
