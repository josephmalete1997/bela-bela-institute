USE belabela_iHL;

-- If an `articles` table already exists, keep it; if `blog_posts` exists, rename it.
-- This script will drop an empty `articles` table to allow a rename if necessary.

SET @has_blog = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'belabela_iHL' AND table_name = 'blog_posts');
SET @has_articles = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'belabela_iHL' AND table_name = 'articles');

-- If blog_posts exists and articles does not, rename.
-- If articles exists already, do nothing.

IF @has_blog = 1 AND @has_articles = 0 THEN
  RENAME TABLE blog_posts TO articles;
END IF;
