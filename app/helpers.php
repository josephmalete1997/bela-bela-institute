<?php
declare(strict_types=1);

/**
 * Escape output for HTML
 */
function e(string $s): string { 
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); 
}

/**
 * Redirect to a path
 */
function redirect(string $path): void {
  header("Location: {$path}");
  exit;
}

/**
 * Generate URL slug from text
 */
function slugify(string $text): string {
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

/**
 * Send notification to user
 */
function notify_user(int $user_id, string $title, string $message = null, string $link = null): bool {
  try {
    $stmt = db()->prepare("INSERT INTO notifications (user_id,title,message,link) VALUES (:uid,:title,:msg,:link)");
    return (bool)$stmt->execute([':uid'=>$user_id,':title'=>$title,':msg'=>$message,':link'=>$link]);
  } catch (Exception $e) {
    log_error("Failed to notify user {$user_id}: " . $e->getMessage());
    return false;
  }
}

/**
 * Validate email address
 */
function validate_email(string $email): bool {
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate and sanitize input
 */
function sanitize_string(string $input, int $max_length = 255): string {
  $input = trim($input);
  $input = strip_tags($input);
  if (mb_strlen($input) > $max_length) {
    $input = mb_substr($input, 0, $max_length);
  }
  return $input;
}

/**
 * Validate password strength
 */
function validate_password(string $password, int $min_length = 8): array {
  $errors = [];
  
  if (mb_strlen($password) < $min_length) {
    $errors[] = "Password must be at least {$min_length} characters long.";
  }
  
  if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter.";
  }
  
  if (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Password must contain at least one lowercase letter.";
  }
  
  if (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password must contain at least one number.";
  }
  
  return $errors;
}

/**
 * Rate limiting check
 */
function check_rate_limit(string $key, int $max_attempts = 5, int $window = 900): bool {
  $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($key) . '.json';
  
  $data = [];
  if (file_exists($cache_file)) {
    $content = file_get_contents($cache_file);
    $data = json_decode($content, true) ?: [];
  }
  
  $now = time();
  $attempts = array_filter($data, function($timestamp) use ($now, $window) {
    return ($now - $timestamp) < $window;
  });
  
  if (count($attempts) >= $max_attempts) {
    return false; // Rate limit exceeded
  }
  
  $attempts[] = $now;
  file_put_contents($cache_file, json_encode($attempts));
  
  return true; // Within rate limit
}

/**
 * Get rate limit remaining time
 */
function get_rate_limit_remaining(string $key, int $window = 900): int {
  $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($key) . '.json';
  
  if (!file_exists($cache_file)) {
    return 0;
  }
  
  $content = file_get_contents($cache_file);
  $data = json_decode($content, true) ?: [];
  
  if (empty($data)) {
    return 0;
  }
  
  $oldest = min($data);
  $remaining = ($oldest + $window) - time();
  
  return max(0, $remaining);
}

/**
 * Log error message
 */
function log_error(string $message, array $context = []): void {
  $log_dir = __DIR__ . '/../logs';
  if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
  }
  
  $log_file = $log_dir . '/error_' . date('Y-m-d') . '.log';
  $timestamp = date('Y-m-d H:i:s');
  $context_str = !empty($context) ? ' ' . json_encode($context) : '';
  $log_entry = "[{$timestamp}] {$message}{$context_str}" . PHP_EOL;
  
  @file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Log info message
 */
function log_info(string $message, array $context = []): void {
  $log_dir = __DIR__ . '/../logs';
  if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
  }
  
  $log_file = $log_dir . '/info_' . date('Y-m-d') . '.log';
  $timestamp = date('Y-m-d H:i:s');
  $context_str = !empty($context) ? ' ' . json_encode($context) : '';
  $log_entry = "[{$timestamp}] {$message}{$context_str}" . PHP_EOL;
  
  @file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Generate secure random token
 */
function generate_token(int $length = 32): string {
  return bin2hex(random_bytes($length));
}

/**
 * Format date for display
 */
function format_date(string $date, string $format = 'Y-m-d H:i:s'): string {
  try {
    $dt = new DateTime($date);
    return $dt->format($format);
  } catch (Exception $e) {
    return $date;
  }
}

/**
 * Get input value with default
 */
function input(string $key, $default = null) {
  return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Check if request is POST
 */
function is_post(): bool {
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 */
function is_get(): bool {
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}
