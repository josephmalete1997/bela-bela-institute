<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
csrf_verify();
$title = trim($_POST['title'] ?? '');
$type = $_POST['type'] ?? 'project';
$description = $_POST['description'] ?? null;
$course_id = (int)($_POST['course_id'] ?? 0) ?: null;
$assigned_user = (int)($_POST['assigned_user'] ?? 0) ?: null;
$status = $_POST['status'] ?? 'backlog';
require_once __DIR__ . '/../../includes/tasks_model.php';
$id = tasks_create(['title'=>$title,'type'=>$type,'description'=>$description,'course_id'=>$course_id,'submitter_id'=>null,'assigned_user_id'=>$assigned_user,'status'=>$status,'position'=>0]);
// Notify enrolled students for this course
if ($course_id) {
	$pdo = db();
	$stmt = $pdo->prepare("SELECT u.id,u.email FROM enrollments e JOIN intakes i ON i.id=e.intake_id JOIN users u ON u.id=e.user_id WHERE i.course_id = :cid");
	$stmt->execute([':cid'=>$course_id]);
	$students = $stmt->fetchAll();
	foreach ($students as $s) {
		$uid = (int)($s['id'] ?? 0);
		if ($uid) {
			notify_user($uid, 'New Course Task', 'A new task "'.htmlspecialchars($title, ENT_QUOTES).'" has been created for your course.', '/student/task_view.php?id='.$id);
			// try sending email if configured
			if (!empty($s['email'])) {
				$to = $s['email'];
				$sub = "New Task: {$title}";
				$body = "A new task '{$title}' has been added to your course.\n\nView: " . (isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . "/student/task_view.php?id={$id}";
				@mail($to, $sub, $body);
			}
		}
	}
}
redirect('../tasks.php');
