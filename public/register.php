<?php require_once __DIR__ . "/../app/bootstrap.php"; ?>

<?php
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  csrf_verify();

  $name  = trim($_POST["full_name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $phone = trim($_POST["phone"] ?? "");
  $pass  = $_POST["password"] ?? "";

  if (!$name || !$email || strlen($pass) < 8) {
    $error = "Fill all fields. Password must be at least 8 characters.";
  } elseif (find_user_by_email($email)) {
    $error = "Email already exists.";
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO users(full_name,email,phone,password_hash,role) VALUES(?,?,?,?, 'student')");
    $stmt->execute([$name, $email, $phone, $hash]);

    $u = find_user_by_email($email);
    login_user($u);
    redirect("/student/index.php");
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
    <label>Full Name</label><br><input name="full_name" required><br><br>
    <label>Email</label><br><input name="email" type="email" required><br><br>
    <label>Phone</label><br><input name="phone"><br><br>
    <label>Password</label><br><input name="password" type="password" required><br><br>
    <button type="submit">Create Account</button>
  </form>
</body>
</html>
