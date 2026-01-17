<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/articles_model.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($id) {
  articles_delete($id);
}
redirect('./articles');
