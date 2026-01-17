<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!is_logged_in()) { http_response_code(403); echo 'Forbidden'; exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
$id = (int)($_POST['id'] ?? 0);
$uid = auth_user()['id'];
if (!$id) { http_response_code(400); echo 'Bad'; exit; }
$stmt = db()->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :uid");
$stmt->execute([':id'=>$id, ':uid'=>$uid]);
redirect($_POST['return'] ?? '/');
