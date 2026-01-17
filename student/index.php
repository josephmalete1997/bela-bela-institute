<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>
<?php require_role("student"); ?>

<?php
header('Location: /student/portal.php'); exit;
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Student Dashboard</title></head>
<body>
  <p>Welcome, <strong><?= e($u["full_name"]) ?></strong> | <a href="../public/logout.php">Logout</a></p>

  <h2>Your Enrollments</h2>
  <?php if (!$rows): ?>
    <p>No enrollments yet.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($rows as $r): ?>
        <li><?= e($r["title"]) ?> — <?= e($r["schedule"]) ?> — <?= e($r["start_date"]) ?> (<?= e($r["status"]) ?>)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h2>Available Intakes</h2>
  <ul>
    <?php foreach ($available as $a): ?>
      <li>
        <?= e($a["title"]) ?> — <?= e($a["schedule"]) ?> — <?= e($a["start_date"]) ?>
        | <a href="../public/apply">Apply</a>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>
