<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_any_role(['admin','educator']);
require_once __DIR__ . "/../includes/tasks_model.php";
$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect('task_list.php'); }
$task = tasks_find($id);
if (!$task) { redirect('task_list.php'); }
$user = auth_user();
$is_admin = ($user['role'] ?? '') === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  if ($is_admin) {
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? 'project';
    $description = $_POST['description'] ?? null;
    $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
    $status = $_POST['status'] ?? 'backlog';
    $url = trim($_POST['url'] ?? '');
    $data = ['title'=>$title,'type'=>$type,'description'=>$description,'assigned_user_id'=>null,'status'=>$status,'url'=>$url];
    tasks_update($id, $data);
    redirect('task_list.php');
  } else {
    require_once __DIR__ . "/../includes/tasks_model.php";
    $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
    if ($course_id && !tasks_is_course_educator($course_id, (int)$user['id'])) {
      http_response_code(403);
      exit('Forbidden');
    }
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? 'project';
    $description = $_POST['description'] ?? null;
    $status = $_POST['status'] ?? 'backlog';
    $url = trim($_POST['url'] ?? '');
    $stmt = db()->prepare("INSERT INTO task_edit_requests (task_id, educator_id, title, type, description, course_id, status, url) VALUES (:tid,:eid,:title,:type,:desc,:cid,:status,:url)");
    $stmt->execute([
      ':tid'=>$id,
      ':eid'=>(int)$user['id'],
      ':title'=>$title,
      ':type'=>$type,
      ':desc'=>$description,
      ':cid'=>$course_id,
      ':status'=>$status,
      ':url'=>$url,
    ]);
    redirect('task_list.php');
  }
}
if ($is_admin) {
  $courses = db()->query("SELECT id,title FROM courses ORDER BY title")->fetchAll();
} else {
  $stmt = db()->prepare("SELECT c.id, c.title FROM courses c JOIN course_educators ce ON ce.course_id = c.id WHERE ce.educator_id = :eid ORDER BY c.title");
  $stmt->execute([':eid' => (int)$user['id']]);
  $courses = $stmt->fetchAll();
}
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2><?= $is_admin ? 'Edit' : 'Request Edit For' ?> Task #<?= e($id) ?></h2>
  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Title<input name="title" value="<?= e($task['title']) ?>" required></label>
    <label>Course<select name="course_id" required>
      <option value="">-- select course --</option>
      <?php foreach($courses as $c): ?><option value="<?= e($c['id']) ?>" <?= $task['course_id']==$c['id']? 'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?>
    </select></label>
    <label>Type<select name="type" id="task-type"><option value="project" <?= $task['type']=='project' ? 'selected':'' ?>>Project</option><option value="topic" <?= $task['type']=='topic' ? 'selected':'' ?>>Topic</option></select></label>
    <label id="url-label" style="display:<?= $task['type']=='project' ? 'block' : 'none' ?>;">URL<input name="url" value="<?= e($task['url'] ?? '') ?>" placeholder="https://..."></label>
    <label>Description</label>
    <div id="quill-desc" style="min-height:200px;"><?= $task['description'] ?></div>
    <input type="hidden" name="description" id="description-hidden" />
    <label>Status<select name="status"><option value="backlog" <?= $task['status']=='backlog' ? 'selected':'' ?>>Backlog</option><option value="in_review" <?= $task['status']=='in_review' ? 'selected':'' ?>>In Review</option><option value="completed" <?= $task['status']=='completed' ? 'selected':'' ?>>Completed</option></select></label>
    <p style="margin-top:12px;color:#64748b;font-size:0.9rem;"><strong>Note:</strong> This task will be visible to all learners enrolled in the selected course.</p>
    <button class="btn"><?= $is_admin ? 'Save Changes' : 'Submit Edit Request' ?></button>
  </form>
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script>
    var quill = new Quill('#quill-desc', { theme: 'snow' });
    quill.root.innerHTML = <?= json_encode($task['description'] ?? '') ?>;
    document.querySelector('form').addEventListener('submit', function(){
      document.getElementById('description-hidden').value = quill.root.innerHTML;
    });
    document.getElementById('task-type').addEventListener('change', function(){
      document.getElementById('url-label').style.display = this.value === 'project' ? 'block' : 'none';
    });
  </script>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
