<?php
function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $cfg = require __DIR__ . "/config.php";
  $db = $cfg["db"];

  $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
  $pdo = new PDO($dsn, $db["user"], $db["pass"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function db_status(): string {
  try {
    db()->query("SELECT 1");
    return "✅ Database connected successfully.";
  } catch (Throwable $e) {
    return "❌ Database connection failed: " . $e->getMessage();
  }
}

