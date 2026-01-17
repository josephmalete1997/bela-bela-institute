<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!is_logged_in()) { http_response_code(403); echo 'Forbidden'; exit; }
$uid = auth_user()['id'];
$stmt = db()->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid AND is_read = 0");
$stmt->execute([':uid'=>$uid]);
header('Content-Type: application/json');
echo json_encode(['success'=>true,'marked'=> $stmt->rowCount()]);
