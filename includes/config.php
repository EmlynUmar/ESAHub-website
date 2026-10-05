<?php
// Global configuration for ESAHub Africa site.

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'ESAHub Africa');
define('BASE_URL', '/ESAHub-website/');

define('CONTACT_PHONE', '+2347013596333');

define('CONTACT_EMAIL', 'esahubafrica@gmail.com');

define('CONTACT_ADDRESS', '1st floor of Risk Mitigation and Engineering Plaza opp zone 1 police station Buk Road Kano.');

// Database credentials - update for production.
const DB_HOST = 'localhost';
const DB_NAME = 'esahub';
const DB_USER = 'root';
const DB_PASS = '';

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/login.php');
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
