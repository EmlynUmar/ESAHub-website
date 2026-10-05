<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'ESAHub Africa | Empowering Growth';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = db();
$categories = $pdo->query("SELECT id, name, slug, description, featured_image FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
$programs = $pdo->query("SELECT p.id, p.title, p.slug, p.summary, p.delivery_mode, p.duration, p.price_label, p.featured_image, c.name category_name FROM programs p JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.is_active = 1 AND c.is_active = 1 ORDER BY p.created_at DESC LIMIT 6")->fetchAll();
$posts = $pdo->query("SELECT title,slug,excerpt,created_at FROM blog_posts WHERE status='published' ORDER BY created_at DESC LIMIT 3")->fetchAll();
$settings = get_settings();

$heroTitle = $settings['hero_title'] ?? 'Empowering communities through practical education and innovation.';
$heroSubtitle = $settings['hero_subtitle'] ?? 'ESAHub Africa supports youth, women, and families with career, business, and digital skills programs.';
$heroImages = [];
if (!empty($settings['hero_images'])) {
    $heroImages = is_array($settings['hero_images']) ? $settings['hero_images'] : json_decode((string) $settings['hero_images'], true);
}
if (empty($heroImages) && !empty($settings['hero_image'])) {
    $heroImages = [$settings['hero_image']];
}
if (empty($heroImages)) {
    $heroImages = ['hero.svg'];
}

$programsTitle = $settings['programs_section_title'] ?? 'Our Programs';
$programsSubtitle = $settings['programs_section_subtitle'] ?? 'Practical training and support designed for people who want to learn, grow, and build better futures.';
$programsImage = $settings['programs_section_image'] ?? '';

$service1Title = $settings['service_1_title'] ?? 'Hall Booking';
$service1Description = $settings['service_1_description'] ?? 'Secure a welcoming, well-equipped venue for trainings, meetings, workshops, and community events.';
$service2Title = $settings['service_2_title'] ?? 'Business Consultation';
$service2Description = $settings['service_2_description'] ?? 'Get practical guidance on business planning, strategy, and growth for entrepreneurs and SMEs.';
?>
<section class="hero">
    <div class="container hero-layout">
        <div class="hero-content">
            <h1><?= e($heroTitle) ?></h1>
            <p><?= e($heroSubtitle) ?></p>
            <div class="cta-group">
                <a class="btn btn-primary" href="<?= e(url('/programs.php')) ?>">Explore Programs</a>
                <a class="btn btn-outline" href="<?= e(url('/contact.php')) ?>">Book a Hall</a>
            </div>
        </div>
        <div class="hero-slide-wrap">
            <?php foreach ($heroImages as $index => $image): ?>
                <img src="<?= e(url('assets/images/' . ltrim($image, '/'))) ?>" alt="ESAHub hero slide" class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Featured Programs</h2>
        </div>
        <div class="card-grid">
            <?php foreach($programs as $program): ?>
            <article class="card program-card">
                <?php if (!empty($program['featured_image'])): ?>
                    <img class="program-card-image" src="<?= e(url('assets/images/uploads/' . $program['featured_image'])) ?>" alt="<?= e($program['title']) ?>">
                <?php endif; ?>
                <span class="badge"><?= e(ucfirst($program['delivery_mode'])) ?></span>
                <h3><a href="<?= e(url('/programs/view.php')) ?>?slug=<?= e($program['slug']) ?>"><?= e($program['title']) ?></a></h3>
                <p><?= e($program['summary']) ?></p>
                <p><strong><?= e($program['duration'] ?: 'Flexible duration') ?></strong> • <?= e($program['price_label'] ?: 'Contact for fee') ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2><?= e($programsTitle) ?></h2>
            <?php if ($programsSubtitle): ?><p><?= e($programsSubtitle) ?></p><?php endif; ?>
        </div>

        <?php if ($programsImage): ?>
            <div class="programs-banner">
                <img src="<?= e(url('assets/images/' . ltrim($programsImage, '/'))) ?>" alt="Programs banner">
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <?php foreach($categories as $category): ?>
            <a class="card program-category-card" href="<?= e(url('/programs.php')) ?>?category=<?= e($category['slug']) ?>">
                <?php if (!empty($category['featured_image'])): ?>
                    <img class="program-card-image" src="<?= e(url('assets/images/uploads/' . $category['featured_image'])) ?>" alt="<?= e($category['name']) ?>">
                <?php endif; ?>
                <h3><?= e($category['name']) ?></h3>
                <p><?= e($category['description'] ?? 'Practical learning experiences designed for growth, innovation, and community transformation.') ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section highlight">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
        </div>
        <div class="service-list">
            <div class="service-item">
                <h3><?= e($service1Title) ?></h3>
                <p><?= e($service1Description) ?></p>
            </div>
            <div class="service-item">
                <h3><?= e($service2Title) ?></h3>
                <p><?= e($service2Description) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Latest Blog</h2>
        </div>
        <div class="card-grid">
            <?php foreach($posts as $post): ?>
            <article class="card">
                <h3><a href="<?= e(url('/blog/post.php')) ?>?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h3>
                <p><?= e($post['excerpt'] ?: 'Read latest updates from ESAHub Africa.') ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container card">
        <h2>Contact Us</h2>
        <p><?= e($settings['hub_address']) ?></p>
        <p><a href="tel:<?= e($settings['phone']) ?>"><?= e($settings['phone']) ?></a> • <a href="mailto:<?= e($settings['email']) ?>"><?= e($settings['email']) ?></a></p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
