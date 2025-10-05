CREATE DATABASE IF NOT EXISTS `minigame_db`;
USE `minigame_db`;

-- Tabel untuk menyimpan skor dari semua game
DROP TABLE IF EXISTS `scores`;
CREATE TABLE `scores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `game_name` VARCHAR(100) NOT NULL,
  `social_media_name` VARCHAR(255) NOT NULL,
  `score` INT(11) NOT NULL,
  `play_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk akun admin
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
