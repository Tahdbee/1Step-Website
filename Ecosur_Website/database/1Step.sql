-- Ecosur Website Database Setup
-- This file creates the necessary database schema for the Ecosur application

-- Create database
CREATE DATABASE IF NOT EXISTS `1Step`;
USE `1Step`;

-- Create Users table
CREATE TABLE IF NOT EXISTS `users` (
    `ID_user` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Reviews table
CREATE TABLE IF NOT EXISTS `review` (
    `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `ID_user` INT NOT NULL,
    `Review` TEXT NOT NULL,
    `Stars` INT DEFAULT 5,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`ID_user`) REFERENCES `users`(`ID_user`) ON DELETE CASCADE,
    INDEX `idx_user` (`ID_user`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create User Profile table (for extended profile information)
CREATE TABLE IF NOT EXISTS `user_profile` (
    `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `ID_user` INT NOT NULL UNIQUE,
    `full_name` VARCHAR(255),
    `bio` TEXT,
    `profile_picture` VARCHAR(255),
    `phone` VARCHAR(20),
    `date_of_birth` DATE,
    `gender` ENUM('Male', 'Female', 'Other', 'Prefer not to say') DEFAULT 'Prefer not to say',
    `location` VARCHAR(255),
    `website` VARCHAR(255),
    `notification_email` BOOLEAN DEFAULT TRUE,
    `notification_marketing` BOOLEAN DEFAULT FALSE,
    `public_profile` BOOLEAN DEFAULT TRUE,
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`ID_user`) REFERENCES `users`(`ID_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create User Activity table (for tracking user actions)
CREATE TABLE IF NOT EXISTS `user_activity` (
    `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `ID_user` INT NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `activity_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ID_user`) REFERENCES `users`(`ID_user`) ON DELETE CASCADE,
    INDEX `idx_user` (`ID_user`),
    INDEX `idx_date` (`activity_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create User Statistics table (for tracking user engagement)
CREATE TABLE IF NOT EXISTS `user_statistics` (
    `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `ID_user` INT NOT NULL UNIQUE,
    `reviews_posted` INT DEFAULT 0,
    `likes_received` INT DEFAULT 0,
    `bookmarks` INT DEFAULT 0,
    `days_active` INT DEFAULT 0,
    `last_login` TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`ID_user`) REFERENCES `users`(`ID_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional - remove if not needed)
-- INSERT INTO `users` (`email`, `password`, `name`) VALUES
-- ('demo@example.com', 'password123', 'Demo User');
