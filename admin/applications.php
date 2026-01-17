<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();
  $id = (int)($_POST["app_id"] ?? 0);
  $action = $_POST["action"] ?? "";
  $notes = trim($_POST["admin_notes"] ?? "");

  $stmt = db()->prepare("SELECT * FROM applications WHERE id=?");
  $stmt->execute([$id]);
  $app = $stmt->fetch();

  if ($app) {
    if ($action === "approve") {
      // mark approved
      db()->prepare("UPDATE applications SET status='approved', admin_notes=? WHERE id=?")->execute([$notes,$id]);

      // create/find student account
      $u = find_user_by_email($app["email"]);
      if (!$u) {
        $tempPass = "Student@" . random_int(1000,9999);
        $hash = password_hash($tempPass, PASSWORD_DEFAULT);

        db()->prepare("INSERT INTO users(full_name,email,phone,password_hash,role) VALUES(?,?,?,?, 'student')")
          ->execute([$app["full_name"], $app["email"], $app["phone"], $hash]);

        // (Optional) show temp pass in admin note
        $notes2 = trim($notes . "\nTemp password: " . $tempPass);
        db()->prepare("UPDATE applications SET admin_notes=? WHERE id=?")->execute([$notes2,$id]);

        $u = find_user_by_email($app["email"]);
      }

      // enroll if intake chosen
      if (!empty($app["intake_id"]) && $u) {
        db()->prepare("INSERT IGNORE INTO enrollments(user_id,intake_id,status) VALUES(?,?,'enrolled')")
          ->execute([(int)$u["id"], (int)$app["intake_id"]]);

        $enrollment_id = db()->lastInsertId();

        // Get course fee
        $fee_stmt = db()->prepare("SELECT c.fee FROM intakes i JOIN courses c ON c.id = i.course_id WHERE i.id = ?");
        $fee_stmt->execute([(int)$app["intake_id"]]);
        $fee = $fee_stmt->fetchColumn();

        if ($fee > 0) {
            db()->prepare("INSERT INTO payments(enrollment_id, amount, status) VALUES(?, ?, 'pending')")->execute([$enrollment_id, $fee]);
        }
      }

      // Notify student
      if ($u) {
        db()->prepare("INSERT INTO notifications(user_id, title, message, link) VALUES(?, ?, ?, ?)")->execute([
          (int)$u["id"],
          'Application Approved',
          'Your application has been approved. Check your enrollments for details.',
          'student/tasks_board'
        ]);
      }

    } elseif ($action === "reject") {
      db()->prepare("UPDATE applications SET status='rejected', admin_notes=? WHERE id=?")->execute([$notes,$id]);

      // Notify student of rejection
      $u = find_user_by_email($app["email"]);
      if ($u) {
        db()->prepare("INSERT INTO notifications(user_id, title, message, link) VALUES(?, ?, ?, ?)")->execute([
          (int)$u["id"],
          'Application Rejected',
          'Your application has been rejected. Please contact us for more information.',
          'student/portal'
        ]);
      }
    }
  }

  redirect("./applications");
}

$apps = db()->query("
  SELECT a.*, c.title AS course_title, i.start_date, i.schedule
  FROM applications a
  JOIN courses c ON c.id=a.course_id
  LEFT JOIN intakes i ON i.id=a.intake_id
  ORDER BY a.created_at DESC
")->fetchAll();
?>

<?php
include './layout/header.php';
?>
  <p><a href="./index">← Back</a></p>
  <h2>Applications</h2>

  <?php foreach ($apps as $a): ?>
    <div style="border:1px solid #ddd; padding:12px; margin:12px 0;">
      <strong><?= e($a["full_name"]) ?></strong> (<?= e($a["email"]) ?>) — <?= e($a["course_title"]) ?><br>
      Intake: <?= e($a["schedule"] ?? "N/A") ?> <?= e($a["start_date"] ?? "") ?><br>
      Status: <strong><?= e($a["status"]) ?></strong><br><br>

      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="app_id" value="<?= (int)$a["id"] ?>">

        <label>Admin notes</label><br>
        <textarea name="admin_notes" rows="3" style="width:100%;"><?= e($a["admin_notes"] ?? "") ?></textarea><br><br>

        <button name="action" value="approve" type="submit">Approve</button>
        <button name="action" value="reject" type="submit">Reject</button>
      </form>
    </div>
  <?php endforeach; ?>

