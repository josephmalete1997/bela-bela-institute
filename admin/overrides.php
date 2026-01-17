<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/tasks_model.php";
$overrides = db()->query("SELECT tro.*, t.title as task_title, u.full_name as user_name, g.full_name as granted_by_name FROM task_review_overrides tro LEFT JOIN tasks t ON t.id = tro.task_id LEFT JOIN users u ON u.id = tro.user_id LEFT JOIN users g ON g.id = tro.granted_by ORDER BY tro.created_at DESC")->fetchAll();
$users = db()->query("SELECT id, full_name as name FROM users ORDER BY full_name ASC")->fetchAll();
require __DIR__ . "/layout/header.php";
// small autocomplete script
?>
<main class="section"><div class="container">
  <h2>Review Overrides</h2>
  <h3>Grant Override</h3>
  <form method="post" action="api/grant_review_override.php">
    <label>Task ID <input name="task_id" required /></label>
    <label>User
      <input id="grant-user-search" placeholder="Search user name or email" autocomplete="off" />
      <input type="hidden" id="grant-user-id" name="user_id" />
    </label>
    <div id="grant-suggestions" style="position:relative"></div>
    <button class="btn">Grant</button>
  </form>
  <script>
  function attachAutocompleteToSearch(searchId, hiddenId, suggestionsId) {
    const input = document.getElementById(searchId);
    const hid = document.getElementById(hiddenId);
    const box = document.getElementById(suggestionsId);
    if (!input || !box || !hid) return;
    input.addEventListener('input', async () => {
      const q = input.value.trim();
      if (q.length < 2) { box.innerHTML = ''; hid.value = ''; return; }
      const res = await fetch('api/user_search.php?q=' + encodeURIComponent(q));
      const list = await res.json();
      box.innerHTML = '';
      list.forEach(u => {
        const btn = document.createElement('div');
        btn.textContent = u.name + ' ('+u.email+')';
        btn.style.padding = '6px 8px';
        btn.style.cursor = 'pointer';
        btn.addEventListener('click', () => { input.value = u.name; hid.value = u.id; box.innerHTML = ''; });
        box.appendChild(btn);
      });
    });
  }
  attachAutocompleteToSearch('grant-user-search','grant-user-id','grant-suggestions');
  </script>
  <h3 style="margin-top:18px;">Existing Overrides</h3>
  <table class="table" style="width:100%;border-collapse:collapse;">
    <thead><tr><th>Task</th><th>User</th><th>Granted By</th><th>Created</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach($overrides as $o): ?>
        <tr>
          <td><?= e($o['task_title'] ?? $o['task_id']) ?></td>
          <td><?= e($o['user_name']) ?> (<?= e($o['user_id']) ?>)</td>
          <td><?= e($o['granted_by_name'] ?? '') ?></td>
          <td><?= e($o['created_at']) ?></td>
          <td>
            <form method="post" action="api/revoke_review_override.php" style="display:inline;">
              <input type="hidden" name="task_id" value="<?= e($o['task_id']) ?>" />
              <input type="hidden" name="user_id" value="<?= e($o['user_id']) ?>" />
              <button class="btn">Revoke</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div></main>
<?php require __DIR__ . '/layout/footer.php';
