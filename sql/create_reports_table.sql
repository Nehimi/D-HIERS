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
