<?php
require_once __DIR__ . "/db.php";

function auth_user(): ?array {
  return $_SESSION["user"] ?? null;
}

function is_logged_in(): bool {
  return !empty($_SESSION["user"]);
}

function login_user(array $user): void {
  $_SESSION["user"] = [
    "id" => (int)$user["id"],
    "full_name" => $user["full_name"],
    "email" => $user["email"],
    "role" => $user["role"],
  ];
}

function logout_user(): void {
  unset($_SESSION["user"]);
}

function find_user_by_email(string $email): ?array {
  $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  return $u ?: null;
}
