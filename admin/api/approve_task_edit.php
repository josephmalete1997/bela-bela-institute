<?php
require_once __DIR__ . "/../../app/bootstrap.php";
require_role('admin');
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
if (!$id || !in_array($action, ['approve','deny'], true)) {
  redirect('../task_edit_requests.php');
}

$req = db()->prepare("SELECT * FROM task_edit_requests WHERE id = :id LIMIT 1");
$req->execute([':id' => $id]);
$row = $req->fetch();
if (!$row) {
  redirect('../task_edit_requests.php');
}

if ($action === 'approve') {
  require_once __DIR__ . "/../../includes/tasks_model.php";
  tasks_update((int)$row['task_id'], [
    'title' => $row['title'],
    'type' => $row['type'],
    'description' => $row['description'],
    'course_id' => $row['course_id'],
    'assigned_user_id' => null,
    'status' => $row['status'],
    'url' => $row['url'],
  ]);
}

$stmt = db()->prepare("UPDATE task_edit_requests SET request_status = :s, reviewed_by = :rb, reviewed_at = NOW() WHERE id = :id");
$stmt->execute([':s' => $action === 'approve' ? 'approved' : 'denied', ':rb' => (int)auth_user()['id'], ':id' => $id]);

redirect('../task_edit_requests.php');
