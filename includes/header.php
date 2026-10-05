<?php
require_once __DIR__ . '/config.php';
$page_title = $page_title ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
    <link rel="icon" type="image/x-icon" href="<?= e(base_url('favicon.ico')) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= e(base_url('assets/images/favicon-32x32.png')) ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= e(base_url('assets/images/favicon-16x16.png')) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(base_url('assets/images/apple-touch-icon.png')) ?>">
    <meta name="theme-color" content="#0F2B46">
</head>
<body>
