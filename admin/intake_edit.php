<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';

$id = (int)($_GET["id"] ?? 0);

// Load courses for dropdown
$courses = db()->query("SELECT id, title FROM courses ORDER BY title ASC")->fetchAll();

// Default intake values
$intake = [
  "course_id" => "",
  "start_date" => "",
  "end_date" => "",
  "schedule" => "Weekends",
  "seats" => 20,
  "is_active" => 1,
];

if ($id) {
  $stmt = db()->prepare("SELECT * FROM intakes WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  if ($row) $intake = $row;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();

  $course_id  = (int)($_POST["course_id"] ?? 0);
  $start_date = trim($_POST["start_date"] ?? "");
  $end_date   = trim($_POST["end_date"] ?? "");
  $schedule   = trim($_POST["schedule"] ?? "");
  $seats      = (int)($_POST["seats"] ?? 0);
  $is_active  = isset($_POST["is_active"]) ? 1 : 0;

  if (!$course_id || !$start_date || !$schedule || $seats < 1) {
    $error = "Please fill required fields. Seats must be at least 1.";
  } else {
    $end_date = $end_date ?: null;

    if ($id) {
      $stmt = db()->prepare("
        UPDATE intakes
        SET course_id=?, start_date=?, end_date=?, schedule=?, seats=?, is_active=?
        WHERE id=?
      ");
      $stmt->execute([$course_id, $start_date, $end_date, $schedule, $seats, $is_active, $id]);
    } else {
      $stmt = db()->prepare("
        INSERT INTO intakes(course_id, start_date, end_date, schedule, seats, is_active)
        VALUES(?,?,?,?,?,?)
      ");
      $stmt->execute([$course_id, $start_date, $end_date, $schedule, $seats, $is_active]);
    }

    redirect("./intakes");
  }
}
?>
<?php
include './layout/header.php';
?>
  <p><a href="./intakes">← Back</a></p>
  <h2><?= $id ? "Edit" : "Add" ?> Intake</h2>

  <?php if ($error): ?>
    <p style="color:red;"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Course *</label><br>
    <select name="course_id" required>
      <option value="">Select course</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= (int)$c["id"] ?>"
          <?= ((int)$intake["course_id"] === (int)$c["id"]) ? "selected" : "" ?>>
          <?= e($c["title"]) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br><br>

    <label>Start Date *</label><br>
    <input type="date" name="start_date" value="<?= e($intake["start_date"] ?? "") ?>" required>
    <br><br>

    <label>End Date (optional)</label><br>
    <input type="date" name="end_date" value="<?= e($intake["end_date"] ?? "") ?>">
    <br><br>

    <label>Schedule *</label><br>
    <select name="schedule" required>
      <?php
        $options = ["Weekends", "Evenings", "Weekends & Evenings", "Weekdays"];
        foreach ($options as $opt):
      ?>
        <option value="<?= e($opt) ?>" <?= ($intake["schedule"] ?? "") === $opt ? "selected" : "" ?>>
          <?= e($opt) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br><br>

    <label>Seats *</label><br>
    <input type="number" min="1" name="seats" value="<?= (int)($intake["seats"] ?? 20) ?>" required>
    <br><br>

    <label>
      <input type="checkbox" name="is_active" <?= ((int)($intake["is_active"] ?? 1) === 1) ? "checked" : "" ?>>
      Active
    </label>
    <br><br>

    <button type="submit">Save Intake</button>
  </form>
