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

        <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">
                <?php echo count($products); ?>
            </div>
            <div class="stat-label">Totali i Produkteve</div>
        </div>

        <div class="stat-card">
            <div class="stat-value">
                <?php echo count($users); ?>
            </div>
            <div class="stat-label">Përdorues të Regjistruar</div>
        </div>

        <div class="stat-card">
            <div class="stat-value">
                <?php echo isset($_COOKIE['last_login']) ? date('d/m/Y H:i', strtotime($_COOKIE['last_login'])) : 'E panjohur'; ?>
            </div>
            <div class="stat-label">Hyrja e Fundit</div>
        </div>

        <div class="stat-card">
            <div class="stat-value">
                <?php echo isset($_COOKIE['last_visited_product']) ? htmlspecialchars($_COOKIE['last_visited_product']) : 'Asnjë'; ?>
            </div>
            <div class="stat-label">Produkti i parë së fundmi</div>
        </div>
    </div>

        <div class="dashboard-sections">
        <div class="card">
            <h3>Lista e Përdoruesve</h3>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Emri</th>
                        <th>Email</th>
                        <th>Roli</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u->getId(); ?></td>

                            <td>
                                <?php echo htmlspecialchars($u->getName()); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($u->getEmail()); ?>
                            </td>

                            <td>
                                <span class="badge <?php echo $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo htmlspecialchars($u->getRole()); ?>
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