CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_name VARCHAR(255),
    user_role VARCHAR(50),
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    status VARCHAR(20) DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample audit log data
INSERT INTO audit_logs (user_name, user_role, action, details, ip_address, status, created_at) VALUES
('Dr. Admin', 'admin', 'User Created', 'Created new HEW account for Sara Tadesse', '192.168.1.105', 'success', '2023-10-24 14:32:05'),
('Abebe Kebede', 'coordinator', 'Report Submitted', 'Submitted monthly performance report for Arada', '10.0.0.45', 'success', '2023-10-24 12:15:30'),
('Unknown', '', 'Failed Login', 'Invalid password attempt for account admin', '45.2.1.99', 'failed', '2023-10-24 11:45:12'),
('Sara Tadesse', 'hew', 'Data Update', 'Updated patient record #44592', '192.168.1.112', 'success', '2023-10-24 09:10:22'),
('System', 'system', 'Backup', 'Automated daily database backup', 'localhost', 'success', '2023-10-23 16:55:00');
