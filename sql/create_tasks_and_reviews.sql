USE belabela_iHL;

-- Tasks table: topics and projects
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
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Reviews/comments for tasks
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
