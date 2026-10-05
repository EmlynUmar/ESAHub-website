<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (empty($id)) { header('Location: ' . url('admin/programs/index.php')); exit; }

$stmt = $pdo->prepare('SELECT * FROM programs WHERE id = ?');
$stmt->execute([$id]);
$program = $stmt->fetch();
if (!$program) { header('Location: ' . url('admin/programs/index.php')); exit; }

$canUseSubtitle = column_exists($pdo, 'programs', 'subtitle');
$canUseDescription = column_exists($pdo, 'programs', 'description');
$canUseRegistrationLink = column_exists($pdo, 'programs', 'registration_link');
$canUsePrice = column_exists($pdo, 'programs', 'price_label');
$canUseDuration = column_exists($pdo, 'programs', 'duration');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Invalid token.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $price_label = trim($_POST['price_label'] ?? '');
        $registration_link = trim($_POST['registration_link'] ?? '');
        $status = in_array($_POST['status'] ?? 'draft', ['draft','published']) ? $_POST['status'] : 'draft';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '' || $slug === '') {
            $errors[] = 'Title and slug required.';
        }

        $image_name = $program['featured_image'];
        if (!empty($_FILES['featured_image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true)) {
                $uploadDir = __DIR__ . '/../../assets/images/uploads';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $image_name = uniqid('prog_', true) . '.' . $ext;
                $destination = $uploadDir . '/' . $image_name;
                if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
                    $errors[] = 'Failed to upload image.';
                }
            } else {
                $errors[] = 'Invalid image format. Use JPG, PNG, or WebP.';
            }
        }

        if (empty($errors)) {
            $setParts = ['title = ?', 'slug = ?', 'summary = ?', 'status = ?', 'is_active = ?', 'updated_at = NOW()'];
            $values = [$title, $slug, $summary, $status, $is_active];

            if ($canUseSubtitle) { $setParts[] = 'subtitle = ?'; $values[] = $subtitle ?: null; }
            if ($canUseDescription) { $setParts[] = 'description = ?'; $values[] = $description; }
            if ($canUseDuration) { $setParts[] = 'duration = ?'; $values[] = $duration ?: null; }
            if ($canUsePrice) { $setParts[] = 'price_label = ?'; $values[] = $price_label ?: null; }
            if (column_exists($pdo, 'programs', 'featured_image')) { $setParts[] = 'featured_image = ?'; $values[] = $image_name; }
            if ($canUseRegistrationLink) { $setParts[] = 'registration_link = ?'; $values[] = $registration_link ?: null; }
            $values[] = $id;

            $up = $pdo->prepare('UPDATE programs SET ' . implode(', ', $setParts) . ' WHERE id = ?');
            $up->execute($values);
            header('Location: ' . url('admin/programs/index.php')); exit;
        }
    }
}

$page_title = 'Edit Program | Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section">
    <div class="container">
        <h2>Edit Program</h2>
        <?php if ($errors): ?><div class="form-message error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <div class="form-group"><label>Title</label><input name="title" value="<?= e($program['title']) ?>"></div>
            <div class="form-group"><label>Slug</label><input name="slug" value="<?= e($program['slug']) ?>"></div>
            <?php if ($canUseSubtitle): ?>
                <div class="form-group"><label>Subtitle</label><input name="subtitle" value="<?= e($program['subtitle'] ?? '') ?>"></div>
            <?php endif; ?>
            <div class="form-group"><label>Summary</label><textarea name="summary"><?= e($program['summary'] ?? '') ?></textarea></div>
            <?php if ($canUseDescription): ?>
                <div class="form-group"><label>Description</label><textarea name="description" rows="6"><?= e($program['description'] ?? '') ?></textarea></div>
            <?php endif; ?>
            <?php if ($canUseDuration): ?>
                <div class="form-group"><label>Duration</label><input name="duration" value="<?= e($program['duration'] ?? '') ?>"></div>
            <?php endif; ?>
            <?php if ($canUsePrice): ?>
                <div class="form-group"><label>Price Label</label><input name="price_label" value="<?= e($program['price_label'] ?? '') ?>"></div>
            <?php endif; ?>
            <?php if ($canUseRegistrationLink): ?>
                <div class="form-group"><label>Registration Link</label><input name="registration_link" type="url" value="<?= e($program['registration_link'] ?? '') ?>"></div>
            <?php endif; ?>
            <div class="form-group"><label>Status</label><select name="status"><option value="draft" <?= $program['status']==='draft'?'selected':'' ?>>Draft</option><option value="published" <?= $program['status']==='published'?'selected':'' ?>>Published</option></select></div>
            <div class="form-group"><label>Current Featured Image</label>
                <?php if (!empty($program['featured_image'])): ?>
                    <div><img src="<?= e(url('assets/images/uploads/' . $program['featured_image'])) ?>" alt="Featured" style="max-width:200px;display:block"></div>
                <?php else: ?>
                    <div>No image uploaded</div>
                <?php endif; ?>
            </div>
            <div class="form-group"><label>Replace Featured Image</label><input type="file" name="featured_image" accept="image/*"></div>
            <div class="form-group switch-row"><label>Active</label><label class="switch"><input type="checkbox" name="is_active" <?= $program['is_active'] ? 'checked' : '' ?>><span class="slider"></span></label></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
