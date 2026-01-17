<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
    $error = 'Upload failed';
  } else {
    $f = fopen($_FILES['csv']['tmp_name'], 'r');
    $header = fgetcsv($f);
    $count = 0;
    while (($row = fgetcsv($f)) !== false) {
      $data = array_combine($header, $row);
      // expected columns: title, type, description, course_id, status
      $task = [
        'title' => $data['title'] ?? 'Untitled',
        'type' => $data['type'] ?? 'project',
        'description' => $data['description'] ?? null,
        'course_id' => isset($data['course_id']) ? (int)$data['course_id'] : null,
        'submitter_id' => null,
        'assigned_user_id' => isset($data['assigned_user_id']) ? (int)$data['assigned_user_id'] : null,
        'status' => $data['status'] ?? 'backlog',
        'position' => 0,
      ];
      require_once __DIR__ . "/../includes/tasks_model.php";
      tasks_create($task);
      $count++;
    }
    fclose($f);
    redirect('tasks.php');
  }
}
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2>Import Tasks (CSV)</h2>
  <p>Upload a CSV file with header: title,type,description,course_id,status,assigned_user_id</p>
  <form method="post" enctype="multipart/form-data">
    <label>CSV file<input type="file" name="csv" accept="text/csv" required></label>
    <button class="btn">Import</button>
  </form>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
