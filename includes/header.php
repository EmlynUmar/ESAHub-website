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
    <meta name="theme-color" content="#0F2B46">
</head>
<body>
