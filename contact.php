<?php
$page_title = 'Contact | ESAHub Africa';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error_message = 'Invalid form submission. Please try again.';
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
            $subject = 'New Contact Form Submission - ESAHub Africa';
            $body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}";
            $headers = "From: {$name} <{$email}>";

            if (mail(CONTACT_EMAIL, $subject, $body, $headers)) {
                $success_message = 'Thank you for reaching out. We will respond shortly.';
            } else {
                $error_message = 'Unable to send your message at this time. Please try again later.';
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
