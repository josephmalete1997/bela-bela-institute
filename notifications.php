<?php
require_once __DIR__ . '/app/bootstrap.php';
if (!is_logged_in()) { redirect('/login.php'); }
$uid = auth_user()['id'];
$stmt = db()->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid'=>$uid]);
$notes = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<main class="section"><div class="container">
  <h2>Notifications</h2>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <h2>Notifications</h2>
    <form id="markAllForm" method="post" action="/api/notifications_mark_all_read.php"><button class="btn">Mark all read</button></form>
  </div>
  <?php foreach($notes as $n): ?>
    <div style="border:1px solid #eef2f7;padding:12px;border-radius:8px;margin-bottom:8px;background:<?= $n['is_read']? '#ffffff':'#f8fafc' ?>;">
      <div style="font-weight:700"><?= e($n['title']) ?> <small style="color:#64748b;float:right;"><?= e($n['created_at']) ?></small></div>
      <div style="margin-top:8px;"><?= e($n['message']) ?></div>
      <div style="margin-top:8px;">
        <?php if (!$n['is_read']): ?>
          <form method="post" action="/api/notifications_mark_read.php" style="display:inline;">
            <input type="hidden" name="id" value="<?= e($n['id']) ?>" />
            <input type="hidden" name="return" value="/notifications.php" />
            <button class="btn">Mark as read</button>
          </form>
        <?php endif; ?>
        <?php if (!empty($n['link'])): ?>
          <a class="btn" href="<?= e($n['link']) ?>">Open</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div></main>
<?php require __DIR__ . '/includes/footer.php';
