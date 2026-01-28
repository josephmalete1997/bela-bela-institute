<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $task_id = (int)($_POST['task_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    if ($task_id && in_array($new_status, ['studying', 'in_review', 'completed'], true)) {
        $user_id = (int)($_SESSION['user']['id'] ?? 0);
        if ($user_id) {
            tasks_update_status_for_user($task_id, $user_id, $new_status, 0);
        }
    }
    redirect('tasks_board.php');
}

$statuses = tasks_list_statuses();

// Get enrolled course IDs for the current student
$user = auth_user();
$user_id = (int)($user['id'] ?? 0);
if (!$user_id) {
    redirect("../public/login.php");
}

$enrolled_course_ids = [];
try {
    $stmt = db()->prepare("
        SELECT DISTINCT c.id 
        FROM enrollments e 
        JOIN intakes i ON e.intake_id = i.id 
        JOIN courses c ON i.course_id = c.id 
        WHERE e.user_id = ? AND e.status = 'enrolled'
    ");
    $stmt->execute([$user_id]);
    $enrolled_course_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    log_error("Failed to load enrolled courses", ["user_id" => $user_id, "error" => $e->getMessage()]);
}

$board = [];
$all_tasks = tasks_find_all_for_user($user_id);
foreach ($statuses as $s) {
    $board[$s] = array_filter($all_tasks, function($task) use ($enrolled_course_ids, $s) {
        $status = $task['user_status'] ?? ($task['status'] ?? 'backlog');
        if ($status !== $s) return false;
        return $task['course_id'] === null || in_array($task['course_id'], $enrolled_course_ids);
    });
}

require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Your Tasks & Peer Review</h2>

    <div id="board" class="kanban-board">
      <?php foreach ($board as $col => $cards): ?>
        <section class="kanban-column" data-col="<?= e($col) ?>">
          <h3><?= e($col) ?></h3>
          <div class="kanban-list" data-col="<?= e($col) ?>">
            <?php foreach ($cards as $card): ?>
              <?php
              $card_status = $card['user_status'] ?? ($card['status'] ?? 'backlog');
              $assigned_reviewers = [];
              if ($card['type'] === 'project') {
                $summary = tasks_get_review_summary((int)$card['id'], (int)$user_id);
                $assigned_reviewers = tasks_list_assigned_reviewers((int)$card['id'], (int)$user_id);
                if ($card_status === 'in_review' && $summary['not_competent'] > 0) {
                  tasks_update_status_for_user((int)$card['id'], (int)$user_id, 'review_feedback', 0);
                  $card_status = 'review_feedback';
                }
              }
              ?>
              <div class="kanban-card <?= $card['type'] === 'topic' ? 'card-topic' : 'card-project' ?>" draggable="true" data-id="<?= (int)$card['id'] ?>">
                <strong class="kanban-card-title"><?= e($card['title'] ?: 'Untitled Task') ?></strong>
                <div class="kanban-meta">Type: <?= e($card['type']) ?> • By: <?= e((string)($card['submitter_id'] ?? '')) ?></div>
                <?php if ($card['type'] === 'project'): ?>
                  <?php if (!empty($assigned_reviewers)): ?>
                    <div class="kanban-meta reviewers">
                      Reviewers:
                      <?php foreach ($assigned_reviewers as $rev): ?>
                        <button
                          type="button"
                          class="reviewer-chip"
                          data-task-id="<?= (int)$card['id'] ?>"
                          data-submitter-id="<?= (int)$user_id ?>"
                          data-reviewer-id="<?= (int)($rev['id'] ?? 0) ?>"
                          data-reviewer-name="<?= e($rev['full_name'] ?? '') ?>">
                          <?= e($rev['full_name'] ?? 'Reviewer') ?>
                        </button>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <div class="kanban-meta reviewers">Reviewers: None yet</div>
                  <?php endif; ?>
                <?php endif; ?>
                <div class="kanban-actions">
                  <a href="task_view.php?id=<?= (int)$card['id'] ?>" class="btn btn-small">View</a>
                  <?php if ($card_status === 'backlog'): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="task_id" value="<?= (int)$card['id'] ?>">
                      <input type="hidden" name="new_status" value="studying">
                      <button type="submit" class="btn btn-small">Start Task</button>
                    </form>
                  <?php elseif ($card_status === 'studying'): ?>
                    <?php if ($card['type'] === 'topic'): ?>
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="task_id" value="<?= (int)$card['id'] ?>">
                        <input type="hidden" name="new_status" value="completed">
                        <button type="submit" class="btn btn-small">Mark Complete</button>
                      </form>
                    <?php elseif ($card['type'] === 'project'): ?>
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="task_id" value="<?= (int)$card['id'] ?>">
                        <input type="hidden" name="new_status" value="in_review">
                        <button type="submit" class="btn btn-small">Move to Review</button>
                      </form>
                    <?php endif; ?>
                  <?php elseif ($card_status === 'in_review'): ?>
                    <?php if ($card['type'] === 'project'): ?>
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="task_id" value="<?= (int)$card['id'] ?>">
                        <input type="hidden" name="new_status" value="studying">
                        <button type="submit" class="btn btn-small">Cancel</button>
                      </form>
                    <?php else: ?>
                      <a href="task_view.php?id=<?= (int)$card['id'] ?>&submitter_id=<?= (int)$user_id ?>" class="btn btn-small">Review</a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  </div>

<div class="modal" id="messageModal" aria-hidden="true">
  <div class="modal-card">
    <h3>Message Reviewer</h3>
    <form method="post" action="../api/task_message.php" id="messageForm">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="task_id" id="msgTaskId">
      <input type="hidden" name="submitter_id" id="msgSubmitterId">
      <input type="hidden" name="recipient_id" id="msgReviewerId">
      <label>To
        <input type="text" id="msgReviewerName" readonly>
      </label>
      <label>Message
        <textarea name="message" required></textarea>
      </label>
      <div class="kanban-actions">
        <button type="submit" class="btn btn-small">Send</button>
        <button type="button" class="btn btn-small btn-ghost" id="closeMessageModal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</main>

<script>
let dragged = null;
document.querySelectorAll('.kanban-card').forEach(c => {
  c.addEventListener('dragstart', e => { dragged = c; c.style.opacity = '0.6'; });
  c.addEventListener('dragend', e => { dragged = null; c.style.opacity = ''; });
});

const modal = document.getElementById('messageModal');
const closeBtn = document.getElementById('closeMessageModal');
document.querySelectorAll('.reviewer-chip').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('msgTaskId').value = btn.dataset.taskId || '';
    document.getElementById('msgSubmitterId').value = btn.dataset.submitterId || '';
    document.getElementById('msgReviewerId').value = btn.dataset.reviewerId || '';
    document.getElementById('msgReviewerName').value = btn.dataset.reviewerName || 'Reviewer';
    modal.classList.add('open');
  });
});
closeBtn?.addEventListener('click', () => modal.classList.remove('open'));
modal?.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('open'); });

const lists = document.querySelectorAll('.kanban-list');
lists.forEach(list => {
  list.addEventListener('dragover', e => { e.preventDefault(); list.classList.add('drag-over'); });
  list.addEventListener('dragleave', e => { list.classList.remove('drag-over'); });
  list.addEventListener('drop', e => {
    e.preventDefault(); list.classList.remove('drag-over');
    if (!dragged) return;
    list.appendChild(dragged);
    // send ordering
    const cols = {};
    document.querySelectorAll('.kanban-list').forEach(l => {
      const col = l.getAttribute('data-col');
      const ids = Array.from(l.querySelectorAll('.kanban-card')).map(c => c.getAttribute('data-id'));
      cols[col] = ids;
    });
    fetch('../admin/api/update_task_order.php', {
      method: 'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ columns: cols, csrf: '<?= e(csrf_token()) ?>' })
    }).then(r=>r.json()).then(j=>{ if(!j.success) alert('Save failed'); }).catch(()=>alert('Save failed'));
  });
});
</script>

<?php require __DIR__ . "/../includes/footer.php";
require __DIR__ . "/layout/footer.php";?>







