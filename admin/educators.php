<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $action = $_POST['action'] ?? '';
  if ($action === 'create') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || $email === '' || $password === '') {
      $errors[] = 'All fields are required.';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = db()->prepare("INSERT INTO users (full_name,email,password_hash,role,status) VALUES (:name,:email,:hash,'educator','active')");
      try {
        $stmt->execute([':name'=>$name, ':email'=>$email, ':hash'=>$hash]);
      } catch (Throwable $e) {
        $errors[] = 'Failed to create educator. Email may already exist.';
      }
    }
  } elseif ($action === 'assign') {
    $educator_id = (int)($_POST['educator_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    if ($educator_id && $course_id) {
      $stmt = db()->prepare("INSERT IGNORE INTO course_educators (course_id, educator_id) VALUES (:cid, :eid)");
      $stmt->execute([':cid'=>$course_id, ':eid'=>$educator_id]);
    }
  } elseif ($action === 'remove') {
    $educator_id = (int)($_POST['educator_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    if ($educator_id && $course_id) {
      $stmt = db()->prepare("DELETE FROM course_educators WHERE course_id = :cid AND educator_id = :eid");
      $stmt->execute([':cid'=>$course_id, ':eid'=>$educator_id]);
    }
  }
  redirect('educators.php');
}

$educators = db()->query("SELECT id, full_name, email FROM users WHERE role = 'educator' ORDER BY full_name")->fetchAll();
$courses = db()->query("SELECT id, title FROM courses ORDER BY title")->fetchAll();
$assignments = db()->query("
  SELECT ce.course_id, ce.educator_id, c.title AS course_title, u.full_name AS educator_name
  FROM course_educators ce
  JOIN courses c ON c.id = ce.course_id
  JOIN users u ON u.id = ce.educator_id
  ORDER BY u.full_name, c.title
")->fetchAll();

require __DIR__ . "/layout/header.php";
?>
<div class="admin-card">
  <h2>Educators</h2>
  <?php if ($errors): ?>
    <div style="color:#b91c1c;margin-bottom:10px;">
      <?php foreach ($errors as $e): ?><div><?= e($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div class="admin-card">
  <h3>Add Educator</h3>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <label>Full name<input name="full_name" required></label>
    <label>Email<input name="email" type="email" required></label>
    <label>Temporary password<input name="password" type="password" required></label>
    <button class="btn-admin btn-primary">Create Educator</button>
  </form>
</div>

<div class="admin-card">
  <h3>Assign Courses</h3>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="assign">
    <label>Educator<select name="educator_id" required>
      <option value="">-- select educator --</option>
      <?php foreach ($educators as $e): ?>
        <option value="<?= (int)$e['id'] ?>"><?= e($e['full_name']) ?> (<?= e($e['email']) ?>)</option>
      <?php endforeach; ?>
    </select></label>
    <label>Course<select name="course_id" required>
      <option value="">-- select course --</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option>
      <?php endforeach; ?>
    </select></label>
    <button class="btn-admin btn-primary">Assign Course</button>
  </form>
</div>

<div class="admin-card">
  <h3>Current Assignments</h3>
  <?php if (!$assignments): ?>
    <p>No course assignments yet.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Educator</th>
          <th>Course</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($assignments as $a): ?>
          <tr>
            <td><?= e($a['educator_name']) ?></td>
            <td><?= e($a['course_title']) ?></td>
            <td>
              <form method="post" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="educator_id" value="<?= (int)$a['educator_id'] ?>">
                <input type="hidden" name="course_id" value="<?= (int)$a['course_id'] ?>">
                <button class="btn-admin btn-secondary" type="submit">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . "/layout/footer.php"; ?>
