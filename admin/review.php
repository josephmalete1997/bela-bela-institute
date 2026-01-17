<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_any_role(['admin','educator']);
require_once __DIR__ . "/../includes/tasks_model.php";

$id = (int)($_GET['id'] ?? 0);
$submitter_id = (int)($_GET['submitter_id'] ?? 0);
if (!$id || !$submitter_id) { http_response_code(400); echo 'Invalid request'; exit; }

$task = tasks_find($id);
if (!$task) { http_response_code(404); echo 'Not found'; exit; }

$role = auth_user()['role'] ?? 'admin';
if ($role === 'educator') {
  $course_id = (int)($task['course_id'] ?? 0);
  if (!$course_id || !tasks_is_course_educator($course_id, (int)auth_user()['id'])) {
    http_response_code(403);
    exit('Forbidden');
  }
}

$error = '';
$task_status = tasks_get_user_status($id, $submitter_id) ?? ($task['status'] ?? 'backlog');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $comment = $_POST['comment'] ?? '';
  $is_competent = isset($_POST['is_competent']) ? 1 : null;
  $rid = tasks_add_review($id, $submitter_id, auth_user()['id'], $comment, $is_competent);
  if ($rid === 0) {
    $error = 'Unable to save review.';
  } else {
    if ($task['type'] === 'project') {
      $summary = tasks_get_review_summary($id, $submitter_id);
      if ($summary['not_competent'] > 0) {
        tasks_update_status_for_user($id, $submitter_id, 'review_feedback', 0);
      } elseif ($summary['competent'] >= 3) {
        tasks_update_status_for_user($id, $submitter_id, 'completed', 0);
      } else {
        tasks_update_status_for_user($id, $submitter_id, 'in_review', 0);
      }
    }
    redirect('reviews.php');
  }
}

$reviews = tasks_get_reviews($id, $submitter_id);
$messages = tasks_get_messages($id, $submitter_id);
require __DIR__ . "/layout/header.php";
?>
<div class="admin-card">
  <h2><?= e($task['title']) ?></h2>
  <p>Type: <?= e($task['type']) ?> • Status: <?= e($task_status) ?></p>
  <div class="admin-card" style="margin-top:12px;">
    <?= nl2br(e($task['description'] ?? '')) ?>
  </div>
</div>

<div class="admin-card">
  <h3>Reviews</h3>
  <?php if (!$reviews): ?>
    <p>No reviews yet.</p>
  <?php else: ?>
    <?php foreach ($reviews as $r): ?>
      <div style="border:1px solid #eef2f7;padding:10px;border-radius:8px;margin-bottom:8px;">
        <div style="font-weight:700"><?= e($r['reviewer_name'] ?? 'Anon') ?> <small style="color:#64748b;"><?= e($r['created_at']) ?></small></div>
        <div style="margin-top:6px;"><?= nl2br(e($r['comment'])) ?></div>
        <div style="margin-top:8px;color:<?= $r['is_competent'] ? '#16a34a' : '#e11d48' ?>;"><?= is_null($r['is_competent']) ? 'Feedback' : ($r['is_competent'] ? 'Marked Competent' : 'Not Competent') ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="admin-card">
  <h3>Messages</h3>
  <?php if (!$messages): ?>
    <p>No messages yet.</p>
  <?php else: ?>
    <?php foreach ($messages as $m): ?>
      <div style="border:1px solid #eef2f7;padding:10px;border-radius:8px;margin-bottom:8px;">
        <div style="font-weight:700"><?= e($m['sender_name'] ?? 'User') ?> <small style="color:#64748b;"><?= e($m['created_at'] ?? '') ?></small></div>
        <div style="margin-top:6px;"><?= nl2br(e($m['message'] ?? '')) ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form method="post" action="../api/task_message.php" style="margin-top:10px;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="task_id" value="<?= (int)$id ?>">
    <input type="hidden" name="submitter_id" value="<?= (int)$submitter_id ?>">
    <input type="hidden" name="recipient_id" value="<?= (int)$submitter_id ?>">
    <label>Reply to student<textarea name="message" required></textarea></label>
    <div><button class="btn-admin btn-primary">Send Message</button></div>
  </form>
</div>

<div class="admin-card">
  <h3>Add Review</h3>
  <?php if (!empty($error)): ?>
    <div style="color:#b91c1c;margin-bottom:8px;"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <label>Comment<textarea name="comment" required></textarea></label>
    <label><input type="checkbox" name="is_competent"> Mark as Competent</label>
    <div><button class="btn-admin btn-primary">Submit Review</button></div>
  </form>
</div>

<?php require __DIR__ . "/layout/footer.php"; ?>
