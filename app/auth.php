<?php
declare(strict_types=1);

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/helpers.php";

/**
 * Get current authenticated user
 */
function auth_user(): ?array {
  return $_SESSION["user"] ?? null;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
  return !empty($_SESSION["user"]) && isset($_SESSION["user"]["id"]);
}

/**
 * Login user and set session
 */
function login_user(array $user): void {
  $_SESSION["user"] = [
    "id" => (int)$user["id"],
    "full_name" => sanitize_string($user["full_name"] ?? ""),
    "email" => filter_var($user["email"] ?? "", FILTER_SANITIZE_EMAIL),
    "role" => sanitize_string($user["role"] ?? "student"),
  ];
  $_SESSION["last_activity"] = time();
  
  log_info("User logged in", ["user_id" => $user["id"], "email" => $user["email"]]);
}

/**
 * Logout user and destroy session
 */
function logout_user(): void {
  $user_id = $_SESSION["user"]["id"] ?? null;
  session_unset();
  session_destroy();
  
  if ($user_id) {
    log_info("User logged out", ["user_id" => $user_id]);
  }
}

/**
 * Find user by email
 */
function find_user_by_email(string $email): ?array {
  if (!validate_email($email)) {
    return null;
  }
  
  try {
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    return $u ?: null;
  } catch (Exception $e) {
    log_error("Error finding user by email", ["email" => $email, "error" => $e->getMessage()]);
    return null;
  }
}

/**
 * Find user by ID
 */
function find_user_by_id(int $id): ?array {
  try {
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    return $u ?: null;
  } catch (Exception $e) {
    log_error("Error finding user by ID", ["user_id" => $id, "error" => $e->getMessage()]);
    return null;
  }
}

/**
 * Update user last login timestamp
 */
function update_last_login(int $user_id): void {
  try {
    $stmt = db()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user_id]);
  } catch (Exception $e) {
    log_error("Error updating last login", ["user_id" => $user_id, "error" => $e->getMessage()]);
  }
}

/**
 * Check if user account is active
 */
function is_user_active(array $user): bool {
  return isset($user["status"]) && $user["status"] === "active";
}

/**
 * Generate password reset token
 */
function generate_password_reset_token(int $user_id): string {
  $token = generate_token(32);
  $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
  
  try {
    $stmt = db()->prepare("
      UPDATE users 
      SET password_reset_token = ?, password_reset_expires = ? 
      WHERE id = ?
    ");
    $stmt->execute([$token, $expires, $user_id]);
    return $token;
  } catch (Exception $e) {
    log_error("Error generating password reset token", ["user_id" => $user_id, "error" => $e->getMessage()]);
    return "";
  }
}

/**
 * Verify password reset token
 */
function verify_password_reset_token(string $token): ?array {
  try {
    $stmt = db()->prepare("
      SELECT * FROM users 
      WHERE password_reset_token = ? 
      AND password_reset_expires > NOW() 
      LIMIT 1
    ");
    $stmt->execute([$token]);
    return $stmt->fetch() ?: null;
  } catch (Exception $e) {
    log_error("Error verifying password reset token", ["error" => $e->getMessage()]);
    return null;
  }
}

/**
 * Clear password reset token
 */
function clear_password_reset_token(int $user_id): void {
  try {
    $stmt = db()->prepare("
      UPDATE users 
      SET password_reset_token = NULL, password_reset_expires = NULL 
      WHERE id = ?
    ");
    $stmt->execute([$user_id]);
  } catch (Exception $e) {
    log_error("Error clearing password reset token", ["user_id" => $user_id, "error" => $e->getMessage()]);
  }
}
