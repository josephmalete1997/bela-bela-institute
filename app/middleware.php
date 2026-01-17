<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/helpers.php";

function require_login(): void {
  if (!is_logged_in()) redirect("/public/login.php");
}

function require_role(string $role): void {
  require_login();
  $u = auth_user();
  if (!$u || $u["role"] !== $role) {
    http_response_code(403);
    exit("Forbidden");
  }
}
