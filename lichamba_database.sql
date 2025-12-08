-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 08, 2025 at 08:36 AM
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('hew','coordinator','hmis','linkage','supervisor','admin') NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `emali` varchar(255) DEFAULT NULL,
  `phoneNumber` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userId`, `password`, `role`, `fullname`, `emali`, `phoneNumber`) VALUES
(1, 'HEW001', '$2y$10$Iz7DVPjZl9Ie/1DBrQ.bvOwCrOh4rwFFa8SXtqK9uFSqB/5x7X1l6', 'hew', 'Alemu Desta', 'alemu3029@gmali.com', 923145630),
(3, 'HMIS01', '$2y$10$Iz7DVPjZl9Ie/1DBrQ.bvOwCrOh4rwFFa8SXtqK9uFSqB/5x7X1l6', 'hmis', 'Temesgen Adugna', 'adugnten22@gmali.com', 784324180),
(4, 'LINK01', '$2y$10$Iz7DVPjZl9Ie/1DBrQ.bvOwCrOh4rwFFa8SXtqK9uFSqB/5x7X1l6', 'linkage', 'Biniam Tsegaye', 'bin2@gmali.com', 780987634),
(5, 'SUP01', '$2y$10$Iz7DVPjZl9Ie/1DBrQ.bvOwCrOh4rwFFa8SXtqK9uFSqB/5x7X1l6', 'supervisor', 'Amanuel Kebede', 'amau09@gmali.com', 980987634),
(9, 'COORD03', '$2y$10$cur5/AbKvKKffNte3f9JQOomF4TEqoTyzW1Tkoncav98pBWJu1fBS', 'coordinator', 'sami', 'sami@gmali.com', 912342340),
(10, 'ADMIN03', '$2y$10$cur5/AbKvKKffNte3f9JQOomF4TEqoTyzW1Tkoncav98pBWJu1fBS', 'admin', 'sami', 'sami@gmali.com', 912342340);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
