<?php
// Minimal helpers for public pages. Adds db(), url(), and get_settings().

require_once __DIR__ . '/config.php';
// Ensure PDO is available; db.php creates $pdo.
if (file_exists(__DIR__ . '/db.php')) {
    require_once __DIR__ . '/db.php';
}

if (!function_exists('db')) {
    function db()
    {
        global $pdo;
        return $pdo ?? null;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(BASE_URL, '/');
        $path = ltrim($path, '/');
        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

function get_settings(): array
{
    $defaults = [
        'hub_address' => CONTACT_ADDRESS ?? '',
        'phone' => CONTACT_PHONE ?? '',
        'email' => CONTACT_EMAIL ?? '',
    ];

    try {
        $pdo = db();
        if ($pdo) {
            // attempt to read a simple settings table if present (key/value)
            $stmt = $pdo->query("SELECT `k`,`v` FROM settings LIMIT 100");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    // store common keys if present
                    $defaults[$r['k']] = $r['v'];
                }
            }
        }
    } catch (Throwable $e) {
        // ignore — fall back to defaults
    }

    return $defaults;
}

function get_blog_posts(): array
{
    $pdo = db();
    if (!$pdo) {
        return [];
    }

    try {
        $stmt = $pdo->query("SELECT id, title, slug, excerpt, content, featured_image, status, created_at, updated_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Throwable $e) {
        return [];
    }
}

function get_blog_post_by_slug(string $slug): ?array
{
    $pdo = db();
    if (!$pdo || $slug === '') {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ?: null;
    } catch (Throwable $e) {
        return null;
    }
}
