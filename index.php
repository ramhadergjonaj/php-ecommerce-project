<?php
$homeFile = __DIR__ . '/pages/home.php';

if (file_exists($homeFile)) {
    require_once $homeFile;
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>PHP Shop</h1>
    <p>Projekti është duke u ndërtuar.</p>
</section>
<?php
require_once __DIR__ . '/includes/footer.php';