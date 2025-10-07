-- Create database
CREATE DATABASE IF NOT EXISTS login_demo;
USE login_demo;

-- Create users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user'
);

-- Insert dummy users (password = '12345' for both)
INSERT INTO users (username, password, role) VALUES
('admin', MD5('12345'), 'admin'),
('param', MD5('12345'), 'user');
('mihir', MD5('12345'), 'user', 'active'),
('jay', MD5('12345'), 'user', 'inactive'),
('ananta', MD5('12345'), 'user', 'active');
