-- D-HEIRS Complete Database Setup
-- Run this in phpMyAdmin to create all necessary tables

-- 1. Create Users Table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_no VARCHAR(20) NOT NULL,
    userId VARCHAR(50) UNIQUE NOT NULL,
    role VARCHAR(50) NOT NULL,
    kebele VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create Kebele Table
DROP TABLE IF EXISTS kebele;
CREATE TABLE kebele (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kebeleName VARCHAR(100) NOT NULL,
    kebeleCode VARCHAR(50) UNIQUE NOT NULL,
    woreda VARCHAR(100),
    zone VARCHAR(100),
    population INT,
    households INT,
    healthPostName VARCHAR(200),
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Audit Logs Table
DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_name VARCHAR(255),
    user_role VARCHAR(50),
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    status VARCHAR(20) DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create Household Table (UPDATED with address fields)
DROP TABLE IF EXISTS household;
CREATE TABLE household (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region VARCHAR(100) NOT NULL,
    zone VARCHAR(100) NOT NULL,
    woreda VARCHAR(100) NOT NULL,
    householdId VARCHAR(50) UNIQUE NOT NULL,
    memberName VARCHAR(255) NOT NULL,
    age INT,
    sex VARCHAR(10),
    kebele VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create Health Data Table
DROP TABLE IF EXISTS health_data;
CREATE TABLE health_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    householdId VARCHAR(50) NOT NULL,
    serviceType VARCHAR(100) NOT NULL,
    totalServed INT NOT NULL,
    details TEXT,
    visitDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (householdId) REFERENCES household(householdId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Kebeles
INSERT INTO kebele (kebeleName, kebeleCode, woreda, zone, population, households, healthPostName, status) VALUES
('Lich-Amba', 'KB-001', 'Libo Kemkem', 'South Gondar', 5000, 1200, 'Lich-Amba Health Post', 'active'),
('Arada', 'KB-002', 'Libo Kemkem', 'South Gondar', 4500, 1100, 'Arada Health Post', 'active'),
('Lereba', 'KB-003', 'Libo Kemkem', 'South Gondar', 3800, 950, 'Lereba Health Post', 'active'),
('PHCU Headquarters', 'KB-004', 'Libo Kemkem', 'South Gondar', 2000, 500, 'PHCU Main Office', 'active');

-- Insert Test Admin User (password: admin123)
INSERT INTO users (first_name, last_name, email, phone_no, userId, role, kebele, status, password) VALUES
('Admin', 'User', 'admin@lichamba.health.et', '+251911000000', 'ADMIN-001', 'admin', 'PHCU Headquarters', 'active', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Verify Tables Created
SELECT 'Tables Created Successfully!' as Status;
SELECT 
    'users' as TableName, COUNT(*) as Records FROM users
UNION ALL
SELECT 'kebele', COUNT(*) FROM kebele
UNION ALL  
SELECT 'audit_logs', COUNT(*) FROM audit_logs
UNION ALL
SELECT 'household', COUNT(*) FROM household
UNION ALL
SELECT 'health_data', COUNT(*) FROM health_data;
