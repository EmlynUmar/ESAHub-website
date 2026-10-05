<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$errors = [];
$values = ['category_id'=>'','title'=>'','slug'=>'','subtitle'=>'','summary'=>'','description'=>'','delivery_mode'=>'online','duration'=>'','price_label'=>'','whatsapp_prefill'=>'','registration_link'=>'','status'=>'draft','is_active'=>1];

$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$canUseSubtitle = column_exists($pdo, 'programs', 'subtitle');
$canUseDescription = column_exists($pdo, 'programs', 'description');
$canUseDuration = column_exists($pdo, 'programs', 'duration');
$canUsePrice = column_exists($pdo, 'programs', 'price_label');
$canUseFeaturedImage = column_exists($pdo, 'programs', 'featured_image');
$canUseRegistrationLink = column_exists($pdo, 'programs', 'registration_link');
$canUseWhatsappPrefill = column_exists($pdo, 'programs', 'whatsapp_prefill');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Invalid token.';
    } else {
        foreach ($values as $k => $_) { $values[$k] = trim((string)($_POST[$k] ?? '')); }
        $values['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        if ($values['title'] === '' || $values['slug'] === '') { $errors[] = 'Title and slug required.'; }
        if (!in_array($values['delivery_mode'], ['online','physical','both'], true)) { $errors[] = 'Invalid delivery mode.'; }

        if (empty($errors)) {
            $image_name = null;
            if (!empty($_FILES['featured_image']['name'])) {
                $allowed = ['jpg','jpeg','png','webp'];
                $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/../../assets/images/uploads';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

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
                $whatsappPrefill = $values['whatsapp_prefill'] !== '' ? $values['whatsapp_prefill'] : null;
                $registrationLink = $values['registration_link'] !== '' ? $values['registration_link'] : null;

                $insertCols = ['category_id','title','slug','summary','delivery_mode','status','is_active'];
                $insertVals = [
                    $values['category_id'] ?: null,
                    $values['title'],
                    $values['slug'],
                    $values['summary'],
                    $values['delivery_mode'],
                    $values['status'],
                    $values['is_active']
                ];

                if ($canUseSubtitle) { $insertCols[] = 'subtitle'; $insertVals[] = $values['subtitle'] ?: null; }
                if ($canUseDescription) { $insertCols[] = 'description'; $insertVals[] = $values['description']; }
                if ($canUseDuration) { $insertCols[] = 'duration'; $insertVals[] = $values['duration'] ?: null; }
                if ($canUsePrice) { $insertCols[] = 'price_label'; $insertVals[] = $values['price_label'] ?: null; }
                if ($canUseFeaturedImage) { $insertCols[] = 'featured_image'; $insertVals[] = $image_name; }
                if ($canUseWhatsappPrefill) { $insertCols[] = 'whatsapp_prefill'; $insertVals[] = $whatsappPrefill; }
                if ($canUseRegistrationLink) { $insertCols[] = 'registration_link'; $insertVals[] = $registrationLink; }

                $insertCols[] = 'created_at';
                $insertVals[] = 'NOW()';
                $insertCols[] = 'updated_at';
                $insertVals[] = 'NOW()';

                $placeholders = implode(', ', array_fill(0, count($insertVals), '?'));
                $stmt = $pdo->prepare('INSERT INTO programs (' . implode(', ', $insertCols) . ') VALUES (' . $placeholders . ')');
                $stmt->execute($insertVals);
                header('Location: ' . url('admin/programs/index.php')); exit;
            }
        }
    }
}

$page_title = 'Create Program | Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section"><div class="container">
    <h2>Create Program</h2>
    <?php if ($errors): ?><div class="form-message error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="card">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-group"><label>Category</label><select name="category_id"><?php foreach($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$values['category_id']===(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Title</label><input name="title" value="<?= e($values['title']) ?>"></div>
        <div class="form-group"><label>Slug</label><input name="slug" value="<?= e($values['slug']) ?>"></div>
        <?php if ($canUseSubtitle): ?><div class="form-group"><label>Subtitle</label><input name="subtitle" value="<?= e($values['subtitle']) ?>"></div><?php endif; ?>
        <div class="form-group"><label>Summary</label><textarea name="summary"><?= e($values['summary']) ?></textarea></div>
        <?php if ($canUseDescription): ?><div class="form-group"><label>Description</label><textarea name="description" rows="6"><?= e($values['description']) ?></textarea></div><?php endif; ?>
        <div class="form-group"><label>Delivery Mode</label><select name="delivery_mode"><option value="online">Online</option><option value="physical">Physical</option><option value="both">Both</option></select></div>
        <?php if ($canUseDuration): ?><div class="form-group"><label>Duration</label><input name="duration" value="<?= e($values['duration']) ?>"></div><?php endif; ?>
        <?php if ($canUsePrice): ?><div class="form-group"><label>Price Label</label><input name="price_label" value="<?= e($values['price_label']) ?>"></div><?php endif; ?>
        <?php if ($canUseFeaturedImage): ?><div class="form-group"><label>Featured Image</label><input type="file" name="featured_image" accept="image/*"></div><?php endif; ?>
        <?php if ($canUseWhatsappPrefill): ?><div class="form-group"><label>WhatsApp Prefill</label><textarea name="whatsapp_prefill"><?= e($values['whatsapp_prefill']) ?></textarea></div><?php endif; ?>
        <?php if ($canUseRegistrationLink): ?><div class="form-group"><label>Registration Link</label><input name="registration_link" type="url" value="<?= e($values['registration_link']) ?>" placeholder="https://... or https://wa.me/..."></div><?php endif; ?>
        <div class="form-group"><label>Status</label><select name="status"><option value="draft">Draft</option><option value="published">Published</option></select></div>
        <div class="form-group switch-row"><label>Active</label><label class="switch"><input type="checkbox" name="is_active" checked><span class="slider"></span></label></div>
        <button class="btn btn-primary" type="submit">Create</button>
    </form>
</div></section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
