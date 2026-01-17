<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $task_id = (int)($_POST['task_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    if ($task_id && in_array($new_status, ['studying', 'in_review', 'completed'], true)) {
        tasks_update($task_id, ['status' => $new_status]);
    }
    redirect('tasks_board.php');
}

$statuses = tasks_list_statuses();

// Get enrolled course IDs for the current student
$user_id = $_SESSION['user']['id'];
$stmt = db()->prepare("
    SELECT DISTINCT c.id 
    FROM enrollments e 
    JOIN intakes i ON e.intake_id = i.id 
    JOIN courses c ON i.course_id = c.id 
    WHERE e.user_id = ? AND e.status = 'enrolled'
");
$stmt->execute([$user_id]);
$enrolled_course_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$board = [];
foreach ($statuses as $s) {
    $tasks = tasks_find_all_by_status($s);
    // Filter tasks to only those for enrolled courses or general tasks (no course_id)
    $board[$s] = array_filter($tasks, function($task) use ($enrolled_course_ids) {
        return $task['course_id'] === null || in_array($task['course_id'], $enrolled_course_ids);
    });
}

require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Your Tasks & Peer Review</h2>

    <div id="board" style="display:flex;gap:12px;margin-top:12px;align-items:flex-start;">
      <?php foreach ($board as $col => $cards): ?>
        <section class="kanban-column" data-col="<?= e($col) ?>" style="flex:1;min-width:220px;background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #eef2f7;">
          <h3 style="margin-top:0;text-transform:capitalize;"><?= e($col) ?></h3>
          <div class="kanban-list" data-col="<?= e($col) ?>" style="min-height:80px;">
            <?php foreach ($cards as $card): ?>
              <div class="kanban-card <?= $card['type'] === 'topic' ? 'card-topic' : 'card-project' ?>" draggable="true" data-id="<?= (int)$card['id'] ?>" style="padding:10px;border-radius:8px;border:1px solid #e6eef8;margin-bottom:8px;box-shadow:0 6px 14px rgba(2,6,23,0.03);">
                <strong><?= e($card['title']) ?></strong>
                <div style="font-size:0.85rem;color:#64748b;margin-top:6px;">Type: <?= e($card['type']) ?> • By: <?= e((string)($card['submitter_id'] ?? '')) ?></div>
                <?php if ($card['type'] === 'project'): ?>
                  <?php
                  $stmt_rev = db()->prepare("SELECT u.email FROM task_reviews tr JOIN users u ON u.id = tr.reviewer_id WHERE tr.task_id = ?");
                  $stmt_rev->execute([$card['id']]);
                  $reviewers = $stmt_rev->fetchAll(PDO::FETCH_COLUMN);
                  if ($reviewers): ?>
                    <div style="font-size:0.8rem;color:#94a3b8;margin-top:4px;">Reviewers: <?= implode(', ', array_map('e', $reviewers)) ?></div>
                  <?php endif; ?>
                <?php endif; ?>
                <div style="margin-top:8px;font-size:0.8rem;">
                  <a href="task_view.php?id=<?= (int)$card['id'] ?>" class="btn btn-small" style="margin-right:4px;">View</a>
                  <?php if ($card['status'] === 'backlog'): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="task_id" value="<?= (int)$card['id'] ?>">
                      <input type="hidden" name="new_status" value="studying">
                      <button type="submit" class="btn btn-small">Start Task</button>
                    </form>
                  <?php elseif ($card['status'] === 'studying'): ?>
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
                  <?php elseif ($card['status'] === 'in_review'): ?>
                    <a href="task_view.php?id=<?= (int)$card['id'] ?>" class="btn btn-small">Review</a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<script>
let dragged = null;
document.querySelectorAll('.kanban-card').forEach(c => {
  c.addEventListener('dragstart', e => { dragged = c; c.style.opacity = '0.6'; });
  c.addEventListener('dragend', e => { dragged = null; c.style.opacity = ''; });
});

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