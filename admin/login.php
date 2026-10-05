<?php
$page_title = 'Admin Login | ESAHub Africa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

if (is_admin_logged_in()) {
    header('Location: ' . base_url('admin/dashboard.php'));
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    // Check lockout
    $attempts = (int)($_SESSION['login_attempts'] ?? 0);
    $lockoutTime = (int)($_SESSION['login_lockout_time'] ?? 0);
    $lockoutDuration = 900; // 15 minutes

    if ($attempts >= 5 && (time() - $lockoutTime) < $lockoutDuration) {
        $minutesLeft = ceil(($lockoutDuration - (time() - $lockoutTime)) / 60);
        $error_message = "Too many failed login attempts. Please wait {$minutesLeft} minute(s) before trying again.";
    } elseif (!verify_csrf($token)) {
        $error_message = 'Invalid request. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Reset lockout and regenerate session ID to prevent fixation
            unset($_SESSION['login_attempts'], $_SESSION['login_lockout_time']);
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: ' . base_url('admin/dashboard.php'));
            exit;
        }

        // Record failed attempt
        $_SESSION['login_attempts'] = $attempts + 1;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_lockout_time'] = time();
            $error_message = 'Too many failed login attempts. Your account has been temporarily locked for 15 minutes.';
        } else {
            $remaining = 5 - $_SESSION['login_attempts'];
            $error_message = "Invalid login credentials. {$remaining} attempt(s) remaining.";
        }
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
