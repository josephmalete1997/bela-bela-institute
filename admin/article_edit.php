<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('admin');
require_once __DIR__ . "/../includes/articles_model.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$article = $id ? articles_find($id) : null;

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $excerpt = $_POST['excerpt'] ?? '';
  $content = $_POST['content'] ?? '';
  $is_published = isset($_POST['is_published']) ? 1 : 0;
  $published_at = $_POST['published_at'] ?? null;

  // handle featured image upload
  $featured = $article['featured_image'] ?? null;
  if (!empty($_FILES['featured_image']['name'])) {
    $up = $_FILES['featured_image'];
    $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
    $fn = 'uploads/articles/' . time() . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!is_dir(__DIR__ . '/../uploads/articles')) mkdir(__DIR__ . '/../uploads/articles', 0755, true);
    if (move_uploaded_file($up['tmp_name'], __DIR__ . '/../' . $fn)) {
      $featured = $fn;
    }
  }

  $data = [
    'title' => $title,
    'excerpt' => $excerpt,
    'content' => $content,
    'featured_image' => $featured,
    'is_published' => $is_published,
    'published_at' => $published_at ?: null,
    'author_id' => auth_user()['id'] ?? null,
  ];

  if ($id) {
    articles_update($id, $data);
    redirect('./articles');
  } else {
    $newId = articles_create($data);
    redirect('./articles');
  }
}

require __DIR__ . "/layout/header.php";
?>
<div class="admin-content">
  <div class="admin-card">
    <h2><?= $article ? 'Edit Article' : 'New Article' ?></h2>

    <form method="post" enctype="multipart/form-data" class="admin-form">
      <label>Title
        <input name="title" value="<?= e($article['title'] ?? '') ?>" required>
      </label>

      <label>Excerpt
        <textarea name="excerpt"><?= e($article['excerpt'] ?? '') ?></textarea>
      </label>

      <label>Content
        <textarea name="content" rows="12"><?= e($article['content'] ?? '') ?></textarea>
      </label>

      <label>Featured image
        <input type="file" name="featured_image" accept="image/*">
      </label>
      <?php if (!empty($article['featured_image'])): ?>
        <div style="margin:8px 0;"><img src="<?= e($article['featured_image']) ?>" alt="" style="max-width:220px;border-radius:8px;"></div>
      <?php endif; ?>

      <label>
        <input type="checkbox" name="is_published" <?= (!empty($article['is_published']) ? 'checked' : '') ?>> Publish now
      </label>

      <label>Publish at (optional)
        <input type="datetime-local" name="published_at" value="<?= e(!empty($article['published_at']) ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '') ?>">
      </label>

      <div style="margin-top:12px;">
        <button class="btn-admin btn-primary">Save</button>
        <a class="btn-admin btn-secondary" href="./articles">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . "/layout/footer.php";
?>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea[name="content"]',
    menubar: false,
    height: 420,
    plugins: 'link image media code lists table',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code',
    content_css: '/css/style.css',
    relative_urls: false,
    remove_script_host: false
  });
</script>