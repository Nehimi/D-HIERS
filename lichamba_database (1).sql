-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 08, 2025 at 04:07 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `lichamba_database`
--

-- --------------------------------------------------------
--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `emali` varchar(100) NOT NULL,
  `phone_no` int NOT NULL,
  `userId` varchar(50) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `kebele` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `confirmPassword` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emali` (`emali`),
  UNIQUE KEY `phone_no` (`phone_no`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE = MyISAM AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (
    `id`,
    `first_name`,
    `last_name`,
    `emali`,
    `phone_no`,
    `userId`,
    `role`,
    `kebele`,
    `status`,
    `password`,
    `confirmPassword`
  )
VALUES (
    3,
    'Samuel',
    'Weldemeskel',
    'samuel@lichamba.health.et',
    2147483647,
    'HEW04',
    'hew',
    'lich-amba',
    'active',
    '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C',
    '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C'
  ),
  (
    2,
    'Eyu',
    'sami',
    'eyu@gmail.com',
    92934567,
    'ADMIN05',
    'admin',
    'lich-amba',
    'active',
    '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO',
    '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO'
  );
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;