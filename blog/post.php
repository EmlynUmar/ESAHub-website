<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: ' . url('blog/index.php'));
    exit;
}

$post = get_blog_post_by_slug($slug);

if (!$post) {
    http_response_code(404);
    $page_title = 'Post Not Found';
} else {
    $page_title = $post['title'] . ' | ESAHub Africa';
}

require_once __DIR__ . '/../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <?php if (!$post): ?>
            <div class="section-title">
                <h2>Post Not Found</h2>
                <p>The post you are looking for does not exist or has been unpublished.</p>
            </div>
            <a class="btn btn-primary" href="<?= e(url('blog/index.php')) ?>">Back to Blog</a>
        <?php else: ?>
            <div class="section-title">
                <h2><?= e($post['title']) ?></h2>
                <p>Published on <?= e(date('F j, Y', strtotime((string) ($post['created_at'] ?? date('Y-m-d H:i:s'))))) ?></p>
            </div>
            <?php if (!empty($post['featured_image'])): ?>
                <img src="<?= e(url('assets/images/uploads/' . $post['featured_image'])) ?>" alt="<?= e($post['title']) ?>">
            <?php endif; ?>
            <div style="margin-top: 1.5rem;">
                <?= nl2br(e(!empty($post['content']) ? $post['content'] : ($post['excerpt'] ?? ''))); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
