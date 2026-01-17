<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/articles_model.php';
require_once __DIR__ . '/includes/header.php';

$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 6;
$offset = ($page - 1) * $per;
if ($q !== '') {
  $articles = articles_search($q, $per, $offset);
  $total = articles_count_search($q);
} else {
  $articles = articles_find_all($per, $offset);
  $total = articles_count_published();
}
$pages = $total ? (int)ceil($total / $per) : 1;

?>

<main class="section">
  <div class="container">
    <div class="section-head">
      <h2>News &amp; Articles</h2>
      <p>Latest news, updates and articles from Bela-Bela Institute.</p>
    </div>

    <div style="margin-bottom:18px;">
      <form method="get" action="articles.php">
        <input type="search" name="q" placeholder="Search news and articles" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" style="padding:8px 10px;border:1px solid var(--line;border-radius:8px;width:60%;max-width:420px;"> 
        <button class="btn btn-small" type="submit">Search</button>
      </form>
    </div>

    <div class="grid cards posts">
      <?php if (empty($articles)): ?>
        <div class="note">No articles found.</div>
      <?php endif; ?>
      <?php foreach ($articles as $a): ?>
        <article class="card">
          <?php if (!empty($a['featured_image'])): ?>
            <img src="<?= htmlspecialchars($a['featured_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>" width="100%">
          <?php endif; ?>
          <h3><a href="article.php?slug=<?= htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></a></h3>
          <p class="lead"><?= htmlspecialchars($a['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <nav aria-label="Pagination" style="margin-top:18px;display:flex;gap:8px;align-items:center;">
        <?php for ($p=1; $p <= $pages; $p++): ?>
          <?php $link = 'articles.php?page=' . $p . ($q !== '' ? '&q=' . urlencode($q) : ''); ?>
          <a class="btn-ghost" href="<?= $link ?>" style="padding:8px 10px;<?= $p === $page ? 'font-weight:700;border-color:var(--brand2);' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php';
?>
