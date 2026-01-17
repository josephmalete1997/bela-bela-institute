<?php
/**
 * Load environment variables from .env file if it exists
 */
if (!function_exists('loadEnv')) {
  function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) continue; // Skip comments
      
      if (strpos($line, '=') === false) continue; // Skip lines without =
      
      list($name, $value) = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);
      
      // Remove quotes if present
      if (preg_match('/^"(.*)"$/', $value, $matches)) {
        $value = $matches[1];
      } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
        $value = $matches[1];
      }
      
      if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
      }
    }
  }
}

// Load .env file from project root (only once)
static $env_loaded = false;
if (!$env_loaded) {
  loadEnv(__DIR__ . '/../.env');
  $env_loaded = true;
}

return [
  "db" => [
    "host" => getenv('DB_HOST') ?: "localhost",
    "name" => getenv('DB_NAME') ?: "belabela_iHL",
    "user" => getenv('DB_USER') ?: "root",
    "pass" => getenv('DB_PASS') ?: "",
    "charset" => getenv('DB_CHARSET') ?: "utf8mb4",
  ],
  "app" => [
    "base_url" => getenv('APP_BASE_URL') ?: "",
    "env" => getenv('APP_ENV') ?: "production",
    "debug" => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    "timezone" => getenv('APP_TIMEZONE') ?: "Africa/Johannesburg",
    "session_lifetime" => (int)(getenv('SESSION_LIFETIME') ?: 7200), // 2 hours
  ],
  "security" => [
    "csrf_token_name" => "csrf",
    "password_min_length" => 8,
    "rate_limit_login" => 5, // attempts per 15 minutes
    "rate_limit_window" => 900, // 15 minutes in seconds
  ],
  "mail" => [
    "from_email" => getenv('MAIL_FROM_EMAIL') ?: "noreply@belabelainstitute.co.za",
    "from_name" => getenv('MAIL_FROM_NAME') ?: "Bela-Bela Institute",
  ],
];
