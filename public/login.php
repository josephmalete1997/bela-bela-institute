<?php
require_once dirname(__DIR__) . "/app/bootstrap.php";
?>
<?php
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();
  $email = trim($_POST["email"] ?? "");
  $pass  = $_POST["password"] ?? "";

  $u = find_user_by_email($email);
  if (!$u || !password_verify($pass, $u["password_hash"])) {
    $error = "Invalid email or password.";
  } elseif ($u["status"] !== "active") {
    $error = "Account blocked.";
  } else {
    login_user($u);
    redirect($u["role"] === "admin" ? "../admin/dashboard" : "../student/index");
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

  <p>No account? <a href="./register">Register</a></p>
</body>
</html>
