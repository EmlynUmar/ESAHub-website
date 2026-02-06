<?php
$page_title = 'Admin Dashboard | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$stmt = $pdo->prepare('SELECT id, title, slug, status, created_at FROM posts ORDER BY created_at DESC');
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Welcome, <?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?></h2>
            <p>Manage your blog posts and content updates.</p>
        </div>
        <a class="btn btn-primary" href="<?php echo e(base_url('admin/create-post.php')); ?>">Create New Post</a>
        <a class="btn btn-outline" href="<?php echo e(base_url('admin/logout.php')); ?>">Logout</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$posts): ?>
                    <tr>
                        <td colspan="4">No posts yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo e($post['title']); ?></td>
                            <td>
                                <span class="badge <?php echo $post['status'] === 'published' ? 'published' : 'draft'; ?>">
                                    <?php echo e(ucfirst($post['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo e(date('M j, Y', strtotime($post['created_at']))); ?></td>
                            <td>
                                <a href="<?php echo e(base_url('admin/edit-post.php?id=' . (string) $post['id'])); ?>">Edit</a>
                                |
                                <form method="post" action="<?php echo e(base_url('admin/delete-post.php')); ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="id" value="<?php echo e((string) $post['id']); ?>">
                                    <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;" onclick="return confirm('Delete this post?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
