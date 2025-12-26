<?php
/**
 * Database Auto-Sync (Self-Healing System)
 * This script ensures the database schema is always up-to-date.
 */

try {
    // 1. Ensure 'created_at' and 'updated_at' exist in health_data
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'created_at'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'updated_at'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    // 2. Ensure 'patient_name' and 'kebele' columns exist
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'patient_name'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN patient_name VARCHAR(255)");
    }
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'kebele'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN kebele VARCHAR(100)");
    }

    // 3. Auto-Rename legacy columns if they exist
    // Check for 'serviceType' -> 'service_type'
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'serviceType'");
    if (mysqli_num_rows($colsRes) > 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data CHANGE COLUMN serviceType service_type VARCHAR(100)");
    } else {
        // Ensure service_type exists if neither name is found (redundant but safe)
        $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'service_type'");
        if (mysqli_num_rows($colsRes) == 0) {
            mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN service_type VARCHAR(100)");
        }
    }

    // Check for 'totalServed' -> 'count'
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'totalServed'");
    if (mysqli_num_rows($colsRes) > 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE health_data CHANGE COLUMN totalServed count INT DEFAULT 1");
    } else {
        $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM health_data LIKE 'count'");
        if (mysqli_num_rows($colsRes) == 0) {
            mysqli_query($dataBaseConnection, "ALTER TABLE health_data ADD COLUMN count INT DEFAULT 1");
        }
    }

    // 4. Ensure 'activity_notifications' table exists
    mysqli_query($dataBaseConnection, "CREATE TABLE IF NOT EXISTS activity_notifications (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 5. Ensure 'audit_logs' table has 'user_role' and 'status' columns
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM audit_logs LIKE 'user_role'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE audit_logs ADD COLUMN user_role VARCHAR(50) AFTER user_name");
    }
    
    $colsRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM audit_logs LIKE 'status'");
    if (mysqli_num_rows($colsRes) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE audit_logs ADD COLUMN status VARCHAR(20) DEFAULT 'success' AFTER ip_address");
    }

} catch (Exception $e) {
    // Silent fail in production, but we could log it
    error_log("DB Sync Warning: " . $e->getMessage());
} catch (mysqli_sql_exception $e) {
    error_log("DB Sync SQL Error: " . $e->getMessage());
}
?>
