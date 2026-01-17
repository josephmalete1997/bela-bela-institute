<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_any_role(['admin','educator']);
require_once __DIR__ . "/../includes/tasks_model.php";

$role = auth_user()['role'] ?? 'admin';
$is_admin = $role === 'admin';
$sql = "
  SELECT
    tp.task_id,
    tp.user_id,
    tp.status,
    tp.updated_at,
    t.title,
    t.type,
    u.full_name,
    u.email,
    (SELECT COUNT(*) FROM task_reviews tr WHERE tr.task_id = tp.task_id AND tr.submitter_id = tp.user_id AND tr.is_competent = 1) AS competent_count,
    (SELECT COUNT(*) FROM task_reviews tr WHERE tr.task_id = tp.task_id AND tr.submitter_id = tp.user_id AND tr.is_competent = 0) AS not_competent_count
  FROM task_progress tp
  JOIN tasks t ON t.id = tp.task_id
  JOIN users u ON u.id = tp.user_id
  WHERE tp.status = 'in_review' AND t.type = 'project'
";
if (!$is_admin) {
  $sql .= " AND t.course_id IN (SELECT course_id FROM course_educators WHERE educator_id = :eid)";
}
$sql .= " ORDER BY tp.updated_at DESC";
$stmt = db()->prepare($sql);
$params = $is_admin ? [] : [':eid' => (int)auth_user()['id']];
$stmt->execute($params);
$rows = $stmt->fetchAll();

require __DIR__ . "/layout/header.php";
?>
<div class="admin-card">
  <h2>Reviews Queue</h2>
  <p>Projects awaiting review. Each submission requires 3 competent reviews to complete.</p>
</div>

<div class="admin-card">
  <?php if (!$rows): ?>
    <p>No projects awaiting review.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Project</th>
          <th>Student</th>
          <th>Competent</th>
          <th>Not Competent</th>
          <th>Updated</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= e($row['title'] ?? '') ?></td>
            <td><?= e($row['full_name'] ?? '') ?> <small style="color:#64748b;">(<?= e($row['email'] ?? '') ?>)</small></td>
            <td><?= (int)$row['competent_count'] ?></td>
            <td><?= (int)$row['not_competent_count'] ?></td>
            <td><?= e($row['updated_at'] ?? '') ?></td>
            <td><a class="btn-admin btn-primary" href="review.php?id=<?= (int)$row['task_id'] ?>&submitter_id=<?= (int)$row['user_id'] ?>">Review</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . "/layout/footer.php"; ?>
