<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . url('admin/blog/index.php'));
    exit;
}

// Determine table
$table = 'posts';
try {
    $check = $pdo->query("SHOW TABLES LIKE 'posts'");
    if (!$check || !$check->fetch()) {
        $table = 'blog_posts';
    }
} catch (Throwable $e) {
    $table = 'blog_posts';
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `" . str_replace('`', '', $table) . "` WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $post = null;
}

if (!$post) {
    header('Location: ' . url('admin/blog/index.php'));
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM `" . str_replace('`', '', $table) . "` WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . url('admin/blog/index.php?deleted=1'));
    exit;
} catch (Throwable $e) {
    header('Location: ' . url('admin/blog/index.php?error=1'));
    exit;
}
