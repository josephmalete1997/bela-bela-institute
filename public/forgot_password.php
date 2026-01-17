<?php
require_once dirname(__DIR__) . "/app/bootstrap.php";

$error = "";
$success = "";

if (is_post()) {
  csrf_verify();
  
  $email = trim(input("email", ""));
  
  if (empty($email)) {
    $error = "Please enter your email address.";
  } elseif (!validate_email($email)) {
    $error = "Invalid email format.";
  } else {
    $user = find_user_by_email($email);
    
    if (!$user) {
      // Don't reveal if email exists for security
      $success = "If an account exists with that email, a password reset link has been sent.";
    } else {
      $token = generate_password_reset_token($user["id"]);
      
      if ($token) {
        $config = require __DIR__ . "/../app/config.php";
        $base_url = $config["app"]["base_url"] ?: (isset($_SERVER["HTTP_HOST"]) ? "http://" . $_SERVER["HTTP_HOST"] : "");
        $reset_link = $base_url . "/public/reset_password.php?token=" . urlencode($token);
        
        // Send email (basic implementation - improve with proper mailer)
        $to = $user["email"];
        $subject = "Password Reset - Bela-Bela Institute";
        $message = "Hello " . e($user["full_name"]) . ",\n\n";
        $message .= "You requested a password reset. Click the link below to reset your password:\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request this, please ignore this email.\n\n";
        $message .= "Best regards,\nBela-Bela Institute";
        
        $headers = "From: " . $config["mail"]["from_email"] . "\r\n";
        $headers .= "Reply-To: " . $config["mail"]["from_email"] . "\r\n";
        
        @mail($to, $subject, $message, $headers);
        
        log_info("Password reset requested", ["user_id" => $user["id"], "email" => $email]);
        $success = "If an account exists with that email, a password reset link has been sent.";
      } else {
        $error = "Failed to generate reset token. Please try again.";
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
  <title>Forgot Password - Bela-Bela Institute</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
    .error { color: red; background: #ffe6e6; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    .success { color: green; background: #e6ffe6; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    input[type="email"] { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
    button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0056b3; }
    a { color: #007bff; text-decoration: none; }
  </style>
</head>
<body>
  <h2>Forgot Password</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>
  
  <?php if ($success): ?>
    <div class="success"><?= e($success) ?></div>
    <p><a href="./login.php">Back to Login</a></p>
  <?php else: ?>
    <p>Enter your email address and we'll send you a link to reset your password.</p>
    
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label>Email</label><br>
      <input name="email" type="email" value="<?= e(input("email", "")) ?>" required autofocus><br><br>
      <button type="submit">Send Reset Link</button>
    </form>
    
    <p><a href="./login.php">Back to Login</a></p>
  <?php endif; ?>
</body>
</html>
