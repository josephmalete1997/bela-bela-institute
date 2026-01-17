<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();
  $id = (int)($_POST["delete_id"] ?? 0);
  if ($id) {
    $stmt = db()->prepare("DELETE FROM courses WHERE id=?");
    $stmt->execute([$id]);
  }
  redirect("./courses");
}

$courses = db()->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();
?>

<?php
include './layout/header.php';
?>
  <p><a href="./">← Back</a></p>
  <h2>Courses</h2>
  <p><a href="./course_edit">+ Add Course</a></p>

  <table border="1" cellpadding="8">
    <tr><th>Title</th><th>Active</th><th>Actions</th></tr>
    <?php foreach ($courses as $c): ?>
      <tr>
        <td><?= e($c["title"]) ?></td>
        <td><?= (int)$c["is_active"] ? "Yes" : "No" ?></td>
        <td>
          <a href="./course_edit?id=<?= (int)$c["id"] ?>">Edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete course?')">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="delete_id" value="<?= (int)$c["id"] ?>">
            <button type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>