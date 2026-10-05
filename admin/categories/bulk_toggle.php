<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . url('admin/categories/index.php')); exit; }

$id = (int)($_POST['id'] ?? 0);
$active = (int)($_POST['active'] ?? 0);
if (empty($id) || !hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) { header('Location: ' . url('admin/categories/index.php')); exit; }

// Update category
$up = $pdo->prepare('UPDATE categories SET is_active = :active WHERE id = :id');
$up->execute([':active'=>$active,':id'=>$id]);

// Cascade to programs in the category: set programs.is_active to the same value
$cascade = $pdo->prepare('UPDATE programs SET is_active = :active WHERE category_id = :id');
$cascade->execute([':active'=>$active,':id'=>$id]);

header('Location: ' . url('admin/categories/index.php')); exit;
