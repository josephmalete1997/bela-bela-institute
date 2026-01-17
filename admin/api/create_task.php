<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
csrf_verify();
require_once __DIR__ . '/../../app/helpers.php';

$title = sanitize_string(trim(input('title', '')), 255);
$type = in_array(input('type', 'project'), ['topic', 'project'], true) ? input('type', 'project') : 'project';
$description = !empty(input('description')) ? sanitize_string(input('description'), 5000) : null;
$course_id = (int)(input('course_id', 0)) ?: null;
$status = in_array(input('status', 'backlog'), ['backlog', 'studying', 'in_review', 'review_feedback', 'completed'], true) 
  ? input('status', 'backlog') 
  : 'backlog';

if (empty($title)) {
  http_response_code(400);
  echo json_encode(['error' => 'Title is required']);
  exit;
}
require_once __DIR__ . '/../../includes/tasks_model.php';
$id = tasks_create(['title'=>$title,'type'=>$type,'description'=>$description,'course_id'=>$course_id,'submitter_id'=>null,'assigned_user_id'=>null,'status'=>$status,'position'=>0]);
// Notify enrolled students for this course
if ($course_id) {
	$pdo = db();
	$stmt = $pdo->prepare("SELECT u.id,u.email FROM enrollments e JOIN intakes i ON i.id=e.intake_id JOIN users u ON u.id=e.user_id WHERE i.course_id = :cid");
	$stmt->execute([':cid'=>$course_id]);
	$students = $stmt->fetchAll();
	foreach ($students as $s) {
		$uid = (int)($s['id'] ?? 0);
		if ($uid) {
			notify_user($uid, 'New Course Task', 'A new task "' . e($title) . '" has been created for your course.', '../../student/task_view.php?id=' . $id);
			// try sending email if configured
			if (!empty($s['email'])) {
				$to = filter_var($s['email'], FILTER_SANITIZE_EMAIL);
				$sub = "New Task: " . e($title);
				$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
			$body = "A new task '" . e($title) . "' has been added to your course.\n\nView: {$scheme}://{$host}/student/task_view.php?id={$id}";
				@mail($to, $sub, $body);
			}
		}
	}
}
redirect('../task_list.php');
