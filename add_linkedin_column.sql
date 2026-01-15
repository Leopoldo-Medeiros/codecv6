-- Add LinkedIn column to profiles table if it doesn't exist
ALTER TABLE profiles ADD COLUMN IF NOT EXISTS linkedin VARCHAR(255) NULL AFTER github; 