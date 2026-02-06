<?php
$page_title = 'ESAHub Africa | Empowering Education & Skills';
require_once __DIR__ . '/includes/header.php';
?>
<header>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
</header>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <div class="eyebrow">Education • Innovation • Opportunity</div>
            <h1>Empowering African communities through education, technology, and bold ideas.</h1>
            <p>ESAHub Africa equips youth, women, and underserved communities with future-ready skills, mentorship, and access to opportunity.</p>
            <div class="cta-group">
                <a class="btn btn-primary" href="<?php echo e(base_url('programs.php')); ?>">Explore Programs</a>
                <a class="btn btn-outline" href="<?php echo e(base_url('contact.php')); ?>">Partner With Us</a>
            </div>
            <div class="hero-meta">
                <div>
                    <strong>5k+</strong>
                    <span>Learners Reached</span>
                </div>
                <div>
                    <strong>20+</strong>
                    <span>Programs Delivered</span>
                </div>
                <div>
                    <strong>12</strong>
                    <span>Community Partners</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="visual-card primary">
                <span>Her Digital Canvas</span>
                <strong>Creative Tech for Women</strong>
            </div>
            <div class="visual-card accent">
                <span>Innovation Lab</span>
                <strong>Build • Test • Launch</strong>
            </div>
            <div class="visual-image">
                <img src="<?php echo e(base_url('assets/images/hero.svg')); ?>" alt="ESAHub Africa community">
            </div>
            <div class="visual-stat">
                <strong>87%</strong>
                <span>skills confidence gain</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Our Mission</h2>
            <p>We are building a thriving ecosystem for digital skills, entrepreneurship, and community development across Africa.</p>
        </div>
        <div class="card-grid">
            <div class="card">
                <h3>Education Access</h3>
                <p>We deliver practical learning programs that expand access to digital literacy and professional growth.</p>
            </div>
            <div class="card">
                <h3>Innovation Labs</h3>
                <p>Our labs empower young innovators to build solutions that solve community challenges.</p>
            </div>
            <div class="card">
                <h3>Community Impact</h3>
                <p>We collaborate with partners to launch initiatives that uplift families and local economies.</p>
            </div>
        </div>
    </div>
</section>

<section class="section highlight">
    <div class="container">
        <div class="impact-split">
            <div>
                <div class="section-title left">
                    <h2>Learning that feels alive</h2>
                    <p>From digital literacy to entrepreneurship, we run hands-on cohorts with mentors, real-world projects, and community showcases.</p>
                </div>
                <div class="chip-grid">
                    <span class="chip">Mentor-led cohorts</span>
                    <span class="chip">Project showcases</span>
                    <span class="chip">Scholarship access</span>
                    <span class="chip">Community hubs</span>
                </div>
                <div class="cta-group">
                    <a class="btn btn-primary" href="<?php echo e(base_url('programs.php')); ?>">See Our Programs</a>
                    <a class="btn btn-outline dark" href="<?php echo e(base_url('contact.php')); ?>">Become a Partner</a>
                </div>
            </div>
            <div class="impact-stack">
                <div class="stack-card">
                    <h3>Community Impact</h3>
                    <p>We collaborate with partners to launch initiatives that uplift families and local economies.</p>
                </div>
                <div class="stack-card highlight-card">
                    <h3>Innovation Labs</h3>
                    <p>Our labs empower young innovators to build solutions that solve community challenges.</p>
                </div>
                <div class="stack-card">
                    <h3>Education Access</h3>
                    <p>Practical learning programs that expand access to digital literacy and professional growth.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
