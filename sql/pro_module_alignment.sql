-- D-HEIRS Pro Module Schema Alignment
-- This script aligns the health_data table with UC-14, UC-15, and UC-16 requirements.

-- Remove columns first if you want a clean start, OR run one by one:
ALTER TABLE health_data ADD COLUMN kebele VARCHAR(100) AFTER householdId;
ALTER TABLE health_data ADD COLUMN patient_name VARCHAR(255) AFTER kebele;
ALTER TABLE health_data ADD COLUMN service_type VARCHAR(100) AFTER patient_name;
ALTER TABLE health_data ADD COLUMN count INT DEFAULT 0 AFTER service_type;
ALTER TABLE health_data ADD COLUMN status VARCHAR(50) DEFAULT 'Pending' AFTER count;
ALTER TABLE health_data ADD COLUMN validated_by INT AFTER status;

-- Copy data from old columns if they exist
UPDATE health_data SET service_type = serviceType WHERE service_type IS NULL AND serviceType IS NOT NULL;
UPDATE health_data SET count = totalServed WHERE count = 0 AND totalServed IS NOT NULL;

-- Ensure constraints (optional, if users want strictly enforced FKs)
-- ALTER TABLE health_data ADD CONSTRAINT fk_validated_by FOREIGN KEY (validated_by) REFERENCES users(id);
