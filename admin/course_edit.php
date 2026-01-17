<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
$id = (int)($_GET["id"] ?? 0);
$course = ["title"=>"","slug"=>"","description"=>"","image"=>"","highlights"=>"[]","is_active"=>1];

if ($id) {
  $stmt = db()->prepare("SELECT * FROM courses WHERE id=?");
  $stmt->execute([$id]);
  $course = $stmt->fetch() ?: $course;
  $course["highlights"] = $course["highlights"] ? json_encode(json_decode($course["highlights"], true), JSON_PRETTY_PRINT) : "[]";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();

  $title = trim($_POST["title"] ?? "");
  $slug  = slugify($_POST["slug"] ?? $title);
  $desc  = trim($_POST["description"] ?? "");
  $image = trim($_POST["image"] ?? "");
  $active = isset($_POST["is_active"]) ? 1 : 0;
  $highlightsRaw = trim($_POST["highlights"] ?? "[]");

  $highlights = json_decode($highlightsRaw, true);
  if (!is_array($highlights)) $highlights = [];

  if ($id) {
    $stmt = db()->prepare("UPDATE courses SET title=?, slug=?, description=?, image=?, highlights=?, is_active=? WHERE id=?");
    $stmt->execute([$title,$slug,$desc,$image,json_encode($highlights),$active,$id]);
  } else {
    $stmt = db()->prepare("INSERT INTO courses(title,slug,description,image,highlights,is_active) VALUES(?,?,?,?,?,?)");
    $stmt->execute([$title,$slug,$desc,$image,json_encode($highlights),$active]);
  }

  redirect("./courses");
}
?>

<?php
include './layout/header.php';
?>
  <p><a href="./courses">← Back</a></p>

  <h2><?= $id ? "Edit" : "Add" ?> Course</h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Title</label><br>
    <input name="title" value="<?= e($course["title"] ?? "") ?>" required><br><br>

    <label>Slug (optional)</label><br>
    <input name="slug" value="<?= e($course["slug"] ?? "") ?>"><br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" required><?= e($course["description"] ?? "") ?></textarea><br><br>

    <label>Image path</label><br>
    <input name="image" value="<?= e($course["image"] ?? "") ?>"><br><br>

    <label>Highlights (JSON array)</label><br>
    <textarea name="highlights" rows="6"><?= e($course["highlights"] ?? "[]") ?></textarea><br><br>

    <label>
      <input type="checkbox" name="is_active" <?= ((int)($course["is_active"] ?? 1) === 1) ? "checked" : "" ?>>
      Active
    </label><br><br>

    <button type="submit">Save</button>
  </form>

