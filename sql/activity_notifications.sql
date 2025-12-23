-- Notifications Table for UC-16 / Activity Tracking
CREATE TABLE IF NOT EXISTS activity_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Recipient user id (or NULL for role-based)
    role VARCHAR(50), -- Target role (e.g., 'hmis')
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info', -- info, success, warning, error
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
