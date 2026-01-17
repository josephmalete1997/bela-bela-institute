<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../includes/articles_model.php';

$action = $_GET['action'] ?? 'list';
if ($action === 'list') {
  $page = max(1, (int)($_GET['page'] ?? 1));
  $per = min(50, max(1, (int)($_GET['per'] ?? 10)));
  $offset = ($page - 1) * $per;
  $items = articles_find_all($per, $offset);
  echo json_encode(['data' => $items]);
  exit;
}

if ($action === 'get' && !empty($_GET['slug'])) {
  $a = articles_find_by_slug($_GET['slug']);
  if (!$a) {
    http_response_code(404); echo json_encode(['error'=>'Not found']); exit;
  }
  echo json_encode($a);
  exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
