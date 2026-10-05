<?php
$page_title = 'Admin Dashboard | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$totalPosts = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$publishedPosts = (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$draftPosts = (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();

$totalPrograms = (int) $pdo->query('SELECT COUNT(*) FROM programs')->fetchColumn();
$activePrograms = (int) $pdo->query("SELECT COUNT(*) FROM programs WHERE is_active = 1")->fetchColumn();
$inactivePrograms = (int) $pdo->query("SELECT COUNT(*) FROM programs WHERE is_active = 0")->fetchColumn();

$totalCategories = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$activeCategories = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn();

$recentPosts = $pdo->query('SELECT id, title, status, created_at FROM posts ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentPrograms = $pdo->query('SELECT p.id, p.title, p.status, p.is_active, c.name AS category_name FROM programs p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.updated_at DESC LIMIT 5')->fetchAll();
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section dashboard-shell">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Welcome, <?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?></h1>
                <p class="muted">Manage blog content, programs, categories, and site settings from one dashboard.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-primary" href="<?= e(url('admin/create-post.php')) ?>">New Post</a>
                <a class="btn btn-outline" href="<?= e(url('admin/programs/create.php')) ?>">Add Program</a>
                <a class="btn btn-outline" href="<?= e(url('admin/logout.php')) ?>">Logout</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="metric-card">
                <span>Posts</span>
                <strong><?= $totalPosts ?></strong>
                <small><?= $publishedPosts ?> published • <?= $draftPosts ?> draft</small>
            </div>
            <div class="metric-card">
                <span>Programs</span>
                <strong><?= $totalPrograms ?></strong>
                <small><?= $activePrograms ?> active • <?= $inactivePrograms ?> inactive</small>
            </div>
            <div class="metric-card">
                <span>Categories</span>
                <strong><?= $totalCategories ?></strong>
                <small><?= $activeCategories ?> active</small>
            </div>
            <div class="metric-card">
                <span>Quick Actions</span>
                <strong>4</strong>
                <small>sections ready</small>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="panel panel-wide">
                <div class="panel-header">
                    <h2>Website Management</h2>
                </div>
                <div class="quick-links">
                    <a href="<?= e(url('admin/create-post.php')) ?>" class="quick-link">
                        <span class="icon">✍️</span>
                        <div>
                            <strong>Create Blog Post</strong>
                            <small>Publish updates and stories</small>
                        </div>
                    </a>
                    <a href="<?= e(url('admin/programs/index.php')) ?>" class="quick-link">
                        <span class="icon">📚</span>
                        <div>
                            <strong>Manage Programs</strong>
                            <small>Activate, edit, and organize training offerings</small>
                        </div>
                    </a>
                    <a href="<?= e(url('admin/categories/index.php')) ?>" class="quick-link">
                        <span class="icon">🗂️</span>
                        <div>
                            <strong>Manage Categories</strong>
                            <small>Control public program groups</small>
                        </div>
                    </a>
                    <a href="<?= e(url('admin/blog/index.php')) ?>" class="quick-link">
                        <span class="icon">📝</span>
                        <div>
                            <strong>Manage Blog Posts</strong>
                            <small>Create and publish blog content</small>
                        </div>
                    </a>
                    <a href="<?= e(url('admin/settings/index.php')) ?>" class="quick-link">
                        <span class="icon">⚙️</span>
                        <div>
                            <strong>Site Settings</strong>
                            <small>Update hero, impact, and contact details</small>
                        </div>
                    </a>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2>Recent Posts</h2>
                    <a href="<?= e(url('admin/create-post.php')) ?>">Add</a>
                </div>
                <ul class="list-panel">
                    <?php if (!$recentPosts): ?>
                        <li>No posts yet.</li>
                    <?php else: ?>
                        <?php foreach ($recentPosts as $post): ?>
                            <li>
                                <div>
                                    <strong><?= e($post['title']) ?></strong>
                                    <small><?= e(ucfirst($post['status'])) ?> • <?= e(date('M j, Y', strtotime($post['created_at']))) ?></small>
                                </div>
                                <a href="<?= e(url('admin/edit-post.php?id=' . $post['id'])) ?>">Edit</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2>Recent Programs</h2>
                    <a href="<?= e(url('admin/programs/index.php')) ?>">View all</a>
                </div>
                <ul class="list-panel">
                    <?php if (!$recentPrograms): ?>
                        <li>No programs yet.</li>
                    <?php else: ?>
                        <?php foreach ($recentPrograms as $program): ?>
                            <li>
                                <div>
                                    <strong><?= e($program['title']) ?></strong>
                                    <small><?= e($program['category_name'] ?: 'Uncategorized') ?> • <?= e($program['is_active'] ? 'Active' : 'Inactive') ?></small>
                                </div>
                                <a href="<?= e(url('admin/programs/edit.php?id=' . $program['id'])) ?>">Edit</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
