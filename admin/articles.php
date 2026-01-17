<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/articles_model.php";

// simple list with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 12;
$offset = ($page - 1) * $per;
$items = articles_find_all($per, $offset);
$total = articles_count_published();
$pages = (int)ceil($total / $per);

require __DIR__ . "/layout/header.php";
?>
<div class="admin-content">
  <div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <h2>Articles</h2>
      <a class="btn-admin btn-primary" href="article_edit.php">New Article</a>
    </div>

    <table class="admin-table" style="margin-top:16px;">
      <thead>
        <tr><th>Title</th><th>Published</th><th>Views</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach($items as $it): ?>
          <tr>
            <td><?= e($it['title']) ?></td>
            <td><?= $it['is_published'] ? e($it['published_at'] ?? $it['created_at']) : 'Draft' ?></td>
            <td><?= (int)$it['views'] ?></td>
            <td style="text-align:right;">
              <a href="article_edit.php?id=<?= (int)$it['id'] ?>">Edit</a> |
              <a href="article_delete.php?id=<?= (int)$it['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</div>
<?php require __DIR__ . "/layout/footer.php";