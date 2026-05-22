<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';

requireAdmin();

$userRepo = app_repositories()['users'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $name = cleanInput($_POST['name'] ?? '');
            $email = strtolower(cleanInput($_POST['email'] ?? ''));
            $role = $_POST['role'] ?? 'customer';
            $password = $_POST['password'] ?? '';

            if ($name === '' || $email === '' || $password === '') {
                throw new InvalidArgumentException('Të gjitha fushat janë të detyrueshme.');
            }

            if (!validateEmail($email)) {
                throw new InvalidArgumentException('Email-i nuk është valid.');
            }

            if (!in_array($role, ['admin', 'customer'], true)) {
                throw new InvalidArgumentException('Roli nuk është valid.');
            }

            if ($userRepo->emailExists($email)) {
                throw new InvalidArgumentException('Ky email ekziston tashmë.');
            }

            $userRepo->create($name, $email, $role, $password);
            $message = 'Përdoruesi u krijua.';
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $email = strtolower(cleanInput($_POST['email'] ?? ''));
            $role = $_POST['role'] ?? 'customer';
            $newPassword = $_POST['password'] ?? '';

            if ($id <= 0 || $name === '' || $email === '') {
                throw new InvalidArgumentException('Të dhënat nuk janë të plota.');
            }

            if (!validateEmail($email)) {
                throw new InvalidArgumentException('Email-i nuk është valid.');
            }

            if ($userRepo->emailExists($email, $id)) {
                throw new InvalidArgumentException('Ky email përdoret nga përdorues tjetër.');
            }

            if ($id === (int) $_SESSION['user_id'] && $role !== 'admin') {
                throw new InvalidArgumentException('Nuk mund ta ndryshoni rolin tuaj në customer.');
            }

            $userRepo->update($id, $name, $email, $role);

            if ($newPassword !== '') {
                $userRepo->updatePassword($id, $newPassword);
            }

            $message = 'Përdoruesi u përditësua.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id === (int) $_SESSION['user_id']) {
                throw new InvalidArgumentException('Nuk mund të fshini llogarinë tuaj.');
            }

            $target = $userRepo->findById($id);

            if ($target && $target->getRole() === 'admin' && $userRepo->countAdmins() <= 1) {
                throw new InvalidArgumentException('Nuk mund të fshihet admini i fundit.');
            }

            $userRepo->delete($id);
            $message = 'Përdoruesi u fshi.';
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = 'Ndodhi një gabim.';
    }
}

$users = $userRepo->findAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editUser = $editId > 0 ? $userRepo->findById($editId) : null;

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Menaxhimi i Përdoruesve</h1>
    <a href="<?php echo e(base_url('pages/dashboard.php')); ?>" class="back-link">&larr; Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="admin-grid">
    <div class="card">
        <h3><?php echo $editUser ? 'Ndrysho përdoruesin' : 'Shto përdorues'; ?></h3>
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $editUser ? 'update' : 'create'; ?>">
            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?php echo $editUser->getId(); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Emri</label>
                <input type="text" name="name" required maxlength="100"
                    value="<?php echo e($editUser ? $editUser->getName() : ''); ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required
                    value="<?php echo e($editUser ? $editUser->getEmail() : ''); ?>">
            </div>

            <div class="form-group">
                <label>Roli</label>
                <select name="role" required>
                    <option value="customer" <?php echo ($editUser && $editUser->getRole() === 'customer') ? 'selected' : ''; ?>>customer</option>
                    <option value="admin" <?php echo ($editUser && $editUser->getRole() === 'admin') ? 'selected' : ''; ?>>admin</option>
                </select>
            </div>

            <div class="form-group">
                <label>Fjalëkalimi <?php echo $editUser ? '(bosh = mos ndrysho)' : ''; ?></label>
                <input type="password" name="password" <?php echo $editUser ? '' : 'required'; ?> minlength="6">
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $editUser ? 'Ruaj' : 'Shto'; ?></button>
            <?php if ($editUser): ?>
                <a href="<?php echo e(base_url('pages/admin/users.php')); ?>" class="btn btn-outline">Anulo</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Lista e përdoruesve</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Roli</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u->getId(); ?></td>
                        <td><?php echo e($u->getName()); ?></td>
                        <td><?php echo e($u->getEmail()); ?></td>
                        <td>
                            <span class="badge <?php echo $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                <?php echo e($u->getRole()); ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="?edit=<?php echo $u->getId(); ?>" class="btn btn-outline btn-sm">Edit</a>
                            <?php if ($u->getRole() === 'customer'): ?>
                                <a href="<?php echo e(base_url('pages/admin/email-user.php?user_id=' . $u->getId())); ?>"
                                    class="btn btn-outline btn-sm">Email</a>
                            <?php endif; ?>
                            <?php if ($u->getId() !== (int) $_SESSION['user_id']): ?>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Fshi përdoruesin?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $u->getId(); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Fshi</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
