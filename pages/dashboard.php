<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

requireAdmin();

$repos = app_repositories();
$users = $repos['users']->findAll();
$productCount = $repos['products']->count();
$userCount = $repos['users']->count();
$newMessages = $repos['contact_messages']->countNew();
$pendingReturns = count($repos['return_requests']->findAllPending());

$currentUserObj = getUserByEmail($_SESSION['user_email']);

$pageTitle = 'Paneli — ' . shopName();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Paneli i administrimit</h1>
        <?php if ($currentUserObj instanceof Admin): ?>
            <p class="admin-greeting"><?php echo e($currentUserObj->getAdminGreeting()); ?></p>
        <?php endif; ?>

        <div class="dashboard-actions">
            <a href="<?php echo e(base_url('pages/admin/products.php')); ?>" class="btn btn-primary">Produktet</a>
            <a href="<?php echo e(base_url('pages/admin/categories.php')); ?>" class="btn btn-outline">Kategoritë</a>
            <a href="<?php echo e(base_url('pages/admin/users.php')); ?>" class="btn btn-outline">Përdoruesit</a>
            <a href="<?php echo e(base_url('pages/admin/orders.php')); ?>" class="btn btn-outline">Porositë</a>
            <a href="<?php echo e(base_url('pages/admin/returns.php')); ?>" class="btn btn-outline">
                Kthimet<?php if ($pendingReturns > 0): ?> (<?php echo $pendingReturns; ?>)<?php endif; ?>
            </a>
            <a href="<?php echo e(base_url('pages/admin/messages.php')); ?>" class="btn btn-outline">
                Mesazhet<?php if ($newMessages > 0): ?> (<?php echo $newMessages; ?>)<?php endif; ?>
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $productCount; ?></div>
            <div class="stat-label">Produkte</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $userCount; ?></div>
            <div class="stat-label">Përdorues</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $newMessages; ?></div>
            <div class="stat-label">Mesazhe të reja</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $pendingReturns; ?></div>
            <div class="stat-label">Kthime në pritje</div>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="card">
            <h3>Përdoruesit e fundit</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Emri</th>
                        <th>Email</th>
                        <th>Roli</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($users, -5) as $u): ?>
                        <tr>
                            <td><?php echo e($u->getName()); ?></td>
                            <td><?php echo e($u->getEmail()); ?></td>
                            <td>
                                <span class="badge <?php echo $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo e($u->getRole()); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
