<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method'; exit; }
csrf_verify();
$id = (int)($_POST['id'] ?? 0);
if (!$id) { redirect('../tasks.php'); }
require_once __DIR__ . '/../../includes/tasks_model.php';
$ok = tasks_delete($id);
redirect('../tasks.php');
