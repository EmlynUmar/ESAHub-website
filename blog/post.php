<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
<<<<<<< HEAD:blog/post.php
    header('Location: ' . url('blog/index.php'));
=======
    header('Location: ' . base_url('blog/index.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/blog/post.php
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
<<<<<<< HEAD:blog/post.php
            <a class="btn btn-primary" href="<?= e(url('blog/index.php')) ?>">Back to Blog</a>
=======
            <a class="btn btn-primary" href="<?php echo e(base_url('blog/index.php')); ?>">Back to Blog</a>
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/blog/post.php
        <?php else: ?>
            <div class="section-title">
                <h2><?= e($post['title']) ?></h2>
                <p>Published on <?= e(date('F j, Y', strtotime((string) ($post['created_at'] ?? date('Y-m-d H:i:s'))))) ?></p>
            </div>
            <?php if (!empty($post['featured_image'])): ?>
<<<<<<< HEAD:blog/post.php
                <img src="<?= e(url('assets/images/uploads/' . $post['featured_image'])) ?>" alt="<?= e($post['title']) ?>">
=======
                <img src="<?php echo e(base_url('assets/images/uploads/' . $post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>">
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/blog/post.php
            <?php endif; ?>
            <div style="margin-top: 1.5rem;">
                <?= nl2br(e(!empty($post['content']) ? $post['content'] : ($post['excerpt'] ?? ''))); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
