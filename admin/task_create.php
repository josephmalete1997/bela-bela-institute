<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $title = trim($_POST['title'] ?? '');
  $type = $_POST['type'] ?? 'project';
  $description = $_POST['description'] ?? null;
  $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
  $assigned_user = (int)($_POST['assigned_user'] ?? 0) ?: null;
  $status = $_POST['status'] ?? 'backlog';
  $data = ['title'=>$title,'type'=>$type,'description'=>$description,'course_id'=>$course_id,'submitter_id'=>null,'assigned_user_id'=>$assigned_user,'status'=>$status,'position'=>0];
  require_once __DIR__ . "/../includes/tasks_model.php";
  $id = tasks_create($data);
  redirect('tasks.php');
}
$courses = db()->query("SELECT id,title FROM courses ORDER BY title")->fetchAll();
$users = db()->query("SELECT id,full_name FROM users ORDER BY full_name")->fetchAll();
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2>Create Course Task</h2>
  <form method="post" class="form">
    <label>Title<input name="title" required></label>
    <label>Course<select name="course_id" required>
      <option value="">-- select course --</option>
      <?php foreach($courses as $c): ?><option value="<?= e($c['id']) ?>"><?= e($c['title']) ?></option><?php endforeach; ?>
    </select></label>
    <label>Type<select name="type"><option value="project">Project</option><option value="topic">Topic</option></select></label>
    <label>Description</label>
    <div id="quill-desc" style="min-height:200px;"></div>
    <input type="hidden" name="description" id="description-hidden" />
    <label>Assign to (optional)<select name="assigned_user"><option value="">-- none --</option><?php foreach($users as $u): ?><option value="<?= e($u['id']) ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></label>
    <label>Status<select name="status"><option value="backlog">Backlog</option><option value="in_review">In Review</option><option value="completed">Completed</option></select></label>
    <button class="btn">Create Task</button>
  </form>
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script>
    var quill = new Quill('#quill-desc', { theme: 'snow' });
    document.querySelector('form').addEventListener('submit', function(){
      document.getElementById('description-hidden').value = quill.root.innerHTML;
    });
  </script>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
