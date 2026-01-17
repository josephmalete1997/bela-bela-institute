<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/articles_model.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
  http_response_code(404);
  echo "Article not found.";
  exit;
}

$article = articles_find_by_slug($slug);
if (!$article) {
  http_response_code(404);
  echo "Article not found.";
  exit;
}

// prepare meta for header
$meta_title = $article['title'];
$meta_description = $article['excerpt'] ?? strip_tags(substr($article['content'], 0, 160));
$meta_og_image = !empty($article['featured_image']) ? $article['featured_image'] : null;

// increment views
articles_increment_views((int)$article['id']);

require_once __DIR__ . '/includes/header.php';

?>
<main class="section">
  <div class="container">
    <div class="section-head">
      <h1><?= e($article['title']) ?></h1>
      <div style="color:var(--muted);margin-bottom:12px;">Published: <?= e($article['published_at'] ?? $article['created_at']) ?> • Views: <?= (int)$article['views'] + 1 ?></div>
    </div>

    <div class="card">
      <?php if (!empty($article['featured_image'])): ?>
        <img src="<?= e($article['featured_image']) ?>" alt="" style="width:100%;max-height:420px;object-fit:cover;border-radius:8px;margin-bottom:12px;">
      <?php endif; ?>

      <div class="content">
        <?= $article['content'] ?>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php';
