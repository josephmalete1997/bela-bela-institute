<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
$payments = db()->query("
  SELECT p.*, e.user_id, u.full_name, c.title AS course_title, i.start_date
  FROM payments p
  JOIN enrollments e ON e.id = p.enrollment_id
  JOIN users u ON u.id = e.user_id
  JOIN intakes i ON i.id = e.intake_id
  JOIN courses c ON c.id = i.course_id
  ORDER BY p.created_at DESC
")->fetchAll();
?>

<?php
include './layout/header.php';
?>
  <p><a href="./index">← Back</a></p>
  <h2>Payments</h2>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Method</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$payments): ?>
        <tr>
          <td colspan="6" style="text-align:center;">No payments found.</td>
        </tr>
      <?php endif; ?>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td><?= e($p["full_name"]) ?></td>
          <td><?= e($p["course_title"]) ?> (<?= e($p["start_date"]) ?>)</td>
          <td>R<?= number_format($p["amount"], 2) ?></td>
          <td>
            <?php
            $status = $p["status"];
            $cls = $status === "paid" ? "badge-green" : ($status === "failed" ? "badge-red" : "badge-yellow");
            ?>
            <span class="badge <?= $cls ?>"><?= e($status) ?></span>
          </td>
          <td><?= e($p["payment_method"] ?? "—") ?></td>
          <td><?= e($p["created_at"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php require_once __DIR__ . '/layout/footer.php'; ?>