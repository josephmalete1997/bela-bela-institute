<?php

declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

$id = (int)($_GET["id"] ?? 0);
if (!$id) redirect("/admin/students.php");

$stmt = db()->prepare("SELECT * FROM users WHERE id=? AND role='student'");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) redirect("/admin/students.php");

// enrollments
$enroll = db()->prepare("
  SELECT e.status, e.created_at,
         i.start_date, i.end_date, i.schedule,
         c.title AS course_title
  FROM enrollments e
  JOIN intakes i ON i.id = e.intake_id
  JOIN courses c ON c.id = i.course_id
  WHERE e.user_id = ?
  ORDER BY i.start_date DESC
");
$enroll->execute([$id]);
$enrollments = $enroll->fetchAll();

// applications (match by email)
$app = db()->prepare("
  SELECT a.*, c.title AS course_title
  FROM applications a
  JOIN courses c ON c.id=a.course_id
  WHERE a.email = ?
  ORDER BY a.created_at DESC
");
$app->execute([(string)$student["email"]]);
$applications = $app->fetchAll();

require_once __DIR__ . '/layout/header.php';
?>

<div class="admin-card">
    <h2>Student Profile</h2>
    <p><a href="./students">← Back to Students</a></p>
    <img
        src="../public/<?= e($student["avatar"] ?? "assets/avatar-placeholder.png") ?>"
        width="140"
        style="border-radius:50%; border:1px solid #e5e7eb; margin-bottom:1rem;"
        alt="Student image">

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
        <div>
            <strong>Name:</strong> <?= e($student["full_name"]) ?><br>
            <strong>Email:</strong> <?= e($student["email"]) ?><br>
            <strong>Phone:</strong> <?= e($student["phone"] ?? "—") ?><br>
        </div>
        <div>
            <strong>Status:</strong>
            <?php if (($student["status"] ?? "active") === "active"): ?>
                <span class="badge badge-green">Active</span>
            <?php else: ?>
                <span class="badge badge-red">Blocked</span>
            <?php endif; ?>
            <br>
            <strong>Joined:</strong> <?= e($student["created_at"]) ?><br>
        </div>
    </div>
</div>

<div class="admin-card">
    <h3>Enrollments</h3>
    <?php if (!$enrollments): ?>
        <p>No enrollments.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Schedule</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $eRow): ?>
                    <tr>
                        <td><?= e($eRow["course_title"]) ?></td>
                        <td><?= e($eRow["schedule"]) ?></td>
                        <td><?= e($eRow["start_date"]) ?></td>
                        <td><?= e($eRow["end_date"] ?? "—") ?></td>
                        <td><?= e($eRow["status"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="admin-card">
    <h3>Applications</h3>
    <?php if (!$applications): ?>
        <p>No applications found for this email.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $a): ?>
                    <?php
                    $status = $a["status"];
                    $cls = $status === "approved" ? "badge-green" : ($status === "rejected" ? "badge-red" : "badge-blue");
                    ?>
                    <tr>
                        <td><?= e($a["course_title"]) ?></td>
                        <td><span class="badge <?= $cls ?>"><?= e($status) ?></span></td>
                        <td><?= e($a["created_at"]) ?></td>
                        <td><?= e($a["admin_notes"] ?? "—") ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>