USE belabela_iHL;

-- Add educator role
ALTER TABLE users
  MODIFY role ENUM('admin','student','educator') NOT NULL DEFAULT 'student';

-- Assign educators to courses
CREATE TABLE IF NOT EXISTS course_educators (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  educator_id INT NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_course_educator (course_id, educator_id),
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (educator_id) REFERENCES users(id) ON DELETE CASCADE
);
