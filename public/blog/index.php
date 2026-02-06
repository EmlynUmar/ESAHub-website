<?php
$page_title = 'Blog | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->prepare('SELECT id, title, slug, content, featured_image, created_at FROM posts WHERE status = ? ORDER BY created_at DESC');
$stmt->execute(['published']);
$posts = $stmt->fetchAll();
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Insights & Updates</h2>
            <p>Read about our programs, community highlights, and announcements.</p>
        </div>

        <div class="blog-list">
            <?php if (!$posts): ?>
                <p>No posts published yet. Please check back soon.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?php echo e(base_url('assets/images/uploads/' . $post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>">
                        <?php else: ?>
                            <img src="<?php echo e(base_url('assets/images/hero.svg')); ?>" alt="ESAHub Africa">
                        <?php endif; ?>
                        <div>
                            <h3><?php echo e($post['title']); ?></h3>
                            <p><?php echo e(mb_strimwidth(strip_tags($post['content']), 0, 160, '...')); ?></p>
                            <a class="btn btn-primary" href="<?php echo e(base_url('blog/' . $post['slug'])); ?>">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
