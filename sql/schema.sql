CREATE DATABASE IF NOT EXISTS belabela_iHL CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE belabela_iHL;

-- USERS
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  phone VARCHAR(50) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
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
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
