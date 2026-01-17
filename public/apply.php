<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
// fetch courses + active intakes
$courses = db()->query("SELECT id,title FROM courses WHERE is_active=1 ORDER BY title")->fetchAll();
$intakes = db()->query("
  SELECT
        i.id, i.course_id, i.start_date, i.schedule, i.seats,
        c.title AS course_title,
        (SELECT COUNT(*) FROM enrollments e WHERE e.intake_id=i.id AND e.status='enrolled') AS enrolled_count
    FROM intakes i
    JOIN courses c ON c.id=i.course_id
    WHERE i.is_active=1
    ORDER BY i.start_date DESC

")->fetchAll();

$ok = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    $full = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $course_id = (int)($_POST["course_id"] ?? 0);
    $intake_id = (int)($_POST["intake_id"] ?? 0);
    $motivation = trim($_POST["motivation"] ?? "");

    if (!$full || !$email || !$course_id) {
        $error = "Please complete required fields.";
    } else {
        $stmt = db()->prepare("
      INSERT INTO applications(course_id,intake_id,full_name,email,phone,motivation)
      VALUES(?,?,?,?,?,?)
    ");
        $stmt->execute([$course_id, $intake_id ?: null, $full, $email, $phone, $motivation]);

        // Notify admins
        $admin_ids = db()->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($admin_ids as $admin_id) {
            db()->prepare("INSERT INTO notifications(user_id, title, message, link) VALUES(?, ?, ?, ?)")->execute([
                $admin_id,
                'New Application Received',
                "Application from $full for course ID $course_id",
                'admin/applications'
            ]);
        }

        $ok = "Application submitted. We will contact you soon.";
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Apply</title>
</head>

<body>
    <h2>Apply</h2>
    <?php if ($ok): ?><p style="color:green;"><?= e($ok) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <label>Full Name *</label><br><input name="full_name" required><br><br>
        <label>Email *</label><br><input name="email" type="email" required><br><br>
        <label>Phone</label><br><input name="phone"><br><br>

        <label>Course *</label><br>
        <select name="course_id" required>
            <option value="">Select course</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= (int)$c["id"] ?>"><?= e($c["title"]) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Intake (optional)</label><br>
        <select name="intake_id">
            <option value="">Select intake</option>
            <?php foreach ($intakes as $i): ?>
                <option value="<?= (int)$i["id"] ?>">
                    <?= e($i["course_title"]) ?> — <?= e($i["schedule"]) ?> — <?= e($i["start_date"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Motivation</label><br>
        <textarea name="motivation" rows="4"></textarea><br><br>

        <button type="submit">Submit Application</button>
    </form>
</body>

</html>