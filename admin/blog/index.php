<?php
$page_title = 'Manage Blog Posts | ESAHub Admin';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

// Determine which table to use
$table = 'posts'; // Default to posts table
try {
    $check = $pdo->query("SHOW TABLES LIKE 'posts'");
    if (!$check || !$check->fetch()) {
        $table = 'blog_posts';
    }
} catch (Throwable $e) {
    $table = 'blog_posts';
}

$posts = [];
try {
    $stmt = $pdo->query("SELECT id, title, slug, status, created_at, updated_at FROM `" . str_replace('`', '', $table) . "` ORDER BY created_at DESC");
    $posts = $stmt ? $stmt->fetchAll() : [];
} catch (Throwable $e) {
    $error_message = 'Unable to fetch blog posts.';
}

require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Blog Management</p>
                <h1>Blog Posts</h1>
                <p class="muted">Create, edit, and manage published and draft blog posts.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-primary" href="<?= e(url('admin/create-post.php')) ?>">New Post</a>
                <a class="btn btn-outline" href="<?= e(url('admin/dashboard.php')) ?>">Back to Dashboard</a>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="form-message error"><?= e($error_message) ?></div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="card">
                <p class="muted">No blog posts yet. <a href="<?= e(url('admin/create-post.php')) ?>">Create the first one</a>.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><strong><?= e($post['title']) ?></strong></td>
                                <td><code style="font-size:0.85rem;"><?= e($post['slug']) ?></code></td>
                                <td>
                                    <span class="badge <?= $post['status'] === 'published' ? 'published' : 'draft' ?>">
                                        <?= e(ucfirst($post['status'])) ?>
                                    </span>
                                </td>
                                <td><?= e(date('M j, Y', strtotime((string) ($post['created_at'] ?? '')))) ?></td>
                                <td>
                                    <a href="<?= e(url('admin/edit-post.php?id=' . $post['id'])) ?>" style="color:var(--accent); font-weight:600; margin-right:1rem;">Edit</a>
                                    <a href="<?= e(url('admin/blog/delete.php?id=' . $post['id'])) ?>" style="color:#c84e3a; font-weight:600; text-decoration:none;" onclick="return confirm('Delete this post?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
