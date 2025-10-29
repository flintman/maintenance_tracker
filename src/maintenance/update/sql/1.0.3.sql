-- 1. Add remember_token column to users table
ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) DEFAULT NULL;

-- 2. Update version in admin_config table
UPDATE admin_config SET config_value = '1.0.3' WHERE config_name = 'version';