USE belabela_iHL;

-- Per-student task progress
CREATE TABLE IF NOT EXISTS task_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  user_id INT NOT NULL,
  status ENUM('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  position INT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_task_user (task_id, user_id),
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Track which learner submission a review belongs to
ALTER TABLE task_reviews
  ADD COLUMN submitter_id INT NULL AFTER task_id,
  ADD INDEX idx_task_submitter (task_id, submitter_id),
  ADD CONSTRAINT fk_task_reviews_submitter
    FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE SET NULL;

-- Assigned reviewers for each learner submission (max 5)
CREATE TABLE IF NOT EXISTS task_reviewer_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  submitter_id INT NOT NULL,
  reviewer_id INT NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_assignment (task_id, submitter_id, reviewer_id),
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages between submitters and reviewers/admins
CREATE TABLE IF NOT EXISTS task_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  submitter_id INT NOT NULL,
  sender_id INT NOT NULL,
  recipient_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
);
