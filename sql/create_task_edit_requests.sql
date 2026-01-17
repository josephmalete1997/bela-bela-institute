USE belabela_iHL;

CREATE TABLE IF NOT EXISTS task_edit_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  educator_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  type ENUM('topic','project') NOT NULL DEFAULT 'project',
  description TEXT NULL,
  course_id INT NULL,
  status ENUM('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  url VARCHAR(255) NULL,
  request_status ENUM('pending','approved','denied') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  reviewed_by INT NULL,
  reviewed_at TIMESTAMP NULL,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (educator_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);
