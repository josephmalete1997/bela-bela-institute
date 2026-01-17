<?php 
require_once __DIR__ . "/../app/bootstrap.php";

$error = "";
$config = require __DIR__ . "/../app/config.php";
$min_password_length = $config["security"]["password_min_length"] ?? 8;

if (is_post()) {
  csrf_verify();

  $name = sanitize_string(trim(input("full_name", "")), 255);
  $email = trim(input("email", ""));
  $phone = sanitize_string(trim(input("phone", "")), 20);
  $pass = input("password", "");
  $pass_confirm = input("password_confirm", "");

  // Validation
  if (empty($name)) {
    $error = "Full name is required.";
  } elseif (mb_strlen($name) < 2) {
    $error = "Full name must be at least 2 characters.";
  } elseif (empty($email)) {
    $error = "Email is required.";
  } elseif (!validate_email($email)) {
    $error = "Invalid email format.";
  } elseif (empty($pass)) {
    $error = "Password is required.";
  } elseif ($pass !== $pass_confirm) {
    $error = "Passwords do not match.";
  } else {
    // Password strength validation
    $password_errors = validate_password($pass, $min_password_length);
    if (!empty($password_errors)) {
      $error = implode(" ", $password_errors);
    } elseif (find_user_by_email($email)) {
      $error = "An account with this email already exists.";
    } else {
      // Create user
      try {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = db()->prepare("
          INSERT INTO users(full_name, email, phone, password_hash, role, status) 
          VALUES(?, ?, ?, ?, 'student', 'active')
        ");
        $stmt->execute([$name, $email, $phone ?: null, $hash]);

        $u = find_user_by_email($email);
        if ($u) {
          login_user($u);
          log_info("New user registered", ["user_id" => $u["id"], "email" => $email]);
          redirect("../student/index.php");
        } else {
          $error = "Registration failed. Please try again.";
        }
      } catch (Exception $e) {
        log_error("Registration error", ["email" => $email, "error" => $e->getMessage()]);
        $error = "An error occurred during registration. Please try again.";
      }
    }
  }
}
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register</title></head>
<body>
  <h2>Create Student Account</h2>
  <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Full Name</label><br>
    <input name="full_name" value="<?= e(input("full_name", "")) ?>" required minlength="2" maxlength="255"><br><br>
    
    <label>Email</label><br>
    <input name="email" type="email" value="<?= e(input("email", "")) ?>" required><br><br>
    
    <label>Phone (Optional)</label><br>
    <input name="phone" type="tel" value="<?= e(input("phone", "")) ?>" maxlength="20"><br><br>
    
    <label>Password (min <?= $min_password_length ?> chars, must include uppercase, lowercase, and number)</label><br>
    <input name="password" type="password" required minlength="<?= $min_password_length ?>"><br><br>
    
    <label>Confirm Password</label><br>
    <input name="password_confirm" type="password" required minlength="<?= $min_password_length ?>"><br><br>
    
    <button type="submit">Create Account</button>
  </form>
</body>
</html>
