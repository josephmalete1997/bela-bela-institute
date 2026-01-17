<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');
$q = trim($_GET['q'] ?? '');
$out = [];
if ($q !== '') {
  $stmt = db()->prepare("SELECT id, full_name AS name, email FROM users WHERE full_name LIKE :q OR email LIKE :q ORDER BY full_name LIMIT 20");
  $stmt->execute([':q' => "%$q%"]);
  $out = $stmt->fetchAll();
}
header('Content-Type: application/json');
echo json_encode($out);
