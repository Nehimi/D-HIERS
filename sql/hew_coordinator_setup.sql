-- HEW COORDINATOR MODULE SETUP SCRIPT
-- Run this script to ensure all necessary tables and test data exist for the HEW Coordinator workflow.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Ensure `health_data` table exists (Stores reports from HEWs)
CREATE TABLE IF NOT EXISTS `health_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `householdId` varchar(50) NOT NULL,
    `kebele` varchar(100) DEFAULT NULL,
    `patient_name` varchar(255) DEFAULT NULL,
    `service_type` varchar(100) DEFAULT NULL,
    `count` int(11) DEFAULT 1,
    `details` text,
    `status` varchar(50) DEFAULT 'Pending', -- States: Pending -> Validated -> Forwarded
    `validated_by` int(11) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Ensure `household` table exists (Stores demographic data)
CREATE TABLE IF NOT EXISTS `household` (
  `id` int NOT NULL AUTO_INCREMENT,
  `region` varchar(100) NOT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `kebele` varchar(100) NOT NULL,
  `householdId` varchar(100) NOT NULL,
  `memberName` varchar(100) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `sex` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ensure `statistical_packages` table exists (Stores forwarded data packages)
CREATE TABLE IF NOT EXISTS `statistical_packages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `package_id` varchar(50) NOT NULL,
    `period` varchar(50) NOT NULL,
    `focal_person_id` int(11) DEFAULT NULL,
    `received_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `status` varchar(50) DEFAULT 'Pending',
    PRIMARY KEY (`id`),
    UNIQUE KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Ensure `activity_notifications` table exists (For Focal Person alerts)
CREATE TABLE IF NOT EXISTS `activity_notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL, -- The recipient (e.g., Focal Person ID)
    `role` varchar(50) DEFAULT NULL, -- OR target role
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `type` varchar(50) DEFAULT 'info',
    `is_read` tinyint(1) DEFAULT 0,
    `action_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- SEED DATA FOR TESTING COORDINATOR WORKFLOW
-- ==========================================

-- A. Insert Test Households (if not exist)
INSERT INTO `household` (`region`, `zone`, `woreda`, `kebele`, `householdId`, `memberName`, `age`, `sex`) VALUES
('Central', 'Hadiya', 'Hosana', 'Arade', 'HH-TEST-001', 'Abebe Kebede', 35, 'Male'),
('Central', 'Hadiya', 'Hosana', 'Lich-amba', 'HH-TEST-002', 'Sara Tesfaye', 28, 'Female'),
('Central', 'Hadiya', 'Hosana', 'Lereba', 'HH-TEST-003', 'Chala Bulto', 45, 'Male');

-- B. Insert Pending Health Reports (Visible in Coordinator Review/Monitor)
INSERT INTO `health_data` (`householdId`, `kebele`, `patient_name`, `service_type`, `count`, `status`, `details`) VALUES
('HH-TEST-001', 'Arade', 'Abebe Kebede', 'Malaria Check', 1, 'Pending', 'Fever reported, test positive'),
('HH-TEST-002', 'Lich-amba', 'Sara Tesfaye', 'ANC Visit', 1, 'Pending', 'First trimester checkup'),
('HH-TEST-003', 'Lereba', 'Chala Bulto', 'Hygiene Education', 1, 'Pending', 'Latrine construction verified'),
('HH-TEST-001', 'Arade', 'Abebe Kebede', 'Family Planning', 1, 'Validated', 'Pills distributed (Ready to Forward)');

-- C. Insert HEW Users (For Dynamic Monitor Dashboard)
INSERT INTO `users` (`first_name`, `last_name`, `emali`, `phone_no`, `userId`, `role`, `kebele`, `status`, `password`, `confirmPassword`) VALUES
('Abeba', 'Kebede', 'abeba.k@lichamba.et', '0911000001', 'HEW-001', 'hew', 'Arade', 'active', '$2y$10$HASH', ''),
('Melkamu', 'Godebo', 'melkamu.g@lichamba.et', '0911000002', 'HEW-002', 'hew', 'Lich-amba', 'active', '$2y$10$HASH', ''),
('Yonas', 'Loba', 'yonas.l@lichamba.et', '0911000003', 'HEW-003', 'hew', 'Lereba', 'active', '$2y$10$HASH', '');

COMMIT;


-- SUCCESS MESSAGE
SELECT "HEW Coordinator setup complete. Tables created and test records inserted." as result;
