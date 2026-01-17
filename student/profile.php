<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_role('student');

$user = auth_user();
$error = "";
$success = "";

$uploadDirRel = "uploads/avatars"; // stored in DB relative to /public
$uploadDirAbs = dirname(__DIR__) . "/public/" . $uploadDirRel;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["avatar"])) {
  csrf_verify();

  $file = $_FILES["avatar"];

  if (!is_dir($uploadDirAbs)) {
    mkdir($uploadDirAbs, 0775, true);
  }

  if ($file["error"] !== UPLOAD_ERR_OK) {
    $error = "Upload failed (error code: {$file['error']}).";
  } elseif (!is_uploaded_file($file["tmp_name"])) {
    $error = "Invalid upload.";
  } else {
    $mime = mime_content_type($file["tmp_name"]) ?: "";

    // Strict MIME → extension mapping (safer than trusting original filename)
    $map = [
      "image/jpeg" => "jpg",
      "image/png"  => "png",
      "image/webp" => "webp",
    ];

    if (!isset($map[$mime])) {
      $error = "Only JPG, PNG, or WEBP allowed.";
    } elseif ($file["size"] > 2 * 1024 * 1024) {
      $error = "Image must be under 2MB.";
    } else {
      $ext = $map[$mime];
      $filename = "student_" . (int)$user["id"] . "." . $ext;

      // Path stored in DB (relative to /public)
      $pathRel = $uploadDirRel . "/" . $filename;

      // Absolute filesystem path
      $fullPath = $uploadDirAbs . "/" . $filename;

      // Move file
      if (!move_uploaded_file($file["tmp_name"], $fullPath)) {
        $error = "Could not save the uploaded file. Check folder permissions.";
      } else {
        // Optional: set file permissions
        @chmod($fullPath, 0644);

        db()->prepare("UPDATE users SET avatar=? WHERE id=?")
          ->execute([$pathRel, (int)$user["id"]]);

        $success = "Profile image updated.";
      }
    }
  }
}

// reload user
$stmt = db()->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([(int)$user["id"]]);
$userData = $stmt->fetch() ?: [];
?>
<?php require __DIR__ . "/layout/header.php"; ?>
<div class="container">
  <h2>My Profile</h2>

  <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>
  <?php if ($success): ?><p style="color:green;"><?= e($success) ?></p><?php endif; ?>

  <?php $avatar = $userData["avatar"] ?? "assets/avatar-placeholder.png"; ?>
  <img src="../public/<?= e($avatar) ?>" width="120" height="120" style="border-radius:50%; border:1px solid #ccc; object-fit:cover;" alt="Profile image">

  <form method="post" enctype="multipart/form-data" style="margin-top:12px;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Upload Avatar <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required></label>
    <br><br>
    <button type="submit" class="btn"><i class="fa fa-upload"></i> Upload Image</button>
  </form>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
