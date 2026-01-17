<?php
declare(strict_types=1);

/**
 * Get cached configuration
 */
function get_config(): array {
  static $config = null;
  if ($config === null) {
    $config = require __DIR__ . "/config.php";
  }
  return $config;
}

/**
 * Get database connection (singleton)
 */
function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $cfg = get_config();
  $db = $cfg["db"];

  try {
    $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
    $pdo = new PDO($dsn, $db["user"], $db["pass"], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
      PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);
    return $pdo;
  } catch (PDOException $e) {
    // Log error but don't expose database details
    if (function_exists('log_error')) {
      log_error("Database connection failed", ["host" => $db['host'], "db" => $db['name']]);
    }
    
    $config = get_config();
    if ($config['app']['debug']) {
      throw new RuntimeException("Database connection failed: " . $e->getMessage());
    } else {
      throw new RuntimeException("Database connection failed. Please contact the administrator.");
    }
  }
}

/**
 * Check database connection status
 */
function db_status(): string {
  try {
    db()->query("SELECT 1");
    return "✅ Database connected successfully.";
  } catch (Throwable $e) {
    return "❌ Database connection failed: " . $e->getMessage();
  }
}

