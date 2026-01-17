<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
$u = auth_user();
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h1>Student Portal</h1>
  <p>Welcome back, <strong><?= e($u['full_name'] ?? 'Student') ?></strong></p>

  <div class="panel-grid" style="margin-top:16px;">
    <a class="card" href="tasks_board.php" style="text-decoration:none;color:inherit;"><div style="font-size:22px;color:var(--accent)"><i class="fa fa-clipboard-list"></i></div><h3>My Tasks</h3><p>Submit and track assignments.</p></a>
    <a class="card" href="task_submit.php" style="text-decoration:none;color:inherit;"><div style="font-size:22px;color:var(--accent)"><i class="fa fa-upload"></i></div><h3>Submit Project</h3><p>Create a new submission for review.</p></a>
    <a class="card" href="profile.php" style="text-decoration:none;color:inherit;"><div style="font-size:22px;color:var(--accent)"><i class="fa fa-user"></i></div><h3>Profile</h3><p>View and edit your profile.</p></a>
    <a class="card" href="/notifications.php" style="text-decoration:none;color:inherit;"><div style="font-size:22px;color:var(--accent)"><i class="fa fa-bell"></i></div><h3>Notifications</h3><p>View your notifications.</p></a>
  </div>

  <div style="margin-top:20px;">
    <h2>Your Courses</h2>
    <?php
    $stmt = db()->prepare("SELECT c.title, e.status FROM enrollments e JOIN intakes i ON i.id=e.intake_id JOIN courses c ON c.id=i.course_id WHERE e.user_id = :uid");
    $stmt->execute([':uid'=>$u['id']]);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      echo '<p>No course enrollments yet.</p>';
    } else {
      echo '<ul>';
      foreach ($rows as $r) {
        echo '<li>'.e($r['title']).' — '.e($r['status']).'</li>';
      }
      echo '</ul>';
    }
    ?>
  </div>
</div></main>
<?php require __DIR__ . "/layout/footer.php";