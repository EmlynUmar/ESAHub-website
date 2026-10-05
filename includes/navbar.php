<?php
$is_admin_page = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin') !== false;

if ($is_admin_page):
?>
<nav class="navbar container admin-navbar">
    <a class="brand" href="<?= e(url('admin/dashboard.php')) ?>">
        <img src="<?= e(url('assets/images/logo.png')) ?>" alt="ESAHub Africa logo">
        <span class="brand-text">
            <span>ESAHub Admin</span>
            <small>Control Center</small>
        </span>
    </a>
    <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
    <div class="nav-links">
        <a href="<?= e(url('admin/dashboard.php')) ?>">Dashboard</a>
        <a href="<?= e(url('admin/create-post.php')) ?>">New Post</a>
        <a href="<?= e(url('admin/programs/index.php')) ?>">Programs</a>
        <a href="<?= e(url('admin/categories/index.php')) ?>">Categories</a>
        <a href="<?= e(url('admin/inquiries.php')) ?>">Inquiries</a>
        <a href="<?= e(url('admin/settings/index.php')) ?>">Settings</a>
        <a href="<?= e(url('admin/logout.php')) ?>">Logout</a>
    </div>
</nav>
<?php else: ?>
<nav class="navbar container">
    <a class="brand" href="<?= e(url('')) ?>">
        <img src="<?= e(url('assets/images/logo.png')) ?>" alt="ESAHub Africa logo">
        <span class="brand-text">
            <span>ESAHub Africa</span>
            <small>Empowering Growth</small>
        </span>
    </a>
    <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
    <div class="nav-links">
        <a href="<?= e(url('about.php')) ?>">About</a>
        <a href="<?= e(url('programs.php')) ?>">Programs</a>
        <a href="<?= e(url('impact.php')) ?>">Impact</a>
        <a href="<?= e(url('blog/index.php')) ?>">Blog</a>
        <a href="<?= e(url('contact.php')) ?>">Contact</a>
    </div>
</nav>
<?php endif; ?>
