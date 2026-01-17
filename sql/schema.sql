CREATE DATABASE IF NOT EXISTS belabela_iHL CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE belabela_iHL;

-- USERS
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  phone VARCHAR(50) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student','educator') NOT NULL DEFAULT 'student',
  status ENUM('active','blocked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COURSES
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  image VARCHAR(255) NULL,
  highlights JSON NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  kanban_status VARCHAR(32) NOT NULL DEFAULT 'backlog',
  kanban_position INT NOT NULL DEFAULT 0,
  fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COURSE EDUCATORS
CREATE TABLE course_educators (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  educator_id INT NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_course_educator (course_id, educator_id),
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (educator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- INTAKES (course runs)
CREATE TABLE intakes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  schedule VARCHAR(140) NOT NULL,  -- e.g. "Weekends" / "Evenings"
  seats INT NOT NULL DEFAULT 20,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- APPLICATIONS
CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  intake_id INT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  phone VARCHAR(50) NULL,
  motivation TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (intake_id) REFERENCES intakes(id) ON DELETE SET NULL,
  INDEX (email),
  INDEX (status)
);

-- ENROLLMENTS
CREATE TABLE enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  intake_id INT NOT NULL,
  status ENUM('enrolled','completed','cancelled') NOT NULL DEFAULT 'enrolled',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_enroll (user_id, intake_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (intake_id) REFERENCES intakes(id) ON DELETE CASCADE
);

-- SEED ADMIN (change password after login)
-- password = Admin@12345
INSERT INTO users(full_name,email,phone,password_hash,role)
VALUES(
  'Site Admin',
  'admin@belabela.co.za',
  '0000000000',
  '$2y$10$H2fYVbXgF2Gm9k8bB7j6jOQq3j2h7lq6l0YcDkSxwq8vXl5yqJ7p2',
  'admin'
);

-- ARTICLES
CREATE TABLE IF NOT EXISTS articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt VARCHAR(500) NULL,
  content LONGTEXT NOT NULL,
  author_id INT NULL,
  featured_image VARCHAR(255) NULL,
  tags JSON NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATETIME NULL,
  views INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX (is_published),
  INDEX (published_at)
);

-- TASKS
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  type ENUM('topic','project') NOT NULL DEFAULT 'topic',
  description TEXT NULL,
  course_id INT NULL,
  submitter_id INT NULL,
  assigned_user_id INT NULL,
  status ENUM('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  position INT NOT NULL DEFAULT 0,
  url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE SET NULL
);

-- REVIEWS
CREATE TABLE IF NOT EXISTS task_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  reviewer_id INT NULL,
  comment TEXT NOT NULL,
  is_competent TINYINT(1) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- TASK REVIEW OVERRIDES
CREATE TABLE IF NOT EXISTS task_review_overrides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  user_id INT NOT NULL,
  granted_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_task_user (task_id,user_id),
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);

-- NOTIFICATIONS
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(191) NOT NULL,
  message TEXT,
  link VARCHAR(255) DEFAULT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- PAYMENTS
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  enrollment_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(50) NULL,
  transaction_id VARCHAR(255) NULL,
  paid_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);
