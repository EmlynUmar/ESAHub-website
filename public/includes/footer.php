<footer>
    <div class="container footer-grid">
        <div>
            <h4>ESAHub Africa</h4>
            <p>Empowering communities with education, skills, and innovation across Africa.</p>
        </div>
        <div>
            <h4>Contact</h4>
            <p><?php echo e(CONTACT_ADDRESS); ?></p>
            <p>Phone: <?php echo e(CONTACT_PHONE); ?></p>
            <p>Email: <a href="mailto:<?php echo e(CONTACT_EMAIL); ?>"><?php echo e(CONTACT_EMAIL); ?></a></p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <p><a href="<?php echo e(base_url('about.php')); ?>">About</a></p>
            <p><a href="<?php echo e(base_url('programs.php')); ?>">Programs</a></p>
            <p><a href="<?php echo e(base_url('impact.php')); ?>">Impact</a></p>
            <p><a href="<?php echo e(base_url('blog/index.php')); ?>">Blog</a></p>
        </div>
    </div>
</footer>
<script src="<?php echo e(base_url('assets/js/main.js')); ?>"></script>
</body>
</html>
