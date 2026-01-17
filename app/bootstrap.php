<?php
declare(strict_types=1);

session_set_cookie_params([
  "httponly" => true,
  "samesite" => "Lax",
  "secure" => false, // set true when using HTTPS
]);

session_start();

require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/csrf.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/middleware.php";
