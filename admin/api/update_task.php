<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
csrf_verify();
$id = (int)($_POST['id'] ?? 0);
if (!$id) { redirect('../tasks.php'); }
$title = trim($_POST['title'] ?? '');
$type = $_POST['type'] ?? 'project';
$description = $_POST['description'] ?? null;
$assigned_user = (int)($_POST['assigned_user'] ?? 0) ?: null;
$status = $_POST['status'] ?? 'backlog';
require_once __DIR__ . '/../../includes/tasks_model.php';
$ok = tasks_update($id, ['title'=>$title,'type'=>$type,'description'=>$description,'assigned_user_id'=>$assigned_user,'status'=>$status]);
redirect('../tasks.php');
