-- Struktur referensi jika aplikasi hendak dimigrasikan ke MySQL.
CREATE DATABASE IF NOT EXISTS cv_multi_user CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cv_multi_user;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cv_profiles (
    user_id INT UNSIGNED PRIMARY KEY,
    profile_json LONGTEXT NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE app_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
