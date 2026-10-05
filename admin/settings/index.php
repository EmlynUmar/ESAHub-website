<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_admin();

$page_title = 'Site Settings | ESAHub Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error_message = 'Security token expired. Please try again.';
    } else {
        $fields = [
            'hero_title' => trim((string) ($_POST['hero_title'] ?? '')),
            'hero_subtitle' => trim((string) ($_POST['hero_subtitle'] ?? '')),
            'programs_section_title' => trim((string) ($_POST['programs_section_title'] ?? '')),
            'programs_section_subtitle' => trim((string) ($_POST['programs_section_subtitle'] ?? '')),
            'service_1_title' => trim((string) ($_POST['service_1_title'] ?? '')),
            'service_1_description' => trim((string) ($_POST['service_1_description'] ?? '')),
            'service_2_title' => trim((string) ($_POST['service_2_title'] ?? '')),
            'service_2_description' => trim((string) ($_POST['service_2_description'] ?? '')),
            'impact_title' => trim((string) ($_POST['impact_title'] ?? '')),
            'impact_subtitle' => trim((string) ($_POST['impact_subtitle'] ?? '')),
            'impact_stat_1_value' => trim((string) ($_POST['impact_stat_1_value'] ?? '')),
            'impact_stat_1_label' => trim((string) ($_POST['impact_stat_1_label'] ?? '')),
            'impact_stat_2_value' => trim((string) ($_POST['impact_stat_2_value'] ?? '')),
            'impact_stat_2_label' => trim((string) ($_POST['impact_stat_2_label'] ?? '')),
            'impact_stat_3_value' => trim((string) ($_POST['impact_stat_3_value'] ?? '')),
            'impact_stat_3_label' => trim((string) ($_POST['impact_stat_3_label'] ?? '')),
            'impact_stat_4_value' => trim((string) ($_POST['impact_stat_4_value'] ?? '')),
            'impact_stat_4_label' => trim((string) ($_POST['impact_stat_4_label'] ?? '')),
            'impact_story_1_title' => trim((string) ($_POST['impact_story_1_title'] ?? '')),
            'impact_story_1_description' => trim((string) ($_POST['impact_story_1_description'] ?? '')),
            'impact_story_2_title' => trim((string) ($_POST['impact_story_2_title'] ?? '')),
            'impact_story_2_description' => trim((string) ($_POST['impact_story_2_description'] ?? '')),
            'impact_story_3_title' => trim((string) ($_POST['impact_story_3_title'] ?? '')),
            'impact_story_3_description' => trim((string) ($_POST['impact_story_3_description'] ?? '')),
            'hub_address' => trim((string) ($_POST['hub_address'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
        ];

        try {
            $stmt = $pdo->prepare('INSERT INTO settings (`k`, `v`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)');
            foreach ($fields as $key => $value) {
                $stmt->execute([$key, $value]);
            }
        } catch (Throwable $e) {
            $error_message = 'Unable to save settings. Please check the database schema.';
        }

        $heroImages = [];
        if (!empty($_FILES['hero_images']['name'][0])) {
            $uploadDir = __DIR__ . '/../../assets/images';
            if (!is_dir($uploadDir . '/uploads')) {
                mkdir($uploadDir . '/uploads', 0777, true);
            }

            foreach ($_FILES['hero_images']['name'] as $index => $name) {
                if ($name === '') continue;
                $tmp = $_FILES['hero_images']['tmp_name'][$index];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp','svg'], true)) {
                    $error_message = 'Only JPG, PNG, WebP, and SVG hero images are allowed.';
                    break;
                }

                $newName = 'hero_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadDir . '/uploads/' . $newName)) {
                    $heroImages[] = 'uploads/' . $newName;
                }
            }

            if (!empty($heroImages)) {
                try {
                    $stmt = $pdo->prepare('INSERT INTO settings (`k`,`v`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)');
                    $stmt->execute(['hero_images', json_encode($heroImages)]);
                } catch (Throwable $e) {
                    $error_message = 'Unable to save hero images. Please check the database schema.';
                }
            }
        }

        // Handle impact story image uploads
        $storyImages = ['impact_story_1_image', 'impact_story_2_image', 'impact_story_3_image'];
        foreach ($storyImages as $imageField) {
            if (!empty($_FILES[$imageField]['name'])) {
                $uploadDir = __DIR__ . '/../../assets/images';
                if (!is_dir($uploadDir . '/uploads')) {
                    mkdir($uploadDir . '/uploads', 0777, true);
                }

                $name = $_FILES[$imageField]['name'];
                $tmp = $_FILES[$imageField]['tmp_name'];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
                    $error_message = 'Only JPG, PNG, and WebP story images are allowed.';
                    break;
                }

                $newName = str_replace('impact_story_', 'story_', $imageField) . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadDir . '/uploads/' . $newName)) {
                    try {
                        $stmt = $pdo->prepare('INSERT INTO settings (`k`,`v`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)');
                        $stmt->execute([$imageField, 'uploads/' . $newName]);
                    } catch (Throwable $e) {
                        $error_message = 'Unable to save story image path.';
                    }
                }
            }
        }

        if (empty($error_message)) {
            $success_message = 'Site settings updated successfully.';
        }
    }
}

try {
    $stmt = $pdo->query('SELECT `k`, `v` FROM settings');
    $settings = $stmt ? $stmt->fetchAll(PDO::FETCH_KEY_PAIR) : [];
} catch (Throwable $e) {
    $settings = [];
}

$heroTitle = $settings['hero_title'] ?? 'Empowering communities through practical education and innovation.';
$heroSubtitle = $settings['hero_subtitle'] ?? 'ESAHub Africa supports youth, women, and families with career, business, and digital skills programs.';
$heroImagesValue = $settings['hero_images'] ?? json_encode(['uploads/hero.svg']);
$programsTitle = $settings['programs_section_title'] ?? 'Our Programs';
$programsSubtitle = $settings['programs_section_subtitle'] ?? 'Practical training and support designed for people who want to learn, grow, and build better futures.';
$service1Title = $settings['service_1_title'] ?? 'Hall Booking';
$service1Description = $settings['service_1_description'] ?? 'Secure a welcoming, well-equipped venue for trainings, meetings, workshops, and community events.';
$service2Title = $settings['service_2_title'] ?? 'Business Consultation';
$service2Description = $settings['service_2_description'] ?? 'Get practical guidance on business planning, strategy, and growth for entrepreneurs and SMEs.';
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
$address = $settings['hub_address'] ?? CONTACT_ADDRESS;
$phone = $settings['phone'] ?? CONTACT_PHONE;
$email = $settings['email'] ?? CONTACT_EMAIL;

require_once __DIR__ . '/../../includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container" style="max-width:1000px;">
        <div class="section-title">
            <h2>Site Settings</h2>
            <p>Update the public website messaging, media, and service details.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="form-message error"><?php echo e($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="form-message success"><?php echo e($success_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="post" action="<?= e(url('admin/settings/index.php')) ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="form-group">
                    <label for="hero_title">Hero Title</label>
                    <input id="hero_title" name="hero_title" type="text" value="<?= e($heroTitle) ?>">
                </div>

                <div class="form-group">
                    <label for="hero_subtitle">Hero Subtitle</label>
                    <textarea id="hero_subtitle" name="hero_subtitle"><?= e($heroSubtitle) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="hero_images">Hero Image Carousel</label>
                    <input id="hero_images" name="hero_images[]" type="file" accept="image/*" multiple>
                    <small>Upload one or more images. The first image appears in the hero carousel.</small>
                </div>

                <div class="form-group">
                    <label for="programs_section_title">Programs Section Title</label>
                    <input id="programs_section_title" name="programs_section_title" type="text" value="<?= e($programsTitle) ?>">
                </div>

                <div class="form-group">
                    <label for="programs_section_subtitle">Programs Section Subtitle</label>
                    <textarea id="programs_section_subtitle" name="programs_section_subtitle"><?= e($programsSubtitle) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="impact_title">Impact Section Title</label>
                    <input id="impact_title" name="impact_title" type="text" value="<?= e($impactTitle) ?>">
                </div>
                <div class="form-group">
                    <label for="impact_subtitle">Impact Section Subtitle</label>
                    <textarea id="impact_subtitle" name="impact_subtitle"><?= e($impactSubtitle) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="impact_stat_1_value">Impact Stat 1 Value</label>
                    <input id="impact_stat_1_value" name="impact_stat_1_value" type="text" value="<?= e($impactStats[0]['value']) ?>">
                    <label for="impact_stat_1_label">Impact Stat 1 Label</label>
                    <input id="impact_stat_1_label" name="impact_stat_1_label" type="text" value="<?= e($impactStats[0]['label']) ?>">
                </div>
                <div class="form-group">
                    <label for="impact_stat_2_value">Impact Stat 2 Value</label>
                    <input id="impact_stat_2_value" name="impact_stat_2_value" type="text" value="<?= e($impactStats[1]['value']) ?>">
                    <label for="impact_stat_2_label">Impact Stat 2 Label</label>
                    <input id="impact_stat_2_label" name="impact_stat_2_label" type="text" value="<?= e($impactStats[1]['label']) ?>">
                </div>
                <div class="form-group">
                    <label for="impact_stat_3_value">Impact Stat 3 Value</label>
                    <input id="impact_stat_3_value" name="impact_stat_3_value" type="text" value="<?= e($impactStats[2]['value']) ?>">
                    <label for="impact_stat_3_label">Impact Stat 3 Label</label>
                    <input id="impact_stat_3_label" name="impact_stat_3_label" type="text" value="<?= e($impactStats[2]['label']) ?>">
                </div>
                <div class="form-group">
                    <label for="impact_stat_4_value">Impact Stat 4 Value</label>
                    <input id="impact_stat_4_value" name="impact_stat_4_value" type="text" value="<?= e($impactStats[3]['value']) ?>">
                    <label for="impact_stat_4_label">Impact Stat 4 Label</label>
                    <input id="impact_stat_4_label" name="impact_stat_4_label" type="text" value="<?= e($impactStats[3]['label']) ?>">
                </div>

                <div class="form-group">
                    <label for="impact_story_1_title">Impact Story 1 Title</label>
                    <input id="impact_story_1_title" name="impact_story_1_title" type="text" value="<?= e($impactStories[0]['title']) ?>">
                    <label for="impact_story_1_description">Description</label>
                    <textarea id="impact_story_1_description" name="impact_story_1_description"><?= e($impactStories[0]['description']) ?></textarea>
                    <label for="impact_story_1_image">Story Image</label>
                    <input id="impact_story_1_image" name="impact_story_1_image" type="file" accept="image/*">
                    <small><?= !empty($impactStories[0]['image']) ? 'Current: ' . e($impactStories[0]['image']) : 'No image uploaded' ?></small>
                </div>
                <div class="form-group">
                    <label for="impact_story_2_title">Impact Story 2 Title</label>
                    <input id="impact_story_2_title" name="impact_story_2_title" type="text" value="<?= e($impactStories[1]['title']) ?>">
                    <label for="impact_story_2_description">Description</label>
                    <textarea id="impact_story_2_description" name="impact_story_2_description"><?= e($impactStories[1]['description']) ?></textarea>
                    <label for="impact_story_2_image">Story Image</label>
                    <input id="impact_story_2_image" name="impact_story_2_image" type="file" accept="image/*">
                    <small><?= !empty($impactStories[1]['image']) ? 'Current: ' . e($impactStories[1]['image']) : 'No image uploaded' ?></small>
                </div>
                <div class="form-group">
                    <label for="impact_story_3_title">Impact Story 3 Title</label>
                    <input id="impact_story_3_title" name="impact_story_3_title" type="text" value="<?= e($impactStories[2]['title']) ?>">
                    <label for="impact_story_3_description">Description</label>
                    <textarea id="impact_story_3_description" name="impact_story_3_description"><?= e($impactStories[2]['description']) ?></textarea>
                    <label for="impact_story_3_image">Story Image</label>
                    <input id="impact_story_3_image" name="impact_story_3_image" type="file" accept="image/*">
                    <small><?= !empty($impactStories[2]['image']) ? 'Current: ' . e($impactStories[2]['image']) : 'No image uploaded' ?></small>
                </div>

                <div class="form-group">
                    <label for="service_1_title">Service 1 Title</label>
                    <input id="service_1_title" name="service_1_title" type="text" value="<?= e($service1Title) ?>">
                </div>
                <div class="form-group">
                    <label for="service_1_description">Service 1 Description</label>
                    <textarea id="service_1_description" name="service_1_description"><?= e($service1Description) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="service_2_title">Service 2 Title</label>
                    <input id="service_2_title" name="service_2_title" type="text" value="<?= e($service2Title) ?>">
                </div>
                <div class="form-group">
                    <label for="service_2_description">Service 2 Description</label>
                    <textarea id="service_2_description" name="service_2_description"><?= e($service2Description) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="hub_address">Address</label>
                    <textarea id="hub_address" name="hub_address"><?= e($address) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" value="<?= e($phone) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?= e($email) ?>">
                </div>

                <button class="btn btn-primary" type="submit">Save Settings</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
