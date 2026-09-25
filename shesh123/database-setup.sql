-- ========================================
-- Barangay System Database Setup
-- ========================================

-- Create the database
CREATE DATABASE IF NOT EXISTS barangay_system;
USE barangay_system;

-- ========================================
-- Users Table (for login)
-- ========================================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- Residents Table
-- ========================================
CREATE TABLE IF NOT EXISTS residents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  age INT,
  gender VARCHAR(20),
  address TEXT,
  contact_number VARCHAR(20),
  category VARCHAR(50) NOT NULL COMMENT 'Senior Citizen, PWD',
  disability_type VARCHAR(100),
  assistance_needed VARCHAR(150),
  assistance_status VARCHAR(50) DEFAULT 'Not Received' COMMENT 'Received, Not Received',
  assistance_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- Insert default admin user
-- ========================================
-- Username: admin
-- Password: admin123
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$YIjlrpmVfQWDPkjlq8BkJeQKX6Bh3kB8g5V5Y2Q1Z3X8W5C6M9L7K');

-- ========================================
-- Insert sample residents (Optional)
-- ========================================
INSERT INTO residents (full_name, age, gender, address, contact_number, category, disability_type, assistance_needed, assistance_status, assistance_date) VALUES 
('Maria Santos', 75, 'Female', '123 Main St, Barangay', '09123456789', 'Senior Citizen', NULL, 'Medicine', 'Received', '2025-01-15'),
('Juan Dela Cruz', 82, 'Male', '456 Oak Ave, Barangay', '09987654321', 'Senior Citizen', NULL, 'Food Assistance', 'Not Received', NULL),
('Angela Reyes', 35, 'Female', '789 Pine Rd, Barangay', '09555555555', 'PWD', 'Hearing Impaired', 'Transportation', 'Received', '2025-01-20'),
('Carlos Mendoza', 40, 'Male', '321 Elm St, Barangay', '09777777777', 'PWD', 'Visually Impaired', 'Medical Aid', 'Not Received', NULL);
