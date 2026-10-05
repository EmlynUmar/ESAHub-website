<?php
require_once __DIR__ . '/../includes/functions.php';
$page_title = 'Blog | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';

$posts = get_blog_posts();
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
            <?php if (empty($posts)): ?>
                <p>No posts published yet. Please check back soon.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <?php $excerpt = !empty($post['excerpt']) ? $post['excerpt'] : $post['content']; ?>
                    <article class="blog-card">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?= e(url('assets/images/uploads/' . $post['featured_image'])) ?>" alt="<?= e($post['title']) ?>">
                        <?php else: ?>
                            <img src="<?= e(url('assets/images/hero.svg')) ?>" alt="ESAHub Africa">
                        <?php endif; ?>
                        <div>
                            <h3><?= e($post['title']) ?></h3>
                            <p><?= e(mb_strimwidth(strip_tags($excerpt), 0, 160, '...')) ?></p>
                            <a class="btn btn-primary" href="<?= e(url('blog/post.php?slug=' . urlencode((string) ($post['slug'] ?? '')))) ?>">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
