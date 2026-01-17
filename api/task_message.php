<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_once __DIR__ . "/../includes/tasks_model.php";

require_login();
csrf_verify();

$task_id = (int)($_POST['task_id'] ?? 0);
$submitter_id = (int)($_POST['submitter_id'] ?? 0);
$recipient_id = (int)($_POST['recipient_id'] ?? 0);
$message = trim($_POST['message'] ?? '');
$sender_id = (int)(auth_user()['id'] ?? 0);

if (!$task_id || !$submitter_id || !$recipient_id || $message === '' || !$sender_id) {
  http_response_code(400);
  exit('Invalid message request.');
}

$task = tasks_find($task_id);
if (!$task) {
  http_response_code(404);
  exit('Task not found.');
}

$sender_role = auth_user()['role'] ?? 'student';
$is_submitter = ($sender_id === $submitter_id);
$is_reviewer = tasks_is_assigned_reviewer($task_id, $submitter_id, $sender_id);
$is_admin = ($sender_role === 'admin');
$is_educator = ($sender_role === 'educator') && tasks_is_course_educator((int)($task['course_id'] ?? 0), $sender_id);

if (!$is_submitter && !$is_reviewer && !$is_admin && !$is_educator) {
  http_response_code(403);
  exit('Not allowed.');
}

if ($is_submitter) {
  $allowed = tasks_is_assigned_reviewer($task_id, $submitter_id, $recipient_id);
  if (!$allowed && $is_admin) $allowed = true;
  if (!$allowed && tasks_is_course_educator((int)($task['course_id'] ?? 0), $recipient_id)) $allowed = true;
  if (!$allowed) {
    http_response_code(403);
    exit('You can only message assigned reviewers or course educators.');
  }
}

if ($is_educator && $recipient_id !== $submitter_id) {
  http_response_code(403);
  exit('Replies must go to the submitter.');
}

if ($is_reviewer && $recipient_id !== $submitter_id) {
  http_response_code(403);
  exit('Replies must go to the submitter.');
}

if ($is_admin && $recipient_id !== $submitter_id) {
  http_response_code(403);
  exit('Replies must go to the submitter.');
}

if ($is_submitter && $recipient_id === $sender_id) {
  http_response_code(403);
  exit('Cannot message yourself.');
}

tasks_add_message($task_id, $submitter_id, $sender_id, $recipient_id, $message);
notify_user($recipient_id, 'New task message', $message, '/student/task_view.php?id=' . $task_id . '&submitter_id=' . $submitter_id);

// notify admins and course educators so they can reply from the reviews tab
$adminStmt = db()->prepare("SELECT id FROM users WHERE role = 'admin'");
$adminStmt->execute();
$admins = $adminStmt->fetchAll(PDO::FETCH_COLUMN);
$educators = tasks_list_course_educators((int)($task['course_id'] ?? 0));
foreach ($admins as $aid) {
  if ((int)$aid === $sender_id) continue;
  notify_user((int)$aid, 'Message on project review', $message, '/admin/review.php?id=' . $task_id . '&submitter_id=' . $submitter_id);
}
foreach ($educators as $ed) {
  $eid = (int)($ed['id'] ?? 0);
  if ($eid && $eid !== $sender_id) {
    notify_user($eid, 'Message on project review', $message, '/admin/review.php?id=' . $task_id . '&submitter_id=' . $submitter_id);
  }
}

if (strpos($_SERVER['HTTP_REFERER'] ?? '', '/admin/') !== false) {
  redirect('/admin/review.php?id=' . $task_id . '&submitter_id=' . $submitter_id);
}
redirect('/student/task_view.php?id=' . $task_id . '&submitter_id=' . $submitter_id);
