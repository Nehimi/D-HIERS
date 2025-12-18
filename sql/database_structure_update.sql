-- D-HEIRS Database Structure Update
-- Run this script to ensure all tables are properly configured

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `emali` VARCHAR(255),
  `phone_no` VARCHAR(20) NOT NULL,
  `userId` VARCHAR(50) UNIQUE NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `kebele` VARCHAR(100) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`),
  INDEX `idx_kebele` (`kebele`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kebele Table  
CREATE TABLE IF NOT EXISTS `kebele` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kebeleName` VARCHAR(100) NOT NULL,
  `kebeleCode` VARCHAR(50) UNIQUE NOT NULL,
  `woreda` VARCHAR(100),
  `zone` VARCHAR(100),
  `population` INT,
  `households` INT,
  `healthPostName` VARCHAR(200),
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `user_name` VARCHAR(255),
  `user_role` VARCHAR(50),
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT,
  `ip_address` VARCHAR(45),
  `status` VARCHAR(20) DEFAULT 'success',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_action` (`action`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default Kebeles if they don't exist
INSERT IGNORE INTO `kebele` (`kebeleName`, `kebeleCode`, `woreda`, `zone`, `population`, `households`, `healthPostName`, `status`) VALUES
('Lich-Amba', 'KB-001', 'Libo Kemkem', 'South Gondar', 5000, 1200, 'Lich-Amba Health Post', 'active'),
('Arada', 'KB-002', 'Libo Kemkem', 'South Gondar', 4500, 1100, 'Arada Health Post', 'active'),
('Lereba', 'KB-003', 'Libo Kemkem', 'South Gondar', 3800, 950, 'Lereba Health Post', 'active'),
('PHCU Headquarters', 'KB-004', 'Libo Kemkem', 'South Gondar', 2000, 500, 'PHCU Main Office', 'active');

-- Verify table structures
SELECT 'Users Table' as TableName, COUNT(*) as RecordCount FROM users
UNION ALL
SELECT 'Kebele Table', COUNT(*) FROM kebele
UNION ALL  
SELECT 'Audit Logs Table', COUNT(*) FROM audit_logs;

-- Show recent users
SELECT id, userId, CONCAT(first_name, ' ', last_name) as full_name, role, kebele, status 
FROM users 
ORDER BY id DESC 
LIMIT 10;
