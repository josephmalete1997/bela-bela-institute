<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();

  $id = (int)($_POST["delete_id"] ?? 0);
  if ($id) {
    $stmt = db()->prepare("DELETE FROM intakes WHERE id=?");
    $stmt->execute([$id]);
  }

  redirect("./intakes");
}

$intakes = db()->query("
  SELECT
    i.*,
    c.title AS course_title,
    (SELECT COUNT(*) FROM enrollments e WHERE e.intake_id = i.id AND e.status = 'enrolled') AS enrolled_count
  FROM intakes i
  JOIN courses c ON c.id = i.course_id
  ORDER BY i.start_date DESC, i.id DESC
")->fetchAll();
?>
<?php
include './layout/header.php';
?>
  <p>
    <a href="./">← Back</a> |
    <a href="../public/logout">Logout</a>
  </p>

  <h2>Intakes</h2>
  <p><a href="./intake_edit">+ Add Intake</a></p>

  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>Course</th>
        <th>Start</th>
        <th>End</th>
        <th>Schedule</th>
        <th>Seats</th>
        <th>Enrolled</th>
        <th>Active</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($intakes as $i): ?>
        <?php
          $seats = (int)$i["seats"];
          $enrolled = (int)$i["enrolled_count"];
          $remaining = max(0, $seats - $enrolled);
        ?>
        <tr>
          <td><?= e($i["course_title"]) ?></td>
          <td><?= e($i["start_date"]) ?></td>
          <td><?= e($i["end_date"] ?? "") ?></td>
          <td><?= e($i["schedule"]) ?></td>
          <td><?= $seats ?></td>
          <td><?= $enrolled ?> (<?= $remaining ?> left)</td>
          <td><?= ((int)$i["is_active"] === 1) ? "Yes" : "No" ?></td>
          <td>
            <a href="./intake_edit?id=<?= (int)$i["id"] ?>">Edit</a>

            <form method="post" style="display:inline" onsubmit="return confirm('Delete intake?');">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="delete_id" value="<?= (int)$i["id"] ?>">
              <button type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
