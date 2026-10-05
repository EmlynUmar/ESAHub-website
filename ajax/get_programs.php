<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = $pdo ?? null;
if (!$pdo) { echo json_encode(['error' => 'db']); exit; }

$category = trim((string)($_GET['category'] ?? ''));
$sort = trim((string)($_GET['sort'] ?? 'latest'));

$order = 'p.created_at DESC';
if ($sort === 'alpha') { $order = 'p.title ASC'; }
elseif ($sort === 'popular') { $order = 'p.updated_at DESC'; }

try {
    if ($category !== '') {
        $stmt = $pdo->prepare('SELECT p.id,p.title,p.slug,p.summary,p.subtitle,p.delivery_mode,p.duration,p.price_label,p.featured_image,p.registration_link,c.name AS category_name FROM programs p JOIN categories c ON c.id = p.category_id WHERE p.status = ? AND p.is_active = 1 AND c.is_active = 1 AND c.slug = ? ORDER BY ' . $order);
        $stmt->execute(['published', $category]);
    } else {
        $stmt = $pdo->prepare('SELECT p.id,p.title,p.slug,p.summary,p.subtitle,p.delivery_mode,p.duration,p.price_label,p.featured_image,p.registration_link,c.name AS category_name FROM programs p JOIN categories c ON c.id = p.category_id WHERE p.status = ? AND p.is_active = 1 AND c.is_active = 1 ORDER BY ' . $order . ' LIMIT 100');
        $stmt->execute(['published']);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['programs' => $rows]);
} catch (Throwable $e) {
    error_log('ajax get_programs error: ' . $e->getMessage());
    echo json_encode(['error' => 'query', 'programs' => []]);
}

?>
