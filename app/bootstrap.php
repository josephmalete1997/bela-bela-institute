<?php
declare(strict_types=1);

// Load configuration first
$config = require_once __DIR__ . "/config.php";

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Configure session security
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
  || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

session_set_cookie_params([
  "lifetime" => $config['app']['session_lifetime'],
  "path" => "/",
  "domain" => "",
  "secure" => $is_https, // Use HTTPS in production
  "httponly" => true,
  "samesite" => "Lax",
]);

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
  $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
  session_regenerate_id(true);
  $_SESSION['created'] = time();
}

// Load core functions
require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/csrf.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/middleware.php";

// Error handling
if ($config['app']['debug']) {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
} else {
  error_reporting(E_ALL);
  ini_set('display_errors', '0');
  ini_set('log_errors', '1');
  ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}
