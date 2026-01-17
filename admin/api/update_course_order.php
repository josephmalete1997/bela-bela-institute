<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['columns']) || !is_array($input['columns'])) {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Invalid payload']);
  exit;
}

// csrf
$csrf = $input['csrf'] ?? '';
if (empty($csrf) || !hash_equals(csrf_token(), $csrf)) {
  http_response_code(403);
  echo json_encode(['success'=>false,'error'=>'CSRF']);
  exit;
}

$allowed = ['backlog','planned','ongoing','completed'];

$pdo = db();
try {
  $pdo->beginTransaction();

  foreach ($input['columns'] as $col => $ids) {
    if (!in_array($col, $allowed)) continue;
    if (!is_array($ids)) continue;
    foreach ($ids as $pos => $id) {
      $id = (int)$id;
      $pos = (int)$pos;
      $stmt = $pdo->prepare("UPDATE courses SET kanban_status = :s, kanban_position = :pos WHERE id = :id");
      $stmt->execute([':s'=>$col, ':pos'=>$pos, ':id'=>$id]);
    }
  }

  $pdo->commit();
  echo json_encode(['success'=>true]);
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
