-- Modding Panel Database Schema
-- Run this SQL to create the necessary database tables

CREATE DATABASE IF NOT EXISTS modding_panel;
USE modding_panel;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Mods table
CREATE TABLE IF NOT EXISTS mods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- License Keys table
CREATE TABLE IF NOT EXISTS license_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_key VARCHAR(50) UNIQUE NOT NULL,
    mod_id INT NULL,
    user_id INT NULL,
    duration_days INT NOT NULL,
    expiration_date DATETIME NOT NULL,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mod_id) REFERENCES mods(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_license_key (license_key),
    INDEX idx_expiration (expiration_date),
    INDEX idx_status (status)
);

-- Create default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');

-- Insert sample mod
INSERT INTO mods (user_id, name, description) VALUES 
(1, 'Sample Mod', 'A sample mod for testing the license key system');

-- Insert sample license keys
INSERT INTO license_keys (license_key, mod_id, user_id, duration_days, expiration_date, status) VALUES 
('DEMO-1234-ABCD-5678', 1, 1, 30, DATE_ADD(NOW(), INTERVAL 30 DAY), 'active'),
('TEST-9876-WXYZ-4321', NULL, 1, 90, DATE_ADD(NOW(), INTERVAL 90 DAY), 'active');