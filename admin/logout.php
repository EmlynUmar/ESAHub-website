<?php
require_once __DIR__ . '/../includes/config.php';

session_destroy();
<<<<<<< HEAD:admin/logout.php
header('Location: ' . url('admin/login.php'));
=======
header('Location: ' . base_url('admin/login.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/logout.php
exit;
