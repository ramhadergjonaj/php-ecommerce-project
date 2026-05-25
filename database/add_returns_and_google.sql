-- Ekzekutoni në phpMyAdmin nëse DB ekziston tashmë
USE ecommerce_db;

ALTER TABLE users
  ADD COLUMN google_id VARCHAR(100) NULL UNIQUE AFTER email;

ALTER TABLE orders
  MODIFY status ENUM('pending', 'completed', 'cancelled', 'returned') NOT NULL DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS return_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  user_id INT NOT NULL,
  reason TEXT,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  admin_note VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  CONSTRAINT fk_return_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_return_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
