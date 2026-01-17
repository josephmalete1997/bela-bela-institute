<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/tasks_model.php";
$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect('tasks.php'); }
$task = tasks_find($id);
if (!$task) { redirect('tasks.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $title = trim($_POST['title'] ?? '');
  $type = $_POST['type'] ?? 'project';
  $description = $_POST['description'] ?? null;
  $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
  $assigned_user = (int)($_POST['assigned_user'] ?? 0) ?: null;
  $status = $_POST['status'] ?? 'backlog';
  $data = ['title'=>$title,'type'=>$type,'description'=>$description,'assigned_user_id'=>$assigned_user,'status'=>$status];
  tasks_update($id, $data);
  redirect('tasks.php');
}
$courses = db()->query("SELECT id,title FROM courses ORDER BY title")->fetchAll();
$users = db()->query("SELECT id,full_name FROM users ORDER BY full_name")->fetchAll();
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2>Edit Task #<?= e($id) ?></h2>
  <form method="post" class="form">
    <label>Title<input name="title" value="<?= e($task['title']) ?>" required></label>
    <label>Course<select name="course_id" required>
      <option value="">-- select course --</option>
      <?php foreach($courses as $c): ?><option value="<?= e($c['id']) ?>" <?= $task['course_id']==$c['id']? 'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?>
    </select></label>
    <label>Type<select name="type"><option value="project" <?= $task['type']=='project' ? 'selected':'' ?>>Project</option><option value="topic" <?= $task['type']=='topic' ? 'selected':'' ?>>Topic</option></select></label>
    <label>Description</label>
    <div id="quill-desc" style="min-height:200px;"><?= $task['description'] ?></div>
    <input type="hidden" name="description" id="description-hidden" />
    <label>Assign to (optional)<select name="assigned_user"><option value="">-- none --</option><?php foreach($users as $u): ?><option value="<?= e($u['id']) ?>" <?= $task['assigned_user_id']==$u['id']? 'selected':'' ?>><?= e($u['full_name']) ?></option><?php endforeach; ?></select></label>
    <label>Status<select name="status"><option value="backlog" <?= $task['status']=='backlog' ? 'selected':'' ?>>Backlog</option><option value="in_review" <?= $task['status']=='in_review' ? 'selected':'' ?>>In Review</option><option value="completed" <?= $task['status']=='completed' ? 'selected':'' ?>>Completed</option></select></label>
    <button class="btn">Save Changes</button>
  </form>
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script>
    var quill = new Quill('#quill-desc', { theme: 'snow' });
    quill.root.innerHTML = <?= json_encode($task['description'] ?? '') ?>;
    document.querySelector('form').addEventListener('submit', function(){
      document.getElementById('description-hidden').value = quill.root.innerHTML;
    });
  </script>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
