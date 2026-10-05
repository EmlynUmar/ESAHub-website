<?php
require_once __DIR__ . '/../includes/functions.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: ' . url('programs.php'));
    exit;
}

$program = db()->prepare('SELECT p.*, c.name AS category_name FROM programs p JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.status = ? AND p.is_active = 1 LIMIT 1');
$program->execute([$slug, 'published']);
$program = $program->fetch(PDO::FETCH_ASSOC);

if (!$program) {
    header('Location: ' . url('programs.php'));
    exit;
}

$page_title = e($program['title']) . ' | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="program-detail card">
            <?php if (!empty($program['featured_image'])): ?>
                <img src="<?= e(url('assets/images/uploads/' . $program['featured_image'])) ?>" alt="<?= e($program['title']) ?>" class="program-detail-image">
            <?php endif; ?>

            <div class="program-detail-content">
                <span class="badge"><?= e($program['category_name']) ?></span>
                <h1><?= e($program['title']) ?></h1>
                <?php if (!empty($program['subtitle'])): ?><h3><?= e($program['subtitle']) ?></h3><?php endif; ?>
                <p><strong>Duration:</strong> <?= e($program['duration'] ?: 'Flexible') ?></p>
                <p><strong>Delivery:</strong> <?= e(ucfirst($program['delivery_mode'])) ?></p>
                <p><strong>Price:</strong> <?= e($program['price_label'] ?: 'Contact for fee') ?></p>
                <div class="program-detail-body">
                    <p><?= nl2br(e($program['description'])) ?></p>
                </div>

                <div class="cta-group" style="margin-top:1.5rem;">
                    <?php if (!empty($program['registration_link'])): ?>
                        <a class="btn btn-primary" href="<?= e($program['registration_link']) ?>" target="_blank" rel="noopener">Register Now</a>
                    <?php endif; ?>
                    <a class="btn btn-outline" href="<?= e(url('contact.php')) ?>">Book a Service</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
