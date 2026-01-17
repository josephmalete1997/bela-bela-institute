<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $type = $_POST['type'] ?? 'project';
  $desc = $_POST['description'] ?? '';
  $data = [
    'title'=>$title,
    'type'=>$type,
    'description'=>$desc,
    'submitter_id'=>auth_user()['id'] ?? null,
    'status'=>$type === 'topic' ? 'studying' : 'in_review'
  ];
  tasks_create($data);
  redirect('/student/tasks_board.php');
}

require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Submit Task / Project</h2>
    <form method="post" class="form">
      <label>Title<input name="title" required></label>
      <label>Type<select name="type"><option value="topic">Topic (reading/video)</option><option value="project">Project (needs review)</option></select></label>
      <label>Description<textarea name="description"></textarea></label>
      <button class="btn">Submit</button>
    </form>
  </div>
</main>
<?php require_once __DIR__ . "/layout/footer.php";