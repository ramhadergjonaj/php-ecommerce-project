<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

requireAdmin();

require_once __DIR__ . '/../includes/header.php';

$users = getUsers();
$products = getProducts();

$currentUserObj = getUserByEmail($_SESSION['user_email']);
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Dashboard i Administratorit</h1>

        <?php if ($currentUserObj instanceof Admin): ?>
            <p class="admin-greeting">
                <?php echo htmlspecialchars($currentUserObj->getAdminGreeting()); ?>
            </p>
        <?php endif; ?>
    </div>