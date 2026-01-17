<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');

$requests = db()->query("
  SELECT r.*, u.full_name AS educator_name, t.title AS task_title
  FROM task_edit_requests r
  JOIN users u ON u.id = r.educator_id
  JOIN tasks t ON t.id = r.task_id
  WHERE r.request_status = 'pending'
  ORDER BY r.created_at DESC
")->fetchAll();

require __DIR__ . "/layout/header.php";
?>
<div class="admin-card">
  <h2>Edit Requests</h2>
  <p>Approve or deny educator edit requests before changes go live.</p>
</div>

<div class="admin-card">
  <?php if (!$requests): ?>
    <p>No pending requests.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Task</th>
          <th>Educator</th>
          <th>Requested</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?= e($r['task_title'] ?? '') ?></td>
            <td><?= e($r['educator_name'] ?? '') ?></td>
            <td><?= e($r['created_at'] ?? '') ?></td>
            <td>
              <form method="post" action="api/approve_task_edit.php" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="action" value="approve">
                <button class="btn-admin btn-primary" type="submit">Approve</button>
              </form>
              <form method="post" action="api/approve_task_edit.php" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="action" value="deny">
                <button class="btn-admin btn-secondary" type="submit">Deny</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . "/layout/footer.php"; ?>
