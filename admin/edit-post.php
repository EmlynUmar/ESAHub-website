<?php
$page_title = 'Edit Post | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . base_url('admin/blog/index.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: ' . base_url('admin/blog/index.php'));
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error_message = 'Invalid request.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $slug = generate_slug($title);

        if ($title === '' || $content === '') {
            $error_message = 'Title and content are required.';
        } else {
            if ($excerpt === '') {
                $excerpt = mb_strimwidth(strip_tags($content), 0, 160, '...');
            }

            $image_name = $post['featured_image'] ?? '';
            if (!empty($_FILES['featured_image']['name'])) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $extension = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed, true)) {
                    $error_message = 'Invalid image format. Use JPG, PNG, or WebP.';
                } else {
                    $image_name = uniqid('post_', true) . '.' . $extension;
                    $destination = __DIR__ . '/../assets/images/uploads/' . $image_name;
                    if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
                        $error_message = 'Failed to upload image.';
                    }
                }
            }
        }

        if ($error_message === '') {
            $slug_check = $pdo->prepare('SELECT COUNT(*) FROM blog_posts WHERE slug = ? AND id != ?');
            $slug_check->execute([$slug, $id]);
            $count = (int) $slug_check->fetchColumn();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $stmt = $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$title, $slug, $excerpt, $content, $image_name, $status, $id]);
            header('Location: ' . base_url('admin/blog/index.php'));
            exit;
        }
    }
}
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Edit Blog Post</h2>
        </div>

        <?php if ($error_message): ?>
            <div class="form-message error"><?php echo e($error_message); ?></div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="<?php echo e($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" required><?php echo e($post['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="excerpt">Excerpt <small style="color:#666;">(optional summary for cards)</small></label>
                <textarea id="excerpt" name="excerpt" rows="2"><?php echo e($post['excerpt'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input id="featured_image" name="featured_image" type="file" accept="image/*">
                <?php if (!empty($post['featured_image'])): ?>
                    <p style="margin-top:0.5rem;font-size:0.9rem;">Current image: <?php echo e($post['featured_image']); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Update Post</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
