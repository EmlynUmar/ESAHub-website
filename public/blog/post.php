<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header('Location: ' . base_url('blog/index.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT title, content, featured_image, created_at FROM posts WHERE slug = ? AND status = ? LIMIT 1');
$stmt->execute([$slug, 'published']);
$post = $stmt->fetch();

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
            <a class="btn btn-primary" href="<?php echo e(base_url('blog/index.php')); ?>">Back to Blog</a>
        <?php else: ?>
            <div class="section-title">
                <h2><?php echo e($post['title']); ?></h2>
                <p>Published on <?php echo e(date('F j, Y', strtotime($post['created_at']))); ?></p>
            </div>
            <?php if (!empty($post['featured_image'])): ?>
                <img src="<?php echo e(base_url('assets/images/uploads/' . $post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>">
            <?php endif; ?>
            <div style="margin-top: 1.5rem;">
                <?php echo nl2br(e($post['content'])); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
