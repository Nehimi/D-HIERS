-- Database Patch for Supervisor Flow Monitor
-- Adds missing auditing columns to health_data if they don't exist.

ALTER TABLE health_data ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE health_data ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Also ensure 'status' exists as it's critical for flow tracking
ALTER TABLE health_data MODIFY COLUMN status VARCHAR(50) DEFAULT 'Pending';
