<?php
// Global configuration for ESAHub Africa site.

declare(strict_types=1);

// Production error handling: log errors silently without leaking system details
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Secure session configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

define('APP_NAME', 'ESAHub Africa');
// Base URL relative to the web root (auto-detected for local subfolders).
$public_dir = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'])) : '';
$base_url = '';
if ($doc_root && strpos($public_dir, $doc_root) === 0) {
    $base_url = substr($public_dir, strlen($doc_root));
}
define('BASE_URL', $base_url ?: '');

define('CONTACT_PHONE', '+2347013596333');

define('CONTACT_EMAIL', 'esahubafrica@gmail.com');

define('CONTACT_ADDRESS', '1st floor of Risk Mitigation and Engineering Plaza opp zone 1 police station Buk Road Kano.');

// SMTP settings (placeholders - update with your cPanel mailbox details).
define('SMTP_HOST', 'mail.yourdomain.com'); // e.g. mail.esahubafrica.org
define('SMTP_PORT', 587); // 465 for SSL, 587 for TLS
define('SMTP_USER', 'contact@yourdomain.com');
define('SMTP_PASS', 'your_smtp_password');
define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'
define('SMTP_FROM', 'contact@yourdomain.com');
define('SMTP_FROM_NAME', 'ESAHub Africa');

// Database credentials - supports environment variables or direct editing.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'esahub');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function base_url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');

    if ($base === '') {
        return $path === '' ? '/' : '/' . $path;
    }

    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: ' . base_url('admin/login.php'));
        exit;
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(BASE_URL, '/');
        $path = ltrim($path, '/');
        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

function generate_slug(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug ?? '', '-') ?: 'post';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `" . str_replace('`', '', $table) . "` LIKE '" . str_replace("'", "\\'", $column) . "'");
    return $stmt !== false && $stmt->fetch() !== false;
}
