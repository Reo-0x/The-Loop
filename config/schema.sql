-- Create database if not exists
CREATE DATABASE IF NOT EXISTS week_planer;
USE week_planer;

-- Create users table if not exists
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create objectives table if not exists
CREATE TABLE IF NOT EXISTS objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    objective TEXT NOT NULL,
    day_of_week INT NOT NULL, -- 1 (Monday) to 7 (Sunday)
    status TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (day_of_week BETWEEN 1 AND 7)
);
