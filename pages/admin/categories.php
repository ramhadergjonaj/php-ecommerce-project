<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';

requireAdmin();

$categoryRepo = app_repositories()['categories'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        $name = cleanInput($_POST['name'] ?? '');
        $description = cleanInput($_POST['description'] ?? '');

        if ($name === '' || strlen($name) < 2) {
            throw new InvalidArgumentException('Emri i kategorisë duhet të ketë të paktën 2 karaktere.');
        }

        if ($action === 'create') {
            $categoryRepo->create($name, $description ?: null);
            $message = 'Kategoria u shtua.';
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $categoryRepo->update($id, $name, $description ?: null);
            $message = 'Kategoria u përditësua.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($categoryRepo->countProducts($id) > 0) {
                throw new InvalidArgumentException('Kategoria ka produkte dhe nuk mund të fshihet.');
            }

            $categoryRepo->delete($id);
            $message = 'Kategoria u fshi.';
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = 'Ndodhi një gabim.';
    }
}

$categories = $categoryRepo->findAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editCategory = $editId > 0 ? $categoryRepo->findById($editId) : null;

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Menaxhimi i Kategorive (CRUD)</h1>
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
        <h3><?php echo $editCategory ? 'Ndrysho Kategorinë' : 'Shto Kategori'; ?></h3>
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $editCategory ? 'update' : 'create'; ?>">
            <?php if ($editCategory): ?>
                <input type="hidden" name="id" value="<?php echo $editCategory->getId(); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Emri</label>
                <input type="text" name="name" required maxlength="100"
                    value="<?php echo e($editCategory ? $editCategory->getName() : ''); ?>">
            </div>

            <div class="form-group">
                <label>Përshkrimi</label>
                <input type="text" name="description" maxlength="255"
                    value="<?php echo e($editCategory ? (string) $editCategory->getDescription() : ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $editCategory ? 'Ruaj' : 'Shto'; ?>
            </button>
            <?php if ($editCategory): ?>
                <a href="<?php echo e(base_url('pages/admin/categories.php')); ?>" class="btn btn-outline">Anulo</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Lista e Kategorive</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Emri</th>
                    <th>Produkte</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat->getId(); ?></td>
                        <td><?php echo e($cat->getName()); ?></td>
                        <td><?php echo $categoryRepo->countProducts($cat->getId()); ?></td>
                        <td class="actions-cell">
                            <a href="?edit=<?php echo $cat->getId(); ?>" class="btn btn-outline btn-sm">Edit</a>
                            <form method="POST" class="inline-form"
                                onsubmit="return confirm('Fshi kategorinë?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat->getId(); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Fshi</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 


//categories