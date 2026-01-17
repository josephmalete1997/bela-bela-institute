<?php
require_once dirname(__DIR__) . "/app/bootstrap.php";

$error = "";
$success = "";
$token = input("token", "");
$config = require __DIR__ . "/../app/config.php";
$min_password_length = $config["security"]["password_min_length"] ?? 8;

if (empty($token)) {
  $error = "Invalid or missing reset token.";
} else {
  $user = verify_password_reset_token($token);
  
  if (!$user) {
    $error = "Invalid or expired reset token. Please request a new one.";
  } elseif (is_post()) {
    csrf_verify();
    
    $pass = input("password", "");
    $pass_confirm = input("password_confirm", "");
    
    if (empty($pass)) {
      $error = "Password is required.";
    } elseif ($pass !== $pass_confirm) {
      $error = "Passwords do not match.";
    } else {
      $password_errors = validate_password($pass, $min_password_length);
      if (!empty($password_errors)) {
        $error = implode(" ", $password_errors);
      } else {
        try {
          $hash = password_hash($pass, PASSWORD_DEFAULT);
          $stmt = db()->prepare("UPDATE users SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
          $stmt->execute([$hash, $user["id"]]);
          
          clear_password_reset_token($user["id"]);
          log_info("Password reset completed", ["user_id" => $user["id"]]);
          
          $success = "Password has been reset successfully. You can now login with your new password.";
        } catch (Exception $e) {
          log_error("Password reset error", ["user_id" => $user["id"], "error" => $e->getMessage()]);
          $error = "An error occurred. Please try again.";
        }
      }
    }
  }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset Password - Bela-Bela Institute</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
    .error { color: red; background: #ffe6e6; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    .success { color: green; background: #e6ffe6; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    input[type="password"] { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
    button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0056b3; }
    a { color: #007bff; text-decoration: none; }
  </style>
</head>
<body>
  <h2>Reset Password</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>
  
  <?php if ($success): ?>
    <div class="success"><?= e($success) ?></div>
    <p><a href="./login.php">Go to Login</a></p>
  <?php elseif ($user ?? null): ?>
    <p>Enter your new password below.</p>
    
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      
      <label>New Password (min <?= $min_password_length ?> chars, must include uppercase, lowercase, and number)</label><br>
      <input name="password" type="password" required minlength="<?= $min_password_length ?>"><br><br>
      
      <label>Confirm New Password</label><br>
      <input name="password_confirm" type="password" required minlength="<?= $min_password_length ?>"><br><br>
      
      <button type="submit">Reset Password</button>
    </form>
    
    <p><a href="./login.php">Back to Login</a></p>
  <?php else: ?>
    <p><a href="./forgot_password.php">Request a new reset link</a></p>
  <?php endif; ?>
</body>
</html>
