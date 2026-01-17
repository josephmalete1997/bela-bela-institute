<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_any_role(['admin','educator']);
$role = auth_user()['role'] ?? 'admin';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $title = trim($_POST['title'] ?? '');
  $type = $_POST['type'] ?? 'project';
  $description = $_POST['description'] ?? null;
  $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
  $status = $_POST['status'] ?? 'backlog';
  $url = trim($_POST['url'] ?? '');
  if ($role === 'educator' && $course_id) {
    require_once __DIR__ . "/../includes/tasks_model.php";
    if (!tasks_is_course_educator((int)$course_id, (int)auth_user()['id'])) {
      http_response_code(403);
      exit('Forbidden');
    }
  }
  $data = ['title'=>$title,'type'=>$type,'description'=>$description,'course_id'=>$course_id,'submitter_id'=>null,'assigned_user_id'=>null,'status'=>$status,'position'=>0,'url'=>$url];
  require_once __DIR__ . "/../includes/tasks_model.php";
  $id = tasks_create($data);
  if ($role === 'educator') {
    redirect('task_create.php');
  }
  redirect('task_list.php');
}
if ($role === 'educator') {
  $stmt = db()->prepare("SELECT c.id, c.title FROM courses c JOIN course_educators ce ON ce.course_id = c.id WHERE ce.educator_id = :eid ORDER BY c.title");
  $stmt->execute([':eid' => (int)auth_user()['id']]);
  $courses = $stmt->fetchAll();
} else {
  $courses = db()->query("SELECT id,title FROM courses ORDER BY title")->fetchAll();
}
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2>Create Course Task</h2>
  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Title<input name="title" required></label>
    <label>Course<select name="course_id" required>
      <option value="">-- select course --</option>
      <?php foreach($courses as $c): ?><option value="<?= e($c['id']) ?>"><?= e($c['title']) ?></option><?php endforeach; ?>
    </select></label>
    <label>Type<select name="type" id="task-type"><option value="project">Project</option><option value="topic">Topic</option></select></label>
    <label id="url-label" style="display:none;">URL<input name="url" placeholder="https://..."></label>
    <label>Description</label>
    <div id="quill-desc" style="min-height:200px;"></div>
    <input type="hidden" name="description" id="description-hidden" />
    <label>Status<select name="status"><option value="backlog">Backlog</option><option value="in_review">In Review</option><option value="completed">Completed</option></select></label>
    <p style="margin-top:12px;color:#64748b;font-size:0.9rem;"><strong>Note:</strong> This task will be visible to all learners enrolled in the selected course.</p>
    <button class="btn">Create Task</button>
  </form>
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script>
    var quill = new Quill('#quill-desc', { theme: 'snow' });
    document.querySelector('form').addEventListener('submit', function(){
      document.getElementById('description-hidden').value = quill.root.innerHTML;
    });
    document.getElementById('task-type').addEventListener('change', function(){
      document.getElementById('url-label').style.display = this.value === 'project' ? 'block' : 'none';
    });
  </script>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
