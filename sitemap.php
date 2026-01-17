<?php
header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/includes/articles_model.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = rtrim($scheme . '://' . $host . dirname($_SERVER['SCRIPT_NAME']), '/');

// fetch up to 1000 published articles
$articles = articles_find_all(1000, 0);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// homepage
echo "  <url>\n    <loc>{$base}/</loc>\n    <changefreq>daily</changefreq>\n    <priority>1.0</priority>\n  </url>\n";

// static pages
$staticPages = [
  '/about.php',
  '/programs.php',
  '/admissions.php',
  '/articles.php',
  '/contact.php',
  '/apply.php',
];
foreach ($staticPages as $page) {
  echo "  <url>\n    <loc>{$base}{$page}</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
}

foreach ($articles as $a) {
  $loc = $base . '/article.php?slug=' . urlencode($a['slug']);
  $last = date('Y-m-d', strtotime($a['published_at'] ?? $a['created_at']));
  echo "  <url>\n    <loc>{$loc}</loc>\n    <lastmod>{$last}</lastmod>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";
}

echo '</urlset>';
