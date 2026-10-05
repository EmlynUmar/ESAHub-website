<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
<<<<<<< HEAD:admin/delete-post.php
    header('Location: ' . url('admin/dashboard.php'));
=======
    header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/delete-post.php
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($token)) {
<<<<<<< HEAD:admin/delete-post.php
    header('Location: ' . url('admin/dashboard.php'));
=======
    header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/delete-post.php
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
<<<<<<< HEAD:admin/delete-post.php
    header('Location: ' . url('admin/dashboard.php'));
=======
    header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/delete-post.php
    exit;
}

$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
$stmt->execute([$id]);

<<<<<<< HEAD:admin/delete-post.php
header('Location: ' . url('admin/dashboard.php'));
=======
header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/delete-post.php
exit;
