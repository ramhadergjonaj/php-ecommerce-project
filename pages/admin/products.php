<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../functions/helpers.php';

requireAdmin();

$repos = app_repositories();
$productRepo = $repos['products'];
$categoryRepo = $repos['categories'];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $name = cleanInput($_POST['name'] ?? '');
            $price = cleanInput($_POST['price'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $description = cleanInput($_POST['description'] ?? '');
            $imageUrl = cleanInput($_POST['image_url'] ?? '');

            if (!validateProductName($name)) {
                throw new InvalidArgumentException('Emri i produktit nuk është valid.');
            }

            if (!validatePrice($price)) {
                throw new InvalidArgumentException('Çmimi nuk është valid.');
            }

            if ($categoryId <= 0) {
                throw new InvalidArgumentException('Zgjidhni një kategori.');
            }

            $upload = handleProductImageUpload($_FILES['image_file'] ?? [], $imageUrl ?: null);
            $image = $upload['success'] ? ($upload['path'] ?: $imageUrl) : '';

            if ($image === '') {
                throw new InvalidArgumentException($upload['error'] ?? 'Vendosni URL ose ngarkoni imazh.');
            }

            if ($action === 'create') {
                $productRepo->create($name, (float) $price, $categoryId, $image, $description);
                $message = 'Produkti u shtua me sukses.';
            } else {
                $id = (int) ($_POST['id'] ?? 0);
                $productRepo->update($id, $name, (float) $price, $categoryId, $image, $description);
                $message = 'Produkti u përditësua.';
            }
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = 'Ndodhi një gabim gjatë ruajtjes.';
    }
}

$products = $productRepo->findAll();
$categories = $categoryRepo->findAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editProduct = $editId > 0 ? $productRepo->findById($editId) : null;

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>Menaxhimi i Produkteve (CRUD)</h1>
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
        <h3><?php echo $editProduct ? 'Ndrysho Produktin' : 'Shto Produkt të Ri'; ?></h3>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $editProduct ? 'update' : 'create'; ?>">
            <?php if ($editProduct): ?>
                <input type="hidden" name="id" value="<?php echo $editProduct->getId(); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Emri</label>
                <input type="text" name="name" required
                    value="<?php echo e($editProduct ? $editProduct->getName() : ''); ?>">
            </div>

            <div class="form-group">
                <label>Çmimi (USD)</label>
                <input type="text" name="price" required pattern="^\d+(\.\d{1,2})?$"
                    value="<?php echo $editProduct ? e((string) $editProduct->getPrice()) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Kategoria</label>
                <select name="category_id" required>
                    <option value="">-- Zgjidh --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat->getId(); ?>"
                            <?php echo ($editProduct && $editProduct->getCategoryId() == $cat->getId()) ? 'selected' : ''; ?>>
                            <?php echo e($cat->getName()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>URL Imazhi</label>
                <input type="url" name="image_url"
                    value="<?php echo e($editProduct ? $editProduct->getImage() : ''); ?>"
                    placeholder="https://...">
            </div>

            <div class="form-group">
                <label>Ngarko Imazh (opsional)</label>
                <input type="file" name="image_file" accept="image/*">
            </div>

            <div class="form-group">
                <label>Përshkrimi</label>
                <textarea name="description" rows="3"><?php echo e($editProduct ? $editProduct->getDescription() : ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $editProduct ? 'Ruaj Ndryshimet' : 'Shto Produktin'; ?>
            </button>

            <?php if ($editProduct): ?>
                <a href="<?php echo e(base_url('pages/admin/products.php')); ?>" class="btn btn-outline">Anulo</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Lista e Produkteve <span class="badge">AJAX Delete</span></h3>

        <table class="data-table" id="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Emri</th>
                    <th>Çmimi</th>
                    <th>Kategoria</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr id="product-row-<?php echo $p->getId(); ?>">
                        <td><?php echo $p->getId(); ?></td>
                        <td><?php echo e($p->getName()); ?></td>
                        <td><?php echo e($p->getFormattedPrice()); ?></td>
                        <td><?php echo e($p->getCategory()); ?></td>
                        <td class="actions-cell">
                            <a href="?edit=<?php echo $p->getId(); ?>" class="btn btn-outline btn-sm">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-product"
                                data-id="<?php echo $p->getId(); ?>">
                                Fshi (AJAX)
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p id="ajax-status" class="ajax-status" aria-live="polite"></p>
    </div>
</div>

<script>
    window.APP_BASE = <?php echo json_encode(base_url()); ?>;
</script>
<script src="<?php echo e(base_url('assets/js/admin-products.js')); ?>"></script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
//products