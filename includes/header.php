<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../functions/helpers.php';


$theme = $_COOKIE['theme'] ?? 'light';
$pageTitle = $pageTitle ?? shopName();
?>
<!DOCTYPE html>
<html lang="sq" data-theme="<?php echo e($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo e(base_url('assets/css/style.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
if (file_exists(__DIR__ . '/navbar.php')) {
    require_once __DIR__ . '/navbar.php';
}
?>
<main class="container">
