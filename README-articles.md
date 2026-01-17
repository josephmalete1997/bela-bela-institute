Articles table schema

Table: `articles`

- id: INT PK
- title: VARCHAR(255)
- slug: VARCHAR(255) UNIQUE
- excerpt: VARCHAR(500)
- content: LONGTEXT
- author_id: INT NULL (references users.id)
- featured_image: VARCHAR(255) NULL (relative path under /uploads)
- tags: JSON NULL (array of tags)
- is_published: TINYINT(1) DEFAULT 0
- published_at: DATETIME NULL
- views: INT DEFAULT 0
- created_at, updated_at: TIMESTAMP

Dev notes

- Run the migration `sql/blog_schema.sql` or `sql/schema.sql` to ensure `articles` table exists.
- Admin pages:
  - `admin/articles.php` - list
  - `admin/article_edit.php` - create/edit (handles image upload)
  - `admin/article_delete.php` - delete
- Public pages:
  - `articles.php` - listing
  - `article.php?slug=...` - single view
- API: `public/api/articles.php?action=list` or `?action=get&slug=...`

Image uploads are stored under `uploads/articles/` and the path saved in `featured_image` is relative to the site root.
