<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (empty($id)) { header('Location: ' . url('admin/categories/index.php')); exit; }

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$cat = $stmt->fetch();
if (!$cat) { header('Location: ' . url('admin/categories/index.php')); exit; }

$canUseDescription = column_exists($pdo, 'categories', 'description');
$canUseFeaturedImage = column_exists($pdo, 'categories', 'featured_image');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Invalid token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($name === '' || $slug === '') { $errors[] = 'Name and slug required.'; }

        $image_name = $cat['featured_image'] ?? null;
        if ($canUseFeaturedImage && !empty($_FILES['featured_image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp','svg'];
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true)) {
                $uploadDir = __DIR__ . '/../../assets/images/uploads';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $image_name = uniqid('cat_', true) . '.' . $ext;
                $destination = $uploadDir . '/' . $image_name;
                if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
                    $errors[] = 'Failed to upload category image.';
                }
            } else {
                $errors[] = 'Invalid image format.';
            }
        }

        if (empty($errors)) {
            $setParts = ['name = ?', 'slug = ?', 'is_active = ?'];
            $values = [$name, $slug, $is_active];
            if ($canUseDescription) { $setParts[] = 'description = ?'; $values[] = $description ?: null; }
            if ($canUseFeaturedImage) { $setParts[] = 'featured_image = ?'; $values[] = $image_name; }
            $values[] = $id;

            $up = $pdo->prepare('UPDATE categories SET ' . implode(', ', $setParts) . ' WHERE id = ?');
            $up->execute($values);
            header('Location: ' . url('admin/categories/index.php')); exit;
        }
    }
}

$page_title = 'Edit Category | Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section"><div class="container">
    <h2>Edit Category</h2>
    <?php if ($errors): ?><div class="form-message error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="card">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <div class="form-group"><label>Name</label><input name="name" value="<?= e($cat['name']) ?>"></div>
        <div class="form-group"><label>Slug</label><input name="slug" value="<?= e($cat['slug']) ?>"></div>
        <?php if ($canUseDescription): ?>
            <div class="form-group"><label>Description</label><textarea name="description"><?= e($cat['description'] ?? '') ?></textarea></div>
        <?php endif; ?>
        <?php if ($canUseFeaturedImage): ?>
            <div class="form-group"><label>Current Image</label>
                <?php if (!empty($cat['featured_image'])): ?>
                    <div><img src="<?= e(url('assets/images/uploads/' . $cat['featured_image'])) ?>" alt="Category image" style="max-width:200px;display:block"></div>
                <?php else: ?>
                    <div>No image uploaded</div>
                <?php endif; ?>
            </div>
            <div class="form-group"><label>Replace Image</label><input type="file" name="featured_image" accept="image/*"></div>
        <?php endif; ?>
        <div class="form-group switch-row"><label>Active</label><label class="switch"><input type="checkbox" name="is_active" <?= !empty($cat['is_active']) ? 'checked' : '' ?>><span class="slider"></span></label></div>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</div></section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
