<footer>
    <div class="container footer-grid">
        <div>
            <h4>ESAHub Africa</h4>
            <p>Empowering communities with education, skills, and innovation across Africa.</p>
        </div>
        <div>
            <h4>Contact</h4>
            <p><?= e(CONTACT_ADDRESS); ?></p>
            <p>Phone: <?= e(CONTACT_PHONE); ?></p>
            <p>Email: <a href="mailto:<?= e(CONTACT_EMAIL); ?>"><?= e(CONTACT_EMAIL); ?></a></p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <p><a href="<?= e(url('about.php')) ?>">About</a></p>
            <p><a href="<?= e(url('programs.php')) ?>">Programs</a></p>
            <p><a href="<?= e(url('impact.php')) ?>">Impact</a></p>
            <p><a href="<?= e(url('blog/index.php')) ?>">Blog</a></p>
        </div>
    </div>
    <div class="container" style="padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.12); margin-top: 1.5rem; text-align: center; font-size: 0.9rem; opacity: 0.9;">
        Built by <a href="https://leenco.com.ng" target="_blank" rel="noopener" style="color: #fff; text-decoration: underline;">Leen-Co Tech Ltd</a>
    </div>
</footer>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
