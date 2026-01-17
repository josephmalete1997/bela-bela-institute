<?php
require_once __DIR__.'/../../app/bootstrap.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
csrf_verify();
$task_id = (int)($_POST['task_id'] ?? 0);
$user_id = (int)($_POST['user_id'] ?? 0);
if (!$task_id || !$user_id) { redirect('../task_list.php'); }
$ok = tasks_revoke_review_override($task_id, $user_id);
redirect('../../student/task_view.php?id='.$task_id);
