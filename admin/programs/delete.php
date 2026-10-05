<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('admin/programs/index.php'));
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($token)) {
    header('Location: ' . base_url('admin/programs/index.php'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . base_url('admin/programs/index.php'));
    exit;
}

// Fetch program to check image
$stmt = $pdo->prepare('SELECT featured_image FROM programs WHERE id = ?');
$stmt->execute([$id]);
$program = $stmt->fetch();

if ($program) {
    if (!empty($program['featured_image'])) {
        $imagePath = __DIR__ . '/../../assets/images/uploads/' . basename($program['featured_image']);
        if (is_file($imagePath)) {
            @unlink($imagePath);
        }
    }

    $del = $pdo->prepare('DELETE FROM programs WHERE id = ?');
    $del->execute([$id]);
}

header('Location: ' . base_url('admin/programs/index.php'));
exit;
