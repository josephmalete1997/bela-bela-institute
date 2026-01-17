<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

$statuses = tasks_list_statuses();
$board = [];
foreach ($statuses as $s) $board[$s] = tasks_find_all_by_status($s);

require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Your Tasks & Peer Review</h2>
    <p><a href="task_submit.php" class="btn btn-small"><i class="fa fa-plus"></i> Submit Task</a></p>

    <div id="board" style="display:flex;gap:12px;margin-top:12px;align-items:flex-start;">
      <?php foreach ($board as $col => $cards): ?>
        <section class="kanban-column" data-col="<?= e($col) ?>" style="flex:1;min-width:220px;background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #eef2f7;">
          <h3 style="margin-top:0;text-transform:capitalize;"><?= e($col) ?></h3>
          <div class="kanban-list" data-col="<?= e($col) ?>" style="min-height:80px;">
            <?php foreach ($cards as $card): ?>
              <div class="kanban-card" draggable="true" data-id="<?= (int)$card['id'] ?>" style="background:#fff;padding:10px;border-radius:8px;border:1px solid #e6eef8;margin-bottom:8px;box-shadow:0 6px 14px rgba(2,6,23,0.03);">
                <strong><?= e($card['title']) ?></strong>
                <div style="font-size:0.85rem;color:#64748b;margin-top:6px;">Type: <?= e($card['type']) ?> • By: <?= e((string)($card['submitter_id'] ?? '')) ?></div>
                <div style="margin-top:8px;font-size:0.8rem;color:#94a3b8;"><a href="task_view.php?id=<?= (int)$card['id'] ?>">View / Review</a></div>
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
    fetch('/admin/api/update_task_order.php', {
      method: 'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ columns: cols, csrf: '<?= e(csrf_token()) ?>' })
    }).then(r=>r.json()).then(j=>{ if(!j.success) alert('Save failed'); }).catch(()=>alert('Save failed'));
  });
});
</script>

<?php require __DIR__ . "/../includes/footer.php";
require __DIR__ . "/layout/footer.php";?>