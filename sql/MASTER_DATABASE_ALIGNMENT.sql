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
