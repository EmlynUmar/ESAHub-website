<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$stmt = $pdo->prepare('SELECT id,name,slug,is_active FROM categories ORDER BY name');
$stmt->execute();
$cats = $stmt->fetchAll();
$page_title = 'Manage Categories | ESAHub Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section">
    <div class="container">
        <div class="section-title"><h2>Categories</h2><p>Manage categories and their visibility.</p></div>
        <p><a class="btn btn-primary" href="<?= e(url('admin/categories/create.php')) ?>">Add Category</a></p>
        <table class="table"><thead><tr><th>Name</th><th>Slug</th><th>Active</th><th>Actions</th></tr></thead><tbody>
        <?php if (empty($cats)): ?>
            <tr><td colspan="4">No categories.</td></tr>
        <?php else: foreach($cats as $c): ?>
            <tr>
                <td><?= e($c['name']) ?></td>
                <td><?= e($c['slug']) ?></td>
                <td>
                    <form method="post" action="<?= e(url('admin/categories/bulk_toggle.php')) ?>" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <input type="hidden" name="active" value="<?= $c['is_active'] ? '0' : '1' ?>">
                        <button class="btn" type="submit"><?= $c['is_active'] ? 'Active' : 'Inactive' ?></button>
                    </form>
                </td>
                <td><a href="<?= e(url('admin/categories/edit.php?id=' . $c['id'])) ?>">Edit</a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody></table>
    </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
