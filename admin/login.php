<?php
$page_title = 'Admin Login | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

if (is_admin_logged_in()) {
<<<<<<< HEAD:admin/login.php
    header('Location: ' . url('admin/dashboard.php'));
=======
    header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/login.php
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
<<<<<<< HEAD:admin/login.php
            header('Location: ' . url('admin/dashboard.php'));
=======
            header('Location: ' . base_url('admin/dashboard.php'));
>>>>>>> 2838c9eab5cf35e5591d27b4abb2d047e2be9945:public/admin/login.php
            exit;
        }

        $error_message = 'Invalid login credentials.';
    }
}
?>
<div class="admin-wrapper">
    <div class="admin-card">
        <h2>Admin Login</h2>
        <p>Sign in to manage ESAHub Africa content.</p>

        <?php if ($error_message): ?>
            <div class="form-message error"><?php echo e($error_message); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
