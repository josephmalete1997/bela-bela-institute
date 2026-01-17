-- Migration: Add password reset functionality to users table
-- Run this if your users table doesn't have password_reset_token and password_reset_expires columns

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(64) NULL,
ADD COLUMN IF NOT EXISTS password_reset_expires DATETIME NULL,
ADD COLUMN IF NOT EXISTS last_login DATETIME NULL;

-- Add index for faster token lookups
CREATE INDEX IF NOT EXISTS idx_password_reset_token ON users(password_reset_token);
