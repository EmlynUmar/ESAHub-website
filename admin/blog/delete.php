<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('admin/blog/index.php'));
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($token)) {
    header('Location: ' . base_url('admin/blog/index.php'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . base_url('admin/blog/index.php'));
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT featured_image FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        if (!empty($post['featured_image'])) {
            $imagePath = __DIR__ . '/../../assets/images/uploads/' . basename($post['featured_image']);
            if (is_file($imagePath)) {
                @unlink($imagePath);
            }
        }

        $del = $pdo->prepare('DELETE FROM blog_posts WHERE id = ?');
        $del->execute([$id]);
    }

    header('Location: ' . base_url('admin/blog/index.php?deleted=1'));
    exit;
} catch (Throwable $e) {
    error_log('Delete post error: ' . $e->getMessage());
    header('Location: ' . base_url('admin/blog/index.php?error=1'));
    exit;
}
