<?php
$homeFile = __DIR__ . '/pages/home.php';

if (file_exists($homeFile)) {
    require_once $homeFile;
    exit;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>PHP Shop</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container">
        <section class="hero">
            <h1>PHP Shop</h1>
            <p>Projekti është duke u ndërtuar.</p>
        </section>
    </main>
</body>
</html>