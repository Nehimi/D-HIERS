-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2025 at 06:15 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lichamba_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

DROP TABLE IF EXISTS `household`;
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
  `serviceType` varchar(100) DEFAULT NULL,
  `totalServed` varchar(100) DEFAULT NULL,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`id`, `region`, `zone`, `woreda`, `kebele`, `householdId`, `memberName`, `age`, `sex`, `serviceType`, `totalServed`, `details`) VALUES
(1, 'Cinteral Ethiopia', 'Hadiya', 'hosana', 'Arada', 'HH-001', 'W/meskel Wolde', 38, 'male', NULL, NULL, NULL),
(2, 'Cinteral Ethiopia', 'Hadiya', 'hosana', 'Lereba', 'HH-002', 'Abebe Bekele', 60, 'male', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kebele`
--

DROP TABLE IF EXISTS `kebele`;
CREATE TABLE IF NOT EXISTS `kebele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kebeleName` varchar(50) DEFAULT NULL,
  `kebeleCode` varchar(50) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `population` int DEFAULT NULL,
  `households` int DEFAULT NULL,
  `healthPostName` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kebele`
--

INSERT INTO `kebele` (`id`, `kebeleName`, `kebeleCode`, `woreda`, `zone`, `population`, `households`, `healthPostName`, `status`) VALUES
(1, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 5000, 1200, 'nnm', 'active'),
(3, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 4999, 1200, NULL, 'active'),
(4, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 4999, 1200, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `emali` varchar(100) NOT NULL,
  `phone_no` varchar(25) NOT NULL,
  `userId` varchar(50) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `kebele` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `confirmPassword` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emali` (`emali`),
  UNIQUE KEY `phone_no` (`phone_no`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `emali`, `phone_no`, `userId`, `role`, `kebele`, `status`, `password`, `confirmPassword`) VALUES
(3, 'Samuel', 'Weldemeskel', 'samuel@lichamba.health.et', '2147483647', 'HEW04', 'hew', 'lich-amba', 'active', '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C', '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C'),
(2, 'Eyu', 'sami', 'eyu@gmail.com', '92934567', 'ADMIN05', 'admin', 'lich-amba', 'active', '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO', '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO'),
(6, 'Baty', 'antor', 'baty@lichamba.health.et', '+2570334567', 'ADMIN09', 'admin', 'phcu-hq', 'active', '$2y$10$YpYvRr2e62Nq9EwucSxvF.k3nRsYIIMEEfeadJg57ldrlBvVhpAJu', ''),
(7, 'Mamo', 'Belay', 'mamo@lichamba.health.et', '+25700981127', 'HEW06', 'hew', 'arada', 'active', '$2y$10$PUiVVS7HnzPC1VmbkgG0F.uW8gLF8Q5rmK7AhGwUZ85SY6K7vOSN2', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ========================================================
-- D-HEIRS MASTER DATABASE ALIGNMENT SCRIPT
-- ========================================================
-- Description: This script ensures all tables and columns 
-- are aligned with the Professional "Pro" implementation.
-- ========================================================

-- 1. USER ROLES & PERMISSIONS
-- Ensures all required roles are supported.
ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL;

-- 2. HEALTH DATA PIPELINE (UC-14, UC-15, UC-16, UC-20)
-- This table tracks the "magic" data flow from field to national submission.
CREATE TABLE IF NOT EXISTS health_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    householdId VARCHAR(50) NOT NULL,
    kebele VARCHAR(100),
    patient_name VARCHAR(255),
    service_type VARCHAR(100),
    count INT DEFAULT 1,
    details TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    validated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (householdId) REFERENCES household(householdId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure auditing columns exist for existing tables
ALTER TABLE health_data ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE health_data ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Alignment for existing columns if they exist under old names
-- Run these if you have data in old tables:
-- ALTER TABLE health_data CHANGE COLUMN serviceType service_type VARCHAR(100);
-- ALTER TABLE health_data CHANGE COLUMN totalServed count INT DEFAULT 1;

-- 3. STATISTICAL SUMMARIES (UC-15)
-- Stores the aggregated woreda-level data packages.
CREATE TABLE IF NOT EXISTS statistical_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id VARCHAR(50) UNIQUE NOT NULL,
    period VARCHAR(50) NOT NULL,
    focal_person_id INT,
    focal_person_name VARCHAR(255),
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending, Processed
    data_summary JSON,
    FOREIGN KEY (focal_person_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. HMIS OFFICIAL REPORTS (UC-16)
CREATE TABLE IF NOT EXISTS hmis_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id VARCHAR(50) UNIQUE NOT NULL,
    source_package_id VARCHAR(50),
    report_name VARCHAR(255) NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    generated_by INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Generated',
    format VARCHAR(20) DEFAULT 'PDF',
    FOREIGN KEY (generated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. AUDIT & OVERSIGHT (UC-22)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_name VARCHAR(255),
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- SEED DATA FOR TESTING THE "MAGIC"
-- ========================================================

-- Create a Focal Person test user (password: focal123)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT IGNORE INTO users (first_name, last_name, email, phone_no, userId, role, kebele, status, password) 
VALUES ('Linkage', 'Officer', 'focal@lichamba.health.et', '+25100112233', 'LINK-001', 'linkage', 'PHCU HQ', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Create a Supervisor test user (password: sup123)
INSERT IGNORE INTO users (first_name, last_name, email, phone_no, userId, role, kebele, status, password) 
VALUES ('System', 'Supervisor', 'sup@lichamba.health.et', '+25100445566', 'SUP-001', 'supervisor', 'Distric Office', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample Health Data in various states
INSERT IGNORE INTO household (region, zone, woreda, householdId, memberName, age, sex, kebele)
VALUES ('Central', 'Hadiya', 'Hosana', 'HH-PRO-001', 'Test Patient', 25, 'Female', 'Arada');

INSERT IGNORE INTO health_data (householdId, kebele, patient_name, service_type, count, status)
VALUES 
('HH-PRO-001', 'Arada', 'Test Patient', 'Immunization', 1, 'Pending'),
('HH-PRO-001', 'Arada', 'Test Patient', 'MCH Visit', 1, 'Validated'),
('HH-PRO-001', 'Arada', 'Test Patient', 'Nutrition Service', 1, 'Summarized');

-- 6. SYSTEM-WIDE NOTIFICATIONS (The Magic)
CREATE TABLE IF NOT EXISTS activity_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    role VARCHAR(50), 
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- D-HEIRS HMIS Module Database Setup
-- This script creates tables for HMIS official reports, analytics, and DHIS2 logs.

-- 1. Statistical Packages (Data received from Linkage Focal Person)
-- These are the validated summaries that HMIS Officer processes.
CREATE TABLE IF NOT EXISTS statistical_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id VARCHAR(50) UNIQUE NOT NULL,
    period VARCHAR(50) NOT NULL,
    focal_person_id INT NOT NULL,
    focal_person_name VARCHAR(255),
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending, Processed
    data_summary JSON, -- Store aggregated numbers/KPIs for easy retrieval
    FOREIGN KEY (focal_person_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. HMIS Official Reports (Generated by HMIS Officer)
CREATE TABLE IF NOT EXISTS hmis_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id VARCHAR(50) UNIQUE NOT NULL,
    source_package_id VARCHAR(50),
    report_name VARCHAR(255) NOT NULL,
    report_type VARCHAR(100) NOT NULL, -- Monthly, KPI, Annual
    generated_by INT NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255),
    file_size VARCHAR(50),
    status VARCHAR(50) DEFAULT 'Generated', -- Generated, Submitted
    format VARCHAR(20) DEFAULT 'PDF',
    notes TEXT,
    FOREIGN KEY (generated_by) REFERENCES users(id),
    FOREIGN KEY (source_package_id) REFERENCES statistical_packages(package_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. DHIS2 Submission Logs
CREATE TABLE IF NOT EXISTS dhis2_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id VARCHAR(50) UNIQUE NOT NULL,
    report_id VARCHAR(50) NOT NULL,
    submitted_by INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dhis2_response_code VARCHAR(10),
    dhis2_reference VARCHAR(100),
    status VARCHAR(50) DEFAULT 'Success', -- Success, Failed
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (report_id) REFERENCES hmis_reports(report_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Analytics Summary (For Dashboard Visualizations)
-- This table can be updated via triggers or scheduled tasks to speed up dashboard loading.
CREATE TABLE IF NOT EXISTS hmis_analytics_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL, -- e.g., 'Reports_Monthly', 'KPI_Accuracy'
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(15, 2),
    period VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Seed Data for Demonstration
INSERT IGNORE INTO statistical_packages (package_id, period, focal_person_id, focal_person_name, status) VALUES
('STAT-2025-001', 'January 2025', 1, 'Abebe Kebede', 'Pending'),
('STAT-2024-124', 'December 2024', 1, 'Sara Tekle', 'Processed');

INSERT IGNORE INTO hmis_reports (report_id, source_package_id, report_name, report_type, generated_by, file_size, status, format) VALUES 
('REP-124', 'STAT-2024-124', 'Monthly_Summary_Dec_2024', 'Monthly', 2, '2457600', 'Generated', 'PDF'),
('REP-132', 'STAT-2024-112', 'KPI_Report_Q4_2024', 'KPI', 2, '1153433', 'Submitted', 'Excel');

INSERT IGNORE INTO dhis2_submissions (submission_id, report_id, submitted_by, dhis2_response_code, dhis2_reference) VALUES
('SUB-101', 'REP-132', 2, '200', 'DHIS2-REF-9988');
-- Password Reset Tokens Table for D-HEIRS
-- Run this SQL to add the password_resets table to your database

CREATE TABLE IF NOT EXISTS `password_resets`(
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `email` (`email`),
  KEY `expires_at` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Index for faster token lookup
CREATE INDEX idx_token_lookup ON password_resets(token, used, expires_at);


-- Password Reset Notifications/Logs Table
-- This stores all password reset activities for audit purposes

CREATE TABLE IF NOT EXISTS `password_reset_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `action` ENUM('request', 'reset_success', 'reset_failed', 'token_expired', 'token_invalid') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Create Generated Reports Table
DROP TABLE IF EXISTS generated_reports;
CREATE TABLE generated_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    generated_by VARCHAR(255) NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_size VARCHAR(50) DEFAULT '0 KB',
    status VARCHAR(50) DEFAULT 'Ready',
    format VARCHAR(20) NOT NULL,
    details TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some dummy data for history
INSERT INTO generated_reports (report_name, report_type, generated_by, generated_at, file_size, status, format) VALUES
('Oct_User_Activity', 'User Activity', 'Dr. Admin', DATE_SUB(NOW(), INTERVAL 2 DAY), '2.4 MB', 'Ready', 'PDF'),
('Health_Post_Perf_Q3', 'Health Post Performance', 'Abebe Kebede', DATE_SUB(NOW(), INTERVAL 3 DAY), '156 KB', 'Ready', 'CSV'),
('Disease_Surveillance_W42', 'Disease Surveillance', 'System (Auto)', DATE_SUB(NOW(), INTERVAL 4 DAY), '1.1 MB', 'Ready', 'Excel');
