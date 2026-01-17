<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/tasks_model.php";
// fetch tasks - tasks are visible to all enrolled students in the course
$tasks = db()->query("SELECT t.*, c.title as course_title, u.full_name as submitter_name FROM tasks t LEFT JOIN courses c ON c.id = t.course_id LEFT JOIN users u ON u.id = t.submitter_id ORDER BY t.created_at DESC")->fetchAll();
require __DIR__ . "/layout/header.php";
?>
<main class="section"><div class="container">
  <h2>Course Tasks</h2>
  <p>
    <a class="btn" href="task_create.php"><i class="fa fa-plus"></i> Create Task</a>
    <a class="btn" href="task_edit.php"><i class="fa fa-edit"></i> Edit Task</a>
    <a class="btn" href="task_import.php"><i class="fa fa-file-import"></i> Import CSV</a>
  </p>
  <table class="table" style="width:100%;border-collapse:collapse;margin-top:12px;">
    <thead><tr><th>ID</th><th>Title</th><th>Course</th><th>Type</th><th>Status</th><th>Submitter</th><th>Enrolled Students</th><th>Position</th><th>URL</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($tasks as $t): ?>
        <tr>
          <td><?= e($t['id']) ?></td>
          <td><?= e($t['title']) ?></td>
          <td><?= e($t['course_title'] ?? '-') ?></td>
          <td><?= e($t['type']) ?></td>
          <td><?= e($t['status']) ?></td>
          <td><?= e($t['submitter_name'] ?? '-') ?></td>
          <td><?= $t['course_id'] ? 'All Enrolled' : '-' ?></td>
          <td><?= e($t['position']) ?></td>
          <td><?php if ($t['url']): ?><a href="<?= e($t['url']) ?>" target="_blank">Link</a><?php else: ?>-<?php endif; ?></td>
          <td><?= e($t['created_at']) ?></td>
          <td>
            <a class="btn" href="task_edit.php?id=<?= e($t['id']) ?>">Edit</a>
            <form method="post" action="api/delete_task.php" style="display:inline">
              <input type="hidden" name="id" value="<?= e($t['id']) ?>" />
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
              <button class="btn" onclick="return confirm('Delete task?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div></main>
<?php require __DIR__ . "/layout/footer.php"; ?>
