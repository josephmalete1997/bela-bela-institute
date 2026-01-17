<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/helpers.php";

function require_login(): void {
  if (!is_logged_in()) {
    // Determine relative path to login based on current location
    $current_dir = dirname($_SERVER['SCRIPT_NAME']);
    if (strpos($current_dir, '/admin') !== false) {
      redirect("../public/login.php");
    } elseif (strpos($current_dir, '/student') !== false) {
      redirect("../public/login.php");
    } else {
      redirect("public/login.php");
    }
  }
}

function require_role(string $role): void {
  require_login();
  $u = auth_user();
  if (!$u || $u["role"] !== $role) {
    http_response_code(403);
    exit("Forbidden");
  }
}
