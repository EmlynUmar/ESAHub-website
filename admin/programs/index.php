<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$stmt = $pdo->prepare('SELECT p.id,p.title,p.slug,p.status,p.is_active,c.name AS category_name FROM programs p JOIN categories c ON c.id=p.category_id ORDER BY p.updated_at DESC');
$stmt->execute();
$programs = $stmt->fetchAll();
$page_title = 'Manage Programs | ESAHub Admin';
require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Programs</h2>
            <p>Manage programs and their visibility.</p>
        </div>
        <p><a class="btn btn-primary" href="<?= e(url('admin/programs/create.php')) ?>">Add Program</a></p>

        <table class="table">
            <thead>
                <tr><th>Title</th><th>Category</th><th>Status</th><th>Active</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($programs)): ?>
                    <tr><td colspan="5">No programs yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($programs as $p): ?>
                        <tr>
                            <td><?= e($p['title']) ?></td>
                            <td><?= e($p['category_name']) ?></td>
                            <td><?= e($p['status']) ?></td>
                            <td>
                                <form method="post" action="<?= e(url('admin/programs/toggle.php')) ?>" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="active" value="<?= $p['is_active'] ? '0' : '1' ?>">
                                    <button class="btn" type="submit"><?= $p['is_active'] ? 'Active' : 'Inactive' ?></button>
                                </form>
                            </td>
                            <td>
                                <a href="<?= e(url('admin/programs/edit.php?id=' . $p['id'])) ?>" style="color:var(--accent); font-weight:600; margin-right:0.75rem;">Edit</a>
                                <form method="post" action="<?= e(url('admin/programs/delete.php')) ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <button type="submit" style="background:none;border:none;color:#c84e3a;font-weight:600;cursor:pointer;padding:0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
