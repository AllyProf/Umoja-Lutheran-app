-- Add missing columns to bookings table for corporate bookings
-- Run this SQL in your database if the columns don't exist

-- Check and add company_id column
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS company_id BIGINT UNSIGNED NULL AFTER guest_id;

-- Add foreign key constraint (if not exists)
-- Note: Remove IF NOT EXISTS if your MySQL version doesn't support it
ALTER TABLE bookings 
ADD CONSTRAINT bookings_company_id_foreign 
FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL;

-- Add payment_responsibility column
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS payment_responsibility ENUM('company', 'self', 'mixed') NULL AFTER company_id;

-- Add is_corporate_booking column
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS is_corporate_booking BOOLEAN DEFAULT FALSE AFTER payment_responsibility;
