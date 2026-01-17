<?php
require_once dirname(__DIR__) . "/app/bootstrap.php";

$error = "";
$config = require __DIR__ . "/../app/config.php";

if (is_post()) {
  csrf_verify();
  
  $email = trim(input("email", ""));
  $pass = input("password", "");
  $ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";
  
  // Rate limiting
  $rate_limit_key = "login_" . $ip . "_" . md5($email);
  $max_attempts = $config["security"]["rate_limit_login"] ?? 5;
  $window = $config["security"]["rate_limit_window"] ?? 900;
  
  if (!check_rate_limit($rate_limit_key, $max_attempts, $window)) {
    $remaining = get_rate_limit_remaining($rate_limit_key, $window);
    $minutes = ceil($remaining / 60);
    $error = "Too many login attempts. Please try again in {$minutes} minute(s).";
    log_info("Rate limit exceeded for login", ["email" => $email, "ip" => $ip]);
  } else {
    // Validate input
    if (empty($email) || empty($pass)) {
      $error = "Please enter both email and password.";
    } elseif (!validate_email($email)) {
      $error = "Invalid email format.";
    } else {
      $u = find_user_by_email($email);
      
      if (!$u) {
        $error = "Invalid email or password.";
        log_info("Failed login attempt - user not found", ["email" => $email, "ip" => $ip]);
      } elseif (!password_verify($pass, $u["password_hash"] ?? "")) {
        $error = "Invalid email or password.";
        log_info("Failed login attempt - wrong password", ["email" => $email, "ip" => $ip]);
      } elseif (!is_user_active($u)) {
        $error = "Your account has been deactivated. Please contact support.";
        log_info("Failed login attempt - inactive account", ["email" => $email, "ip" => $ip]);
      } else {
        // Successful login
        login_user($u);
        update_last_login($u["id"]);
        
        // Clear rate limit on success
        $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($rate_limit_key) . '.json';
        @unlink($cache_file);
        
        $redirect = $u["role"] === "admin" ? "../admin/dashboard.php" : "../student/index.php";
        redirect($redirect);
      }
    }
  }
}
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title></head>
<body>
  <h2>Login</h2>
  <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Email</label><br>
    <input name="email" type="email" required><br><br>
    <label>Password</label><br>
    <input name="password" type="password" required><br><br>
    <button type="submit">Login</button>
  </form>

  <p>No account? <a href="./register.php">Register</a></p>
  <p><a href="./forgot_password.php">Forgot Password?</a></p>
</body>
</html>
