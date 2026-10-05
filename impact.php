<?php
require_once __DIR__ . '/includes/functions.php';

$settings = get_settings();
$page_title = 'Impact | ESAHub Africa';
require_once __DIR__ . '/includes/header.php';

$impactTitle = $settings['impact_title'] ?? 'Our Impact';
$impactSubtitle = $settings['impact_subtitle'] ?? 'We measure success by the lives transformed and communities strengthened.';
$impactStats = [
    ['value' => $settings['impact_stat_1_value'] ?? '1,500+', 'label' => $settings['impact_stat_1_label'] ?? 'Learners trained'],
    ['value' => $settings['impact_stat_2_value'] ?? '50+', 'label' => $settings['impact_stat_2_label'] ?? 'Community workshops'],
    ['value' => $settings['impact_stat_3_value'] ?? '30+', 'label' => $settings['impact_stat_3_label'] ?? 'Partner organizations'],
    ['value' => $settings['impact_stat_4_value'] ?? '10+', 'label' => $settings['impact_stat_4_label'] ?? 'Innovation projects'],
];
$impactStories = [
    ['title' => $settings['impact_story_1_title'] ?? 'Community Tech Fair', 'description' => $settings['impact_story_1_description'] ?? 'Highlight of student projects and partner showcases.', 'image' => $settings['impact_story_1_image'] ?? ''],
    ['title' => $settings['impact_story_2_title'] ?? 'Women in Digital', 'description' => $settings['impact_story_2_description'] ?? 'Celebrating graduates of Her Digital Canvas.', 'image' => $settings['impact_story_2_image'] ?? ''],
    ['title' => $settings['impact_story_3_title'] ?? 'Startup Spotlight', 'description' => $settings['impact_story_3_description'] ?? 'Showcasing innovative startups from our incubator.', 'image' => $settings['impact_story_3_image'] ?? ''],
];
?>
<header>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2><?= e($impactTitle) ?></h2>
            <p><?= e($impactSubtitle) ?></p>
        </div>
        <div class="stats">
            <?php foreach ($impactStats as $stat): ?>
                <div class="stat">
                    <strong><?= e($stat['value']) ?></strong>
                    <p><?= e($stat['label']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section highlight">
    <div class="container">
        <div class="section-title">
            <h2>Stories of Change</h2>
            <p>Highlighted outcomes and community impact from our programs.</p>
        </div>
        <div class="card-grid">
            <?php foreach ($impactStories as $story): ?>
                <div class="card">
                    <?php if (!empty($story['image'])): ?>
                        <img src="<?= e(url('assets/images/' . $story['image'])) ?>" alt="<?= e($story['title']) ?>" style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:1rem;">
                    <?php endif; ?>
                    <h3><?= e($story['title']) ?></h3>
                    <p><?= e($story['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
