<?php
require_once __DIR__ . '/../includes/config.php';

if (is_admin_logged_in()) {
    header('Location: ' . url('admin/dashboard.php'));
} else {
    header('Location: ' . url('admin/login.php'));
}
exit;
