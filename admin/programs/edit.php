<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (empty($id)) {
    header('Location: ' . base_url('admin/programs/index.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM programs WHERE id = ?');
$stmt->execute([$id]);
$program = $stmt->fetch();
if (!$program) {
    header('Location: ' . base_url('admin/programs/index.php'));
    exit;
}

$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();

$canUseSubtitle = column_exists($pdo, 'programs', 'subtitle');
$canUseDescription = column_exists($pdo, 'programs', 'description');
$canUseRegistrationLink = column_exists($pdo, 'programs', 'registration_link');
$canUsePrice = column_exists($pdo, 'programs', 'price_label');
$canUseDuration = column_exists($pdo, 'programs', 'duration');
$canUseWhatsappPrefill = column_exists($pdo, 'programs', 'whatsapp_prefill');
$canUseFeaturedImage = column_exists($pdo, 'programs', 'featured_image');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $title = trim((string)($_POST['title'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));
        $subtitle = trim((string)($_POST['subtitle'] ?? ''));
        $summary = trim((string)($_POST['summary'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $deliveryMode = in_array($_POST['delivery_mode'] ?? '', ['online','physical','both'], true) ? $_POST['delivery_mode'] : 'online';
        $duration = trim((string)($_POST['duration'] ?? ''));
        $priceLabel = trim((string)($_POST['price_label'] ?? ''));
        $whatsappPrefill = trim((string)($_POST['whatsapp_prefill'] ?? ''));
        $registrationLink = trim((string)($_POST['registration_link'] ?? ''));
        $status = in_array($_POST['status'] ?? 'draft', ['draft','published'], true) ? $_POST['status'] : 'draft';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '' || $slug === '') {
            $errors[] = 'Title and slug are required.';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Please select a valid category.';
        }

        $image_name = $program['featured_image'];
        if (!empty($_FILES['featured_image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true)) {
                $uploadDir = __DIR__ . '/../../assets/images/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newImage = uniqid('prog_', true) . '.' . $ext;
                $destination = $uploadDir . '/' . $newImage;
                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
                    // Remove old image if one existed
                    if (!empty($program['featured_image'])) {
                        $oldPath = $uploadDir . '/' . basename($program['featured_image']);
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $image_name = $newImage;
                } else {
                    $errors[] = 'Failed to upload image.';
                }
            } else {
                $errors[] = 'Invalid image format. Use JPG, PNG, or WebP.';
            }
        }

        if (empty($errors)) {
            $setParts = [
                'category_id = ?',
                'title = ?',
                'slug = ?',
                'summary = ?',
                'delivery_mode = ?',
                'status = ?',
                'is_active = ?',
                'updated_at = NOW()'
            ];
            $values = [$categoryId, $title, $slug, $summary, $deliveryMode, $status, $isActive];

            if ($canUseSubtitle) {
                $setParts[] = 'subtitle = ?';
                $values[] = $subtitle !== '' ? $subtitle : null;
            }
            if ($canUseDescription) {
                $setParts[] = 'description = ?';
                $values[] = $description;
            }
            if ($canUseDuration) {
                $setParts[] = 'duration = ?';
                $values[] = $duration !== '' ? $duration : null;
            }
            if ($canUsePrice) {
                $setParts[] = 'price_label = ?';
                $values[] = $priceLabel !== '' ? $priceLabel : null;
            }
            if ($canUseWhatsappPrefill) {
                $setParts[] = 'whatsapp_prefill = ?';
                $values[] = $whatsappPrefill !== '' ? $whatsappPrefill : null;
            }
            if ($canUseRegistrationLink) {
                $setParts[] = 'registration_link = ?';
                $values[] = $registrationLink !== '' ? $registrationLink : null;
            }
            if ($canUseFeaturedImage) {
                $setParts[] = 'featured_image = ?';
                $values[] = $image_name;
            }

            $values[] = $id;

            $up = $pdo->prepare('UPDATE programs SET ' . implode(', ', $setParts) . ' WHERE id = ?');
            $up->execute($values);
            header('Location: ' . base_url('admin/programs/index.php'));
            exit;
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
        <?php if ($errors): ?>
            <div class="form-message error"><?= e(implode(' ', $errors)) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($cats as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= ((int)$program['category_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title *</label>
                <input id="title" name="title" value="<?= e($program['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug *</label>
                <input id="slug" name="slug" value="<?= e($program['slug']) ?>" required>
            </div>

            <?php if ($canUseSubtitle): ?>
                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input id="subtitle" name="subtitle" value="<?= e($program['subtitle'] ?? '') ?>">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="summary">Summary *</label>
                <textarea id="summary" name="summary" required><?= e($program['summary'] ?? '') ?></textarea>
            </div>

            <?php if ($canUseDescription): ?>
                <div class="form-group">
                    <label for="description">Detailed Description *</label>
                    <textarea id="description" name="description" rows="6" required><?= e($program['description'] ?? '') ?></textarea>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="delivery_mode">Delivery Mode</label>
                <select id="delivery_mode" name="delivery_mode">
                    <option value="online" <?= ($program['delivery_mode'] === 'online') ? 'selected' : '' ?>>Online</option>
                    <option value="physical" <?= ($program['delivery_mode'] === 'physical') ? 'selected' : '' ?>>Physical</option>
                    <option value="both" <?= ($program['delivery_mode'] === 'both') ? 'selected' : '' ?>>Both</option>
                </select>
            </div>

            <?php if ($canUseDuration): ?>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input id="duration" name="duration" value="<?= e($program['duration'] ?? '') ?>" placeholder="e.g. 6 Weeks">
                </div>
            <?php endif; ?>

            <?php if ($canUsePrice): ?>
                <div class="form-group">
                    <label for="price_label">Price Label</label>
                    <input id="price_label" name="price_label" value="<?= e($program['price_label'] ?? '') ?>" placeholder="e.g. Free or ₦25,000">
                </div>
            <?php endif; ?>

            <?php if ($canUseWhatsappPrefill): ?>
                <div class="form-group">
                    <label for="whatsapp_prefill">WhatsApp Inquiry Template</label>
                    <textarea id="whatsapp_prefill" name="whatsapp_prefill" rows="2" placeholder="Hi ESAHub, I am interested in registering for this program..."><?= e($program['whatsapp_prefill'] ?? '') ?></textarea>
                </div>
            <?php endif; ?>

            <?php if ($canUseRegistrationLink): ?>
                <div class="form-group">
                    <label for="registration_link">Registration Link</label>
                    <input id="registration_link" name="registration_link" type="url" value="<?= e($program['registration_link'] ?? '') ?>" placeholder="https://... or https://wa.me/...">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?= ($program['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($program['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                </select>
            </div>

            <div class="form-group">
                <label for="featured_image">Current Featured Image</label>
                <?php if (!empty($program['featured_image'])): ?>
                    <div style="margin-bottom:0.5rem;">
                        <img src="<?= e(base_url('assets/images/uploads/' . $program['featured_image'])) ?>" alt="Featured" style="max-width:200px;border-radius:8px;display:block;">
                    </div>
                <?php else: ?>
                    <p style="color:#777;font-size:0.9rem;">No image uploaded</p>
                <?php endif; ?>
                <label for="featured_image" style="margin-top:0.5rem;font-weight:normal;">Replace Image</label>
                <input id="featured_image" type="file" name="featured_image" accept="image/*">
            </div>

            <div class="form-group switch-row">
                <label for="is_active">Active (Visible)</label>
                <label class="switch">
                    <input id="is_active" type="checkbox" name="is_active" <?= $program['is_active'] ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <button class="btn btn-primary" type="submit">Save Changes</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
