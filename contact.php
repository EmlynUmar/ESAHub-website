<?php
$page_title = 'Contact | ESAHub Africa';
$success_message = '';
$error_message = '';

// Support running from /public or from a project root that points to /public.
$public_root = __DIR__;
if (!is_dir($public_root . '/includes') && is_dir($public_root . '/public/includes')) {
    $public_root = $public_root . '/public';
}

require_once $public_root . '/includes/header.php';
require_once $public_root . '/includes/db.php';

// PHPMailer (manual install optional)
$phpmailer_candidates = [
    $public_root . '/vendor/PHPMailer/src',
    __DIR__ . '/vendor/PHPMailer/src',
    __DIR__ . '/public/vendor/PHPMailer/src',
];

$phpmailer_loaded = false;
foreach ($phpmailer_candidates as $candidate) {
    if (is_file($candidate . '/PHPMailer.php') && is_file($candidate . '/SMTP.php') && is_file($candidate . '/Exception.php')) {
        require_once $candidate . '/Exception.php';
        require_once $candidate . '/PHPMailer.php';
        require_once $candidate . '/SMTP.php';
        $phpmailer_loaded = true;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error_message = 'Invalid form submission. Please refresh the page and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $error_message = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Please provide a valid email address.';
        } else {
            try {
                // Save to database inquiries table
                $stmt = $pdo->prepare('INSERT INTO inquiries (name, email, phone, message, status, created_at) VALUES (?, ?, ?, ?, "unread", NOW())');
                $stmt->execute([$name, $email, $phone, $message]);
                $success_message = 'Thank you for reaching out! We have received your inquiry and will respond shortly.';

                // Optional SMTP email dispatch if PHPMailer is installed
                if ($phpmailer_loaded && SMTP_HOST !== 'mail.yourdomain.com') {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASS;
                        $mail->SMTPSecure = SMTP_ENCRYPTION;
                        $mail->Port = SMTP_PORT;

                        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                        $mail->addAddress(CONTACT_EMAIL, 'ESAHub Africa');
                        $mail->addReplyTo($email, $name);

                        $mail->isHTML(false);
                        $mail->Subject = 'New Contact Form Submission - ESAHub Africa';
                        $mail->Body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}";

                        $mail->send();
                    } catch (Throwable $mailEx) {
                        error_log('Contact form email dispatch error: ' . $mailEx->getMessage());
                    }
                }
            } catch (Throwable $e) {
                error_log('Contact form inquiry save error: ' . $e->getMessage());
                $error_message = 'Unable to submit your message at this time. Please try contacting us directly via phone or email.';
            }
        }
    }
}
?>
<header>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Contact Us</h2>
            <p>We would love to hear from you. Reach out with questions, partnerships, or program inquiries.</p>
        </div>

        <?php if ($success_message): ?>
            <div class="form-message success"><?php echo e($success_message); ?></div>
        <?php elseif ($error_message): ?>
            <div class="form-message error"><?php echo e($error_message); ?></div>
        <?php endif; ?>

        <div class="card-grid">
            <div class="card">
                <h3>Contact Details</h3>
                <p><strong>Address:</strong> <?php echo e(CONTACT_ADDRESS); ?></p>
                <p><strong>Phone:</strong> <?php echo e(CONTACT_PHONE); ?></p>
                <p><strong>Email:</strong> <?php echo e(CONTACT_EMAIL); ?></p>
            </div>
            <div class="card">
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input id="name" name="name" type="text" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="text">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
