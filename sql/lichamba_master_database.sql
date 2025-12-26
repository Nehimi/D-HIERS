-- ========================================================
-- D-HEIRS MASTER DATABASE: lichamba_database
-- ========================================================
-- Description: Consolidated database for the entire D-HEIRS system.
-- Includes: Authentication, HEW, Coordinator, Focal, and HMIS modules.
-- ========================================================

CREATE DATABASE IF NOT EXISTS `lichamba_database`;
USE `lichamba_database`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. KEBELE TABLE
CREATE TABLE IF NOT EXISTS `kebele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kebeleName` varchar(50) DEFAULT NULL,
  `kebeleCode` varchar(50) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `population` int DEFAULT NULL,
  `households` int DEFAULT NULL,
  `healthPostName` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. USERS TABLE
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone_no` varchar(25) NOT NULL,
  `userId` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `kebele` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `password` varchar(255) NOT NULL,
  `confirmPassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. HOUSEHOLD TABLE
CREATE TABLE IF NOT EXISTS `household` (
  `id` int NOT NULL AUTO_INCREMENT,
  `region` varchar(100) NOT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `kebele` varchar(100) NOT NULL,
  `householdId` varchar(100) NOT NULL, -- FK source
  `memberName` varchar(100) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `sex` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `householdId` (`householdId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. HEALTH DATA TABLE
CREATE TABLE IF NOT EXISTS `health_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `householdId` varchar(100) NOT NULL, -- Synced to 100
    `kebele` varchar(100) DEFAULT NULL,
    `patient_name` varchar(255) DEFAULT NULL,
    `service_type` varchar(100) DEFAULT NULL,
    `count` int(11) DEFAULT 1,
    `details` text,
    `status` varchar(50) DEFAULT 'Pending',
    `submitted_by_id` int(11) DEFAULT NULL,
    `validated_by` int(11) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`householdId`) REFERENCES `household`(`householdId`) ON DELETE CASCADE,
    FOREIGN KEY (`submitted_by_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`validated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. STATISTICAL PACKAGES
CREATE TABLE IF NOT EXISTS `statistical_packages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `package_id` varchar(50) NOT NULL,
    `period` varchar(50) NOT NULL,
    `focal_person_id` int(11) DEFAULT NULL,
    `focal_person_name` varchar(255) DEFAULT NULL,
    `received_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `status` varchar(50) DEFAULT 'Pending',
    `data_summary` json DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `package_id` (`package_id`),
    FOREIGN KEY (`focal_person_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. ACTIVITY NOTIFICATIONS
CREATE TABLE IF NOT EXISTS `activity_notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `role` varchar(50) DEFAULT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `type` varchar(50) DEFAULT 'info',
    `is_read` tinyint(1) DEFAULT 0,
    `action_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. HMIS OFFICIAL REPORTS
CREATE TABLE IF NOT EXISTS `hmis_reports` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `report_id` varchar(50) UNIQUE NOT NULL,
    `source_package_id` varchar(50) DEFAULT NULL,
    `report_name` varchar(255) NOT NULL,
    `report_type` varchar(100) NOT NULL,
    `generated_by` int DEFAULT NULL,
    `generated_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `file_path` varchar(255) DEFAULT NULL,
    `file_size` varchar(50) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'Generated',
    `format` varchar(20) DEFAULT 'PDF',
    `notes` text DEFAULT NULL,
    FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`source_package_id`) REFERENCES `statistical_packages`(`package_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. DHIS2 SUBMISSION LOGS
CREATE TABLE IF NOT EXISTS `dhis2_submissions` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `submission_id` varchar(50) UNIQUE NOT NULL,
    `report_id` varchar(50) NOT NULL,
    `submitted_by` int NOT NULL,
    `submitted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `dhis2_response_code` varchar(10),
    `dhis2_reference` varchar(100),
    `status` varchar(50) DEFAULT 'Success',
    FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`report_id`) REFERENCES `hmis_reports`(`report_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. ANALYTICS SUMMARY
CREATE TABLE IF NOT EXISTS `hmis_analytics_summary` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `category` varchar(100) NOT NULL,
    `metric_name` varchar(100) NOT NULL,
    `metric_value` decimal(15, 2),
    `period` varchar(50),
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. PASSWORD RESETS
CREATE TABLE IF NOT EXISTS `password_resets`(
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. PASSWORD RESET LOGS
CREATE TABLE IF NOT EXISTS `password_reset_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. GENERATED REPORTS (History)
CREATE TABLE IF NOT EXISTS `generated_reports` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `report_name` varchar(255) NOT NULL,
    `report_type` varchar(100) NOT NULL,
    `generated_by` varchar(255) NOT NULL,
    `generated_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `file_size` varchar(50) DEFAULT '0 KB',
    `status` varchar(50) DEFAULT 'Ready',
    `format` varchar(20) NOT NULL,
    `details` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. AUDIT LOGS
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `user_id` int DEFAULT NULL,
    `user_name` varchar(255) DEFAULT NULL,
    `user_role` varchar(50) DEFAULT NULL,
    `action` varchar(100) NOT NULL,
    `details` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `status` varchar(20) DEFAULT 'success',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- SEED DATA
-- ==========================================

-- A. Kebeles
INSERT IGNORE INTO `kebele` (`kebeleName`, `kebeleCode`, `woreda`, `zone`, `population`, `households`, `status`) VALUES
('Arada', 'KB-001', 'Hosana', 'Hadiya', 5200, 1100, 'active'),
('Lich-amba', 'KB-002', 'Hosana', 'Hadiya', 4900, 1200, 'active'),
('Lereba', 'KB-003', 'Hosana', 'Hadiya', 4800, 1050, 'active');

-- B. Test Users
-- Note: Insert users first so they can be referenced by ID
INSERT IGNORE INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone_no`, `userId`, `role`, `kebele`, `status`, `password`) VALUES
(1, 'Semira', 'Kedir', 'semira@lichamba.health.et', '0911223344', 'HEW-001', 'hew', 'Lich-amba', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'Abebe', 'Coordinator', 'coord@lichamba.health.et', '0900112233', 'COORD-001', 'coordinator', 'PHCU HQ', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'Sara', 'Focal', 'focal@lichamba.health.et', '0988776655', 'FOCAL-001', 'focal', 'Woreda Office', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- C. Test Households
INSERT IGNORE INTO `household` (`region`, `zone`, `woreda`, `kebele`, `householdId`, `memberName`, `age`, `sex`) VALUES
('Central', 'Hadiya', 'Hosana', 'Lich-amba', 'HH-001', 'W/meskel Wolde', 38, 'male'),
('Central', 'Hadiya', 'Hosana', 'Arada', 'HH-002', 'Abebe Bekele', 60, 'male');

-- D. Initial Health Reports
INSERT IGNORE INTO `health_data` (`householdId`, `kebele`, `patient_name`, `service_type`, `count`, `status`, `details`, `submitted_by_id`) VALUES
('HH-001', 'Lich-amba', 'W/meskel Wolde', 'ANC Visit', 1, 'Pending', 'Initial checkup completed.', 1),
('HH-002', 'Arada', 'Abebe Bekele', 'Immunization', 1, 'Validated', 'Completed childhood cycle.', 1);

COMMIT;
