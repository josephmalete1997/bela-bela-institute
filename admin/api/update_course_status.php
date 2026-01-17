<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['id']) || empty($input['kanban_status'])) {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Invalid']);
  exit;
}

// basic csrf check
if (empty($input['csrf']) || !hash_equals(csrf_token(), $input['csrf'])) {
  http_response_code(403);
  echo json_encode(['success'=>false,'error'=>'CSRF']);
  exit;
}

$id = (int)$input['id'];
$status = preg_replace('/[^a-z_]/','', $input['kanban_status']);
$allowed = ['backlog','planned','ongoing','completed'];
if (!in_array($status, $allowed)) $status = 'backlog';

$stmt = db()->prepare("UPDATE courses SET kanban_status = :s WHERE id = :id");
$stmt->execute([':s' => $status, ':id' => $id]);

echo json_encode(['success'=>true]);
