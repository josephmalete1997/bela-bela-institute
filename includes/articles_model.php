<?php

require_once __DIR__ . "/../app/db.php";
require_once __DIR__ . "/../app/helpers.php";

function articles_find_all(int $limit = 10, int $offset = 0): array {
  $stmt = db()->prepare("SELECT * FROM articles WHERE is_published = 1 ORDER BY published_at DESC, id DESC LIMIT :limit OFFSET :offset");
  $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
}

function articles_search(string $q, int $limit = 10, int $offset = 0): array {
  $like = '%' . str_replace('%', '\\%', $q) . '%';
  $stmt = db()->prepare("SELECT * FROM articles WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q) ORDER BY published_at DESC, id DESC LIMIT :limit OFFSET :offset");
  $stmt->bindValue(':q', $like, PDO::PARAM_STR);
  $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
}

function articles_count_search(string $q): int {
  $like = '%' . str_replace('%', '\\%', $q) . '%';
  $stmt = db()->prepare("SELECT COUNT(*) as c FROM articles WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q)");
  $stmt->execute([':q' => $like]);
  $r = $stmt->fetch();
  return (int)($r['c'] ?? 0);
}

function articles_count_published(): int {
  $stmt = db()->query("SELECT COUNT(*) as c FROM articles WHERE is_published = 1");
  $r = $stmt->fetch();
  return (int)($r['c'] ?? 0);
}

function articles_find_by_slug(string $slug): ?array {
  $stmt = db()->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
  $stmt->execute([':slug' => $slug]);
  $r = $stmt->fetch();
  return $r ?: null;
}

function articles_find(int $id): ?array {
  $stmt = db()->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $id]);
  $r = $stmt->fetch();
  return $r ?: null;
}

function articles_create(array $data): int {
  $slug = $data['slug'] ?? slugify($data['title'] ?? '');
  // ensure unique slug
  $base = $slug; $i = 1;
  while (true) {
    $stmt = db()->prepare("SELECT id FROM articles WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    if (!$stmt->fetch()) break;
    $slug = $base . '-' . $i++;
  }

  $stmt = db()->prepare("INSERT INTO articles (title,slug,excerpt,content,author_id,featured_image,tags,is_published,published_at) VALUES (:title,:slug,:excerpt,:content,:author_id,:featured_image,:tags,:is_published,:published_at)");
  $stmt->execute([
    ':title' => $data['title'] ?? '',
    ':slug' => $slug,
    ':excerpt' => $data['excerpt'] ?? null,
    ':content' => $data['content'] ?? '',
    ':author_id' => $data['author_id'] ?? null,
    ':featured_image' => $data['featured_image'] ?? null,
    ':tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
    ':is_published' => $data['is_published'] ?? 0,
    ':published_at' => $data['published_at'] ?? null,
  ]);
  return (int)db()->lastInsertId();
}

function articles_update(int $id, array $data): bool {
  if (isset($data['slug'])) {
    $slug = $data['slug'];
  } else {
    $slug = slugify($data['title'] ?? '');
  }
  // ensure unique slug (allow current)
  $base = $slug; $i = 1;
  while (true) {
    $stmt = db()->prepare("SELECT id FROM articles WHERE slug = :slug AND id != :id LIMIT 1");
    $stmt->execute([':slug' => $slug, ':id' => $id]);
    if (!$stmt->fetch()) break;
    $slug = $base . '-' . $i++;
  }

  $stmt = db()->prepare("UPDATE articles SET title=:title, slug=:slug, excerpt=:excerpt, content=:content, author_id=:author_id, featured_image=:featured_image, tags=:tags, is_published=:is_published, published_at=:published_at WHERE id = :id");
  return $stmt->execute([
    ':title' => $data['title'] ?? '',
    ':slug' => $slug,
    ':excerpt' => $data['excerpt'] ?? null,
    ':content' => $data['content'] ?? '',
    ':author_id' => $data['author_id'] ?? null,
    ':featured_image' => $data['featured_image'] ?? null,
    ':tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
    ':is_published' => $data['is_published'] ?? 0,
    ':published_at' => $data['published_at'] ?? null,
    ':id' => $id,
  ]);
}

function articles_delete(int $id): bool {
  $stmt = db()->prepare("DELETE FROM articles WHERE id = :id");
  return $stmt->execute([':id' => $id]);
}

function articles_increment_views(int $id): void {
  $stmt = db()->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
  $stmt->execute([':id' => $id]);
}

