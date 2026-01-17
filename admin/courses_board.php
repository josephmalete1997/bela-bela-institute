<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');

// fetch courses grouped by kanban_status
$stmt = db()->query("SELECT * FROM courses ORDER BY created_at DESC");
$all = $stmt->fetchAll();
$board = [
  'backlog' => [],
  'planned' => [],
  'ongoing' => [],
  'completed' => []
];
foreach ($all as $c) {
  $s = $c['kanban_status'] ?? 'backlog';
  if (!isset($board[$s])) $s = 'backlog';
  $board[$s][] = $c;
}

require __DIR__ . "/layout/header.php";
?>
<div class="admin-content">
  <div class="admin-card">
    <h2>Courses Board</h2>
    <p>Drag cards between columns to update status.</p>
    <div id="board" style="display:flex;gap:12px;margin-top:12px;align-items:flex-start;">
      <?php foreach ($board as $col => $cards): ?>
        <section class="kanban-column" data-col="<?= e($col) ?>" style="flex:1;min-width:220px;background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #eef2f7;">
          <h3 style="margin-top:0;text-transform:capitalize;"><?= e($col) ?></h3>
          <div class="kanban-list" data-col="<?= e($col) ?>" style="min-height:80px;">
            <?php foreach ($cards as $card): ?>
              <div class="kanban-card" draggable="true" data-id="<?= (int)$card['id'] ?>" style="background:#fff;padding:10px;border-radius:8px;border:1px solid #e6eef8;margin-bottom:8px;box-shadow:0 6px 14px rgba(2,6,23,0.03);">
                <strong><?= e($card['title']) ?></strong>
                <div style="font-size:0.85rem;color:#64748b;margin-top:6px;"><?= e(substr($card['description'] ?? '', 0, 120)) ?></div>
                <div style="margin-top:8px;font-size:0.8rem;color:#94a3b8;">ID: <?= (int)$card['id'] ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<style>
.kanban-list.drag-over { background: rgba(37,99,235,0.04); }
</style>

<script>
// Basic drag and drop
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
    // build columns payload (ids in order)
    const cols = {};
    document.querySelectorAll('.kanban-list').forEach(l => {
      const col = l.getAttribute('data-col');
      const ids = Array.from(l.querySelectorAll('.kanban-card')).map(c => c.getAttribute('data-id'));
      cols[col] = ids;
    });
    fetch('./api/update_course_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ columns: cols, csrf: '<?= e(csrf_token()) ?>' })
    }).then(r=>r.json()).then(j=>{
      if (!j.success) alert('Save failed');
    }).catch(()=>alert('Save failed'));
  });
});
</script>

<?php require __DIR__ . "/layout/footer.php";