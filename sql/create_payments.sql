USE belabela_iHL;

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