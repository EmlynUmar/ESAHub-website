<?php
$page_title = 'Create Post | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error_message = 'Invalid request.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $slug = generate_slug($title);

        if ($title === '' || $content === '') {
            $error_message = 'Title and content are required.';
        } else {
            $image_name = '';
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
            $slug_check = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = ?');
            $slug_check->execute([$slug]);
            $count = (int) $slug_check->fetchColumn();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $stmt = $pdo->prepare('INSERT INTO posts (title, slug, content, featured_image, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$title, $slug, $content, $image_name, $status]);
<<<<<<< HEAD:admin/create-post.php
            header('Location: ' . url('admin/dashboard.php'));
=======
            header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/create-post.php
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
            <h2>Create Blog Post</h2>
        </div>

        <?php if ($error_message): ?>
            <div class="form-message error"><?php echo e($error_message); ?></div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input id="title" name="title" type="text" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input id="featured_image" name="featured_image" type="file" accept="image/*">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Save Post</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
