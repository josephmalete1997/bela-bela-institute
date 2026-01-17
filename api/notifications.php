<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!is_logged_in()) { http_response_code(403); echo json_encode([]); exit; }
$uid = auth_user()['id'];
$limit = (int)($_GET['limit'] ?? 10);
$stmt = db()->prepare("SELECT id,title,message,link,is_read,created_at FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim");
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($rows);
