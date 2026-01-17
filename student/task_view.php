<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

$id = (int)($_GET['id'] ?? 0);
$task = tasks_find($id);
if (!$task) { http_response_code(404); echo 'Not found'; exit; }

// Check if student is enrolled in the course for this task (or if it's a general task)
$user_id = $_SESSION['user']['id'];
if ($task['course_id'] !== null) {
    $stmt = db()->prepare("
        SELECT 1 FROM enrollments e 
        JOIN intakes i ON e.intake_id = i.id 
        WHERE e.user_id = ? AND i.course_id = ? AND e.status = 'enrolled'
        LIMIT 1
    ");
    $stmt->execute([$user_id, $task['course_id']]);
    $enrolled = $stmt->fetch();

    if (!$enrolled) {
        http_response_code(403);
        echo 'Access denied: You are not enrolled in this course.';
        exit;
    }
}

$error = '';
$user_id = auth_user()['id'] ?? null;
$can_review = tasks_user_can_review($user_id, $id);
$can_review = $can_review && ($task['submitter_id'] != $user_id);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // submit review/comment
  if (!$can_review) {
    $error = 'You are not eligible to review this submission. Only students who have completed a project for this course (or admins) may review.';
  } else {
    $comment = $_POST['comment'] ?? '';
    $is_competent = isset($_POST['is_competent']) ? 1 : null;
    $rid = tasks_add_review($id, $user_id, $comment, $is_competent);
    if ($rid === 0) {
      $error = 'Unable to save review — you may not be eligible.';
    } else {
      // if reviewer marked competent -> move to completed (only for projects)
      if ($is_competent) {
        tasks_update_status_and_position($id, 'completed', 0);
      } else {
        tasks_update_status_and_position($id, 'review_feedback', 0);
      }
      redirect('tasks_board.php');
    }
  }
}

$reviews = tasks_get_reviews($id);
$eligible_list = [];
// build list of eligible reviewers for display (students who completed a project in this course + overrides + admins)
$pdo = db();
$stmtc = $pdo->prepare("SELECT DISTINCT u.id, u.full_name AS name, u.role FROM users u
  LEFT JOIN tasks t ON t.submitter_id = u.id AND t.course_id = :cid
  WHERE (t.status = 'completed' AND t.type = 'project') OR u.role = 'admin'");
$stmtc->execute([':cid' => $task['course_id'] ?? 0]);
$eligible_list = $stmtc->fetchAll();
$overrides = tasks_list_overrides_for_task($id);
require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2><?= e($task['title']) ?></h2>
    <p>Type: <?= e($task['type']) ?> • Status: <?= e($task['status']) ?></p>
    <?php if ($task['type'] === 'project' && !empty($task['url'])): ?>
      <p>Project URL: <a href="<?= e($task['url']) ?>" target="_blank" class="btn btn-small">View Content</a></p>
    <?php endif; ?>
    <div class="card" style="margin-bottom:12px;"> <?= nl2br(e($task['description'] ?? '')) ?> </div>

    <h3>Reviews</h3>
    <?php foreach ($reviews as $r): ?>
      <div style="border:1px solid #eef2f7;padding:10px;border-radius:8px;margin-bottom:8px;">
        <div style="font-weight:700"><?= e($r['reviewer_name'] ?? 'Anon') ?> <small style="color:#64748b;"><?= e($r['created_at']) ?></small></div>
        <div style="margin-top:6px;"><?= nl2br(e($r['comment'])) ?></div>
        <div style="margin-top:8px;color:<?= $r['is_competent'] ? '#16a34a' : '#e11d48' ?>;"><?= is_null($r['is_competent']) ? 'Feedback' : ($r['is_competent'] ? 'Marked Competent' : 'Not Competent') ?></div>
      </div>
    <?php endforeach; ?>

    <h3>Add Review / Feedback</h3>
    <h4>Eligible Reviewers</h4>
    <div style="margin-bottom:8px;">
      <?php foreach ($eligible_list as $el): ?>
        <div style="display:inline-block;background:#f1f5f9;padding:6px 10px;margin-right:6px;border-radius:6px;"><?= e($el['name']) ?> <small style="color:#64748b;">(<?= e($el['role']) ?>)</small></div>
      <?php endforeach; ?>
      <?php foreach ($overrides as $ov): ?>
        <div style="display:inline-block;background:#ecfccb;padding:6px 10px;margin-right:6px;border-radius:6px;"><?= e($ov['user_name']) ?> <small style="color:#65a30d;">(override)</small></div>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($error)): ?>
      <div style="color:#b91c1c;margin-bottom:8px;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($can_review): ?>
    <form method="post">
      <label>Comment<textarea name="comment" required></textarea></label>
      <label><input type="checkbox" name="is_competent"> Mark as Competent</label>
      <div><button class="btn">Submit Review</button></div>
    </form>
    <?php else: ?>
      <div style="color:#374151;">Only students who have completed a project for this course may submit reviews. If you believe this is an error contact an instructor.</div>
    <?php endif; ?>
    <?php if (auth_user()['role'] === 'admin'): ?>
      <h4>Admin: Grant / Revoke Overrides</h4>
      <form method="post" action="../admin/api/grant_review_override.php">
        <input type="hidden" name="task_id" value="<?= e($id) ?>" />
        <label>User to grant:
          <input id="grant-user-search" placeholder="Search user name or email" autocomplete="off" />
          <input type="hidden" id="grant-user-id" name="user_id" />
        </label>
        <div id="grant-suggestions" style="position:relative"></div>
        <button class="btn">Grant Override</button>
      </form>
      <form method="post" action="../admin/api/revoke_review_override.php" style="margin-top:8px;">
        <input type="hidden" name="task_id" value="<?= e($id) ?>" />
        <label>User to revoke:
          <input id="revoke-user-search" placeholder="Search override user" autocomplete="off" />
          <input type="hidden" id="revoke-user-id" name="user_id" />
        </label>
        <div id="revoke-suggestions" style="position:relative"></div>
        <button class="btn">Revoke Override</button>
      </form>
      <p style="margin-top:8px;"><a href="../admin/overrides.php">Manage all overrides</a></p>
    <?php endif; ?>
  </div>
</main>
<?php require __DIR__ . "/layout/footer.php";
// attach autocomplete for admin inputs
?>
<script>
async function fetchUsers(q){
  const res = await fetch('/admin/api/user_search.php?q=' + encodeURIComponent(q));
  return await res.json();
}
function attachAutocompleteToSearch(searchId, hiddenId, boxId) {
  const input = document.getElementById(searchId); const hid = document.getElementById(hiddenId); const box = document.getElementById(boxId);
  if(!input||!box||!hid) return;
  input.addEventListener('input', async ()=>{
    const q = input.value.trim(); if(q.length<2){box.innerHTML=''; hid.value=''; return}
    const list = await fetchUsers(q);
    box.innerHTML='';
    list.forEach(u=>{const d=document.createElement('div');d.textContent=u.name+' ('+ (u.email||u.id) +')';d.style.padding='6px';d.style.cursor='pointer';d.onclick=()=>{input.value=u.name; hid.value=u.id; box.innerHTML='';};box.appendChild(d)});
  })
}
attachAutocompleteToSearch('grant-user-search','grant-user-id','grant-suggestions');
attachAutocompleteToSearch('revoke-user-search','revoke-user-id','revoke-suggestions');
</script>