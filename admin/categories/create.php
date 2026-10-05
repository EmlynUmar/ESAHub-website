<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$errors = [];
$values = ['name'=>'','slug'=>'','description'=>'','is_active'=>1];
$canUseDescription = column_exists($pdo, 'categories', 'description');
$canUseFeaturedImage = column_exists($pdo, 'categories', 'featured_image');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Invalid token.';
    } else {
        $values['name'] = trim((string)($_POST['name'] ?? ''));
        $values['slug'] = trim((string)($_POST['slug'] ?? ''));
        $values['description'] = trim((string)($_POST['description'] ?? ''));
        $values['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        if ($values['name'] === '' || $values['slug'] === '') { $errors[] = 'Name and slug required.'; }

        $image_name = null;
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
            $insertCols = ['name', 'slug', 'is_active'];
            $insertVals = [$values['name'], $values['slug'], $values['is_active']];
            if ($canUseDescription) { $insertCols[] = 'description'; $insertVals[] = $values['description'] ?: null; }
            if ($canUseFeaturedImage) { $insertCols[] = 'featured_image'; $insertVals[] = $image_name; }

            $st = $pdo->prepare('INSERT INTO categories (' . implode(', ', $insertCols) . ') VALUES (' . implode(', ', array_fill(0, count($insertVals), '?')) . ')');
            $st->execute($insertVals);
            header('Location: ' . url('admin/categories/index.php')); exit;
        }
    }
}

$page_title = 'Create Category | Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section"><div class="container">
    <h2>Create Category</h2>
    <?php if ($errors): ?><div class="form-message error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="card">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-group"><label>Name</label><input name="name" value="<?= e($values['name']) ?>"></div>
        <div class="form-group"><label>Slug</label><input name="slug" value="<?= e($values['slug']) ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description"><?= e($values['description']) ?></textarea></div>
        <div class="form-group"><label>Category Image</label><input type="file" name="featured_image" accept="image/*"></div>
        <div class="form-group switch-row"><label>Active</label><label class="switch"><input type="checkbox" name="is_active" checked><span class="slider"></span></label></div>
        <button class="btn btn-primary" type="submit">Create</button>
    </form>
</div></section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
