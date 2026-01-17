USE belabela_iHL;

-- Seed sample articles (safe to run multiple times)

INSERT INTO articles (title,slug,excerpt,content,author_id,featured_image,tags,is_published,published_at)
SELECT * FROM (
  SELECT 'Bela-Bela Institute Opens New Intake' AS title, 'new-intake' AS slug, 'Weekend and evening classes now available. Enrol today.' AS excerpt, '<p>We are excited to announce new intake dates for our practical ICT courses. Classes available on weekends and evenings to suit working learners.</p>' AS content, 1 AS author_id, 'uploads/articles/intake.jpg' AS featured_image, JSON_ARRAY('news','intake') AS tags, 1 AS is_published, NOW() AS published_at
) AS tmp
WHERE NOT EXISTS(SELECT 1 FROM articles WHERE slug = 'new-intake') LIMIT 1;

INSERT INTO articles (title,slug,excerpt,content,author_id,featured_image,tags,is_published,published_at)
SELECT * FROM (
  SELECT 'Student Project Showcase' AS title, 'student-project-showcase' AS slug, 'See the projects our learners built this term.' AS excerpt, '<p>Our students recently completed capstone projects that showcase their skills in web and data development.</p>' AS content, 1 AS author_id, 'uploads/articles/projects.jpg' AS featured_image, JSON_ARRAY('students','projects') AS tags, 1 AS is_published, NOW() - INTERVAL 7 DAY AS published_at
) AS tmp
WHERE NOT EXISTS(SELECT 1 FROM articles WHERE slug = 'student-project-showcase') LIMIT 1;

INSERT INTO articles (title,slug,excerpt,content,author_id,featured_image,tags,is_published,published_at)
SELECT * FROM (
  SELECT 'Free Workshop: Intro to Python' AS title, 'intro-to-python-workshop' AS slug, 'Join our free workshop to learn Python basics.' AS excerpt, '<p>Sign up for a hands-on introduction to Python programming suitable for beginners. Limited seats available.</p>' AS content, 1 AS author_id, 'uploads/articles/python.jpg' AS featured_image, JSON_ARRAY('workshop','python') AS tags, 1 AS is_published, NOW() - INTERVAL 14 DAY AS published_at
) AS tmp
WHERE NOT EXISTS(SELECT 1 FROM articles WHERE slug = 'intro-to-python-workshop') LIMIT 1;
