-- =====================================================================
-- Della's 21st Birthday — Admin Panel Database Schema
-- Iterasi 0 (Fase A: Fondasi)
--
-- Cara pakai (Laragon):
--   1. Buka HeidiSQL / phpMyAdmin bawaan Laragon.
--   2. Buat database baru bernama `della_birthday` (utf8mb4_unicode_ci).
--   3. Jalankan file ini (schema.sql) di database tersebut.
--   4. Lanjut jalankan seed.sql untuk mengisi data awal.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `della_birthday`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `della_birthday`;

-- ---------------------------------------------------------------------
-- admins: akun untuk login ke /admin
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- settings: key-value untuk teks-teks sederhana lintas section
-- (Hero, Cake, Gate/Countdown, Footer, dst)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- memories: foto kenangan di Gallery Section
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `memories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `image_url` VARCHAR(500) NOT NULL,
  `caption` VARCHAR(255) NOT NULL,
  `event_date` VARCHAR(50) NULL,
  `location` VARCHAR(150) NULL,
  `tag` VARCHAR(100) NULL,
  `note` TEXT NULL,
  `likes` INT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_memories_published_sort` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- love_letter: isi surat cinta (selalu 1 baris, id = 1)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `love_letter` (
  `id` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `salutation` VARCHAR(255) NOT NULL DEFAULT '',
  `paragraphs_json` JSON NOT NULL,
  `closing` VARCHAR(255) NOT NULL DEFAULT '',
  `sender` VARCHAR(150) NOT NULL DEFAULT '',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- messages: "Amplop Doa" (wishes) — data lama + submission publik (Fase B)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_name` VARCHAR(100) NULL,
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
  `role_relation` VARCHAR(100) NULL,
  `avatar_emoji` VARCHAR(10) NULL,
  `envelope_color` VARCHAR(20) NULL,
  `message` TEXT NOT NULL,
  `hint` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `source` ENUM('seed','admin','public_form') NOT NULL DEFAULT 'admin',
  `likes` INT UNSIGNED NOT NULL DEFAULT 0,
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_status_created` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- admin_activity_log: opsional, jejak audit sederhana (Iterasi 6)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NULL,
  `action` VARCHAR(150) NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activity_admin` (`admin_id`),
  CONSTRAINT `fk_activity_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
