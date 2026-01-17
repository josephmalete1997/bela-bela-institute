<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$counts = db()->query("
  SELECT
    (SELECT COUNT(*) FROM users WHERE role='student') AS students,
    (SELECT COUNT(*) FROM courses) AS courses,
    (SELECT COUNT(*) FROM intakes) AS intakes,
    (SELECT COUNT(*) FROM applications WHERE status='pending') AS pending_apps,
    (SELECT COUNT(*) FROM applications WHERE status='approved') AS approved_apps,
    (SELECT COUNT(*) FROM enrollments WHERE status='enrolled') AS active_enrollments
")->fetch();

$recentApps = db()->query("
  SELECT a.*, c.title AS course_title
  FROM applications a
  JOIN courses c ON c.id = a.course_id
  ORDER BY a.created_at DESC
  LIMIT 8
")->fetchAll();

$appsByStatus = db()->query("
  SELECT status, COUNT(*) AS total
  FROM applications
  GROUP BY status
")->fetchAll();

$appsByCourse = db()->query("
  SELECT c.title, COUNT(*) AS total
  FROM applications a
  JOIN courses c ON c.id = a.course_id
  GROUP BY c.id
  ORDER BY total DESC
  LIMIT 6
")->fetchAll();

require_once __DIR__ . '/layout/header.php';
?>

<div class="admin-card">
  <h2>Analytics Dashboard</h2>
  <p>Quick overview of learners, applications, and enrollments.</p>
</div>

<div class="admin-grid-3">
  <div class="admin-card stat-card">
    <div class="stat-title">Students</div>
    <div class="stat-value"><?= (int)$counts["students"] ?></div>
  </div>
  <div class="admin-card stat-card">
    <div class="stat-title">Courses</div>
    <div class="stat-value"><?= (int)$counts["courses"] ?></div>
  </div>
  <div class="admin-card stat-card">
    <div class="stat-title">Intakes</div>
    <div class="stat-value"><?= (int)$counts["intakes"] ?></div>
  </div>

  <div class="admin-card stat-card">
    <div class="stat-title">Pending Applications</div>
    <div class="stat-value"><?= (int)$counts["pending_apps"] ?></div>
  </div>
  <div class="admin-card stat-card">
    <div class="stat-title">Approved Applications</div>
    <div class="stat-value"><?= (int)$counts["approved_apps"] ?></div>
  </div>
  <div class="admin-card stat-card">
    <div class="stat-title">Active Enrollments</div>
    <div class="stat-value"><?= (int)$counts["active_enrollments"] ?></div>
  </div>
</div>

<div class="admin-grid-2">
  <div class="admin-card">
    <h3>Applications by Status</h3>
    <ul class="clean-list">
      <?php foreach ($appsByStatus as $row): ?>
        <li>
          <span class="pill"><?= e($row["status"]) ?></span>
          <strong><?= (int)$row["total"] ?></strong>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="admin-card">
    <h3>Top Courses (Applications)</h3>
    <ul class="clean-list">
      <?php foreach ($appsByCourse as $row): ?>
        <li>
          <?= e($row["title"]) ?>
          <strong><?= (int)$row["total"] ?></strong>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<div class="admin-card">
  <h3>Recent Applications</h3>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Course</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recentApps as $a): ?>
        <tr>
          <td><?= e($a["full_name"]) ?></td>
          <td><?= e($a["course_title"]) ?></td>
          <td>
            <?php
              $status = $a["status"];
              $cls = $status === "approved" ? "badge-green" : ($status === "rejected" ? "badge-red" : "badge-blue");
            ?>
            <span class="badge <?= $cls ?>"><?= e($status) ?></span>
          </td>
          <td><?= e($a["created_at"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
