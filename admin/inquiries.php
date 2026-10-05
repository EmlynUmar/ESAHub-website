<?php
$page_title = 'Contact Inquiries | ESAHub Admin';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    if ($action === 'mark_read' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE inquiries SET status = "read" WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: ' . base_url('admin/inquiries.php'));
        exit;
    } elseif ($action === 'mark_unread' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE inquiries SET status = "unread" WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: ' . base_url('admin/inquiries.php'));
        exit;
    } elseif ($action === 'delete' && $id > 0) {
        $stmt = $pdo->prepare('DELETE FROM inquiries WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: ' . base_url('admin/inquiries.php'));
        exit;
    }
}

$inquiries = $pdo->query('SELECT * FROM inquiries ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$unreadCount = (int) $pdo->query('SELECT COUNT(*) FROM inquiries WHERE status = "unread"')->fetchColumn();
?>
<header>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
</header>

<section class="section">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Communication Center</p>
                <h1>Inquiries & Messages</h1>
                <p class="muted">Review contact form submissions, hall bookings, and program partnership requests.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-outline" href="<?= e(base_url('admin/dashboard.php')) ?>">Back to Dashboard</a>
            </div>
        </div>

        <div class="stats-grid" style="margin-bottom:1.5rem;">
            <div class="metric-card">
                <span>Total Inquiries</span>
                <strong><?= count($inquiries) ?></strong>
                <small><?= $unreadCount ?> unread</small>
            </div>
            <div class="metric-card">
                <span>Unread Messages</span>
                <strong><?= $unreadCount ?></strong>
                <small>Requires attention</small>
            </div>
        </div>

        <?php if (empty($inquiries)): ?>
            <div class="card">
                <p class="muted">No messages received yet. Inquiries submitted via the Contact page will appear here.</p>
            </div>
        <?php else: ?>
            <div class="card" style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Sender</th>
                            <th>Contact</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr style="<?= $inq['status'] === 'unread' ? 'background:rgba(15, 181, 200, 0.05);font-weight:600;' : '' ?>">
                                <td>
                                    <span class="badge <?= $inq['status'] === 'unread' ? 'draft' : 'published' ?>">
                                        <?= e(ucfirst($inq['status'])) ?>
                                    </span>
                                </td>
                                <td><?= e($inq['name']) ?></td>
                                <td>
                                    <div><a href="mailto:<?= e($inq['email']) ?>"><?= e($inq['email']) ?></a></div>
                                    <?php if (!empty($inq['phone'])): ?>
                                        <div style="font-size:0.85rem;color:#666;"><a href="tel:<?= e($inq['phone']) ?>"><?= e($inq['phone']) ?></a></div>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width:320px;">
                                    <div style="white-space:pre-wrap;font-size:0.9rem;font-weight:normal;"><?= nl2br(e($inq['message'])) ?></div>
                                </td>
                                <td style="white-space:nowrap;font-size:0.85rem;"><?= e(date('M j, Y g:ia', strtotime($inq['created_at']))) ?></td>
                                <td style="white-space:nowrap;">
                                    <?php if ($inq['status'] === 'unread'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$inq['id'] ?>">
                                            <input type="hidden" name="action" value="mark_read">
                                            <button type="submit" class="btn btn-small btn-outline" style="margin-right:0.25rem;">Mark Read</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$inq['id'] ?>">
                                            <input type="hidden" name="action" value="mark_unread">
                                            <button type="submit" class="btn btn-small" style="margin-right:0.25rem;">Mark Unread</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this inquiry?');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$inq['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" style="background:none;border:none;color:#c84e3a;font-weight:600;cursor:pointer;padding:0.25rem;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
