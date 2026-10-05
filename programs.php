<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Programs | ESAHub Africa';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$selectedCategory = trim((string) ($_GET['category'] ?? ''));

$categoryFields = ['id', 'name', 'slug'];
if ($pdo && column_exists($pdo, 'categories', 'description')) { $categoryFields[] = 'description'; }
if ($pdo && column_exists($pdo, 'categories', 'featured_image')) { $categoryFields[] = 'featured_image'; }

$categorySql = 'SELECT ' . implode(', ', array_map(fn($field) => 'c.' . $field, $categoryFields)) . ' FROM categories c WHERE c.is_active = 1 ORDER BY c.name ASC';
$categories = $pdo ? $pdo->query($categorySql)->fetchAll() : [];

$selected = null;
$programs = [];
if ($selectedCategory !== '') {
    foreach ($categories as $category) {
        if (($category['slug'] ?? '') === $selectedCategory) { $selected = $category; break; }
    }

    if ($selected && $pdo) {
        $programFields = ['p.id', 'p.title', 'p.slug', 'p.summary', 'p.delivery_mode', 'p.duration', 'p.price_label', 'p.featured_image', 'c.name AS category_name'];
        if (column_exists($pdo, 'programs', 'subtitle')) { $programFields[] = 'p.subtitle'; }
        if (column_exists($pdo, 'programs', 'description')) { $programFields[] = 'p.description'; }
        if (column_exists($pdo, 'programs', 'registration_link')) { $programFields[] = 'p.registration_link'; }
        $programSql = 'SELECT ' . implode(', ', $programFields) . ' FROM programs p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? AND p.status = "published" AND p.is_active = 1 AND c.is_active = 1 ORDER BY p.created_at DESC';
        $programsStmt = $pdo->prepare($programSql);
        $programsStmt->execute([$selected['id']]);
        $programs = $programsStmt->fetchAll();
    }
} else {
    if ($pdo) {
        $programFields = ['p.id', 'p.title', 'p.slug', 'p.summary', 'p.delivery_mode', 'p.duration', 'p.price_label', 'p.featured_image', 'c.name AS category_name'];
        if (column_exists($pdo, 'programs', 'subtitle')) { $programFields[] = 'p.subtitle'; }
        if (column_exists($pdo, 'programs', 'description')) { $programFields[] = 'p.description'; }
        if (column_exists($pdo, 'programs', 'registration_link')) { $programFields[] = 'p.registration_link'; }
        $programSql = 'SELECT ' . implode(', ', $programFields) . ' FROM programs p JOIN categories c ON c.id = p.category_id WHERE p.status = "published" AND p.is_active = 1 AND c.is_active = 1 ORDER BY c.name ASC, p.created_at DESC';
        $programs = $pdo->query($programSql)->fetchAll();
    }
}

require_once __DIR__ . '/includes/navbar.php';

?>
    <div class="container">
        <div class="section-title">
            <h2>Programs</h2>
            <p>Explore our categories and discover practical learning and innovation opportunities.</p>
        </div>

        <div class="main" style="margin-top:1.5rem;">
            <?php if ($selected && !empty($selected['description'])): ?>
                <div class="card" style="margin-bottom:1rem;">
                    <?php if (!empty($selected['featured_image'])): ?>
                        <img src="<?= e(url('assets/images/uploads/' . $selected['featured_image'])) ?>" alt="<?= e($selected['name']) ?>" style="width:100%;max-height:260px;object-fit:cover;border-radius:12px;margin-bottom:1rem;">
                    <?php endif; ?>
                    <h3><?= e($selected['name']) ?></h3>
                    <p><?= e($selected['description']) ?></p>
                </div>
            <?php endif; ?>

            <div style="display:flex;justify-content:flex-end;margin-bottom:0.75rem;gap:0.5rem;align-items:center;">
                <label for="program-sort" class="muted" style="margin-right:0.5rem;">Sort:</label>
                <select id="program-sort" style="padding:0.45rem 0.6rem;border-radius:8px;border:1px solid #D6DCE6;">
                    <option value="latest">Latest</option>
                    <option value="alpha">A → Z</option>
                    <option value="popular">Popular</option>
                </select>
            </div>

            <div class="program-list">
                <?php if (!empty($programs)): foreach ($programs as $program): ?>
                    <article class="program-card card">
                        <div class="thumb">
                            <?php if (!empty($program['featured_image'])): ?>
                                <img src="<?= e(url('assets/images/uploads/' . $program['featured_image'])) ?>" alt="<?= e($program['title']) ?>">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(90deg,var(--accent-soft),#fff);display:flex;align-items:center;justify-content:center;color:var(--primary);">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="program-meta"><span class="badge"><?= e(ucfirst($program['delivery_mode'])) ?></span><div class="muted"><?= e($program['category_name'] ?? '') ?></div></div>
                            <h3><a href="<?= e(url('programs/view.php')) ?>?slug=<?= e($program['slug']) ?>"><?= e($program['title']) ?></a></h3>
                            <?php if (!empty($program['subtitle'])): ?><div class="text-sm"><strong><?= e($program['subtitle']) ?></strong></div><?php endif; ?>
                            <p class="muted" style="margin-top:0.5rem"><?= e($program['summary']) ?></p>
                            <div class="program-cta">
                                <a class="btn btn-primary btn-small" href="<?= e(url('programs/view.php')) ?>?slug=<?= e($program['slug']) ?>">View Details</a>
                                <?php if (!empty($program['registration_link'])): ?>
                                    <a class="btn btn-outline btn-small" href="<?= e($program['registration_link']) ?>" target="_blank" rel="noopener">Register</a>
                                <?php else: ?>
                                    <a class="btn btn-outline btn-small" href="<?= e(url('contact.php')) ?>">Book Now</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; else: ?>
                    <div class="card">No programs found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
