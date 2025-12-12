-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2025 at 06:15 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lichamba_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

DROP TABLE IF EXISTS `household`;
CREATE TABLE IF NOT EXISTS `household` (
  `id` int NOT NULL AUTO_INCREMENT,
  `region` varchar(100) NOT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `kebele` varchar(100) NOT NULL,
  `householdId` varchar(100) NOT NULL,
  `memberName` varchar(100) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `sex` varchar(50) DEFAULT NULL,
  `serviceType` varchar(100) DEFAULT NULL,
  `totalServed` varchar(100) DEFAULT NULL,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`id`, `region`, `zone`, `woreda`, `kebele`, `householdId`, `memberName`, `age`, `sex`, `serviceType`, `totalServed`, `details`) VALUES
(1, 'Cinteral Ethiopia', 'Hadiya', 'hosana', 'Arada', 'HH-001', 'W/meskel Wolde', 38, 'male', NULL, NULL, NULL),
(2, 'Cinteral Ethiopia', 'Hadiya', 'hosana', 'Lereba', 'HH-002', 'Abebe Bekele', 60, 'male', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kebele`
--

DROP TABLE IF EXISTS `kebele`;
CREATE TABLE IF NOT EXISTS `kebele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kebeleName` varchar(50) DEFAULT NULL,
  `kebeleCode` varchar(50) DEFAULT NULL,
  `woreda` varchar(100) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `population` int DEFAULT NULL,
  `households` int DEFAULT NULL,
  `healthPostName` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kebele`
--

INSERT INTO `kebele` (`id`, `kebeleName`, `kebeleCode`, `woreda`, `zone`, `population`, `households`, `healthPostName`, `status`) VALUES
(1, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 5000, 1200, 'nnm', 'active'),
(3, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 4999, 1200, NULL, 'active'),
(4, 'lich-amba', 'KB-005', 'Hadya', 'hadya', 4999, 1200, NULL, 'active');

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
  `phone_no` varchar(25) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `emali`, `phone_no`, `userId`, `role`, `kebele`, `status`, `password`, `confirmPassword`) VALUES
(3, 'Samuel', 'Weldemeskel', 'samuel@lichamba.health.et', '2147483647', 'HEW04', 'hew', 'lich-amba', 'active', '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C', '$2y$10$Me4ZpaW5v7/P35sPg0sLUedq99HoyXTqSQuqbcrNYg3V9UqIQzS4C'),
(2, 'Eyu', 'sami', 'eyu@gmail.com', '92934567', 'ADMIN05', 'admin', 'lich-amba', 'active', '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO', '$2y$10$w/GaGMBXFTxOhDlhJKL6ye6JIilekpWxkZFlF37Gcs5AXTn.XA4xO'),
(6, 'Baty', 'antor', 'baty@lichamba.health.et', '+2570334567', 'ADMIN09', 'admin', 'phcu-hq', 'active', '$2y$10$YpYvRr2e62Nq9EwucSxvF.k3nRsYIIMEEfeadJg57ldrlBvVhpAJu', ''),
(7, 'Mamo', 'Belay', 'mamo@lichamba.health.et', '+25700981127', 'HEW06', 'hew', 'arada', 'active', '$2y$10$PUiVVS7HnzPC1VmbkgG0F.uW8gLF8Q5rmK7AhGwUZ85SY6K7vOSN2', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
