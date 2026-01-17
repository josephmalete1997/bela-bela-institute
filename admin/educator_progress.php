<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_any_role(['admin','educator']);

$user = auth_user();
$is_admin = ($user['role'] ?? '') === 'admin';

$sql = "
  SELECT
    u.id AS student_id,
    u.full_name AS student_name,
    u.email AS student_email,
    c.id AS course_id,
    c.title AS course_title,
    COUNT(DISTINCT t.id) AS total_tasks,
    SUM(tp.status = 'completed') AS completed_tasks,
    MAX(tp.updated_at) AS last_update,
    MIN(t.id) AS sample_task_id
  FROM enrollments e
  JOIN intakes i ON i.id = e.intake_id
  JOIN courses c ON c.id = i.course_id
  JOIN users u ON u.id = e.user_id
  LEFT JOIN tasks t ON t.course_id = c.id
  LEFT JOIN task_progress tp ON tp.task_id = t.id AND tp.user_id = u.id
  WHERE e.status = 'enrolled'
";
if (!$is_admin) {
  $sql .= " AND c.id IN (SELECT course_id FROM course_educators WHERE educator_id = :eid)";
}
$sql .= " GROUP BY u.id, c.id ORDER BY c.title, u.full_name";

$stmt = db()->prepare($sql);
$params = $is_admin ? [] : [':eid' => (int)$user['id']];
$stmt->execute($params);
$rows = $stmt->fetchAll();

require __DIR__ . "/layout/header.php";
?>
<div class="admin-card">
  <h2>Student Progress</h2>
  <p>Track student progress by course. Progress is based on completed tasks in each course.</p>
</div>

<div class="admin-card">
  <?php if (!$rows): ?>
    <p>No progress records yet.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Course</th>
          <th>Completed</th>
          <th>Progress</th>
          <th>Risk</th>
          <th>Last Update</th>
          <th>Contact</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <?php
            $total = (int)($row['total_tasks'] ?? 0);
            $done = (int)($row['completed_tasks'] ?? 0);
            $percent = $total > 0 ? round(($done / $total) * 100) : 0;
            $risk = $percent >= 60 ? 'On Track' : 'At Risk';
          ?>
          <tr>
            <td><?= e($row['student_name'] ?? '') ?><br><small style="color:#64748b;"><?= e($row['student_email'] ?? '') ?></small></td>
            <td><?= e($row['course_title'] ?? '-') ?></td>
            <td><?= $done ?> / <?= $total ?></td>
            <td><?= $percent ?>%</td>
            <td><?= $risk ?></td>
            <td><?= e($row['last_update'] ?? '-') ?></td>
            <td>
              <?php if (!empty($row['sample_task_id'])): ?>
                <form method="post" action="../api/task_message.php">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="task_id" value="<?= (int)$row['sample_task_id'] ?>">
                  <input type="hidden" name="submitter_id" value="<?= (int)$row['student_id'] ?>">
                  <input type="hidden" name="recipient_id" value="<?= (int)$row['student_id'] ?>">
                  <textarea name="message" required placeholder="Message student"></textarea>
                  <button class="btn-admin btn-primary" type="submit">Send</button>
                </form>
              <?php else: ?>
                <span style="color:#64748b;">No tasks yet</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . "/layout/footer.php"; ?>
