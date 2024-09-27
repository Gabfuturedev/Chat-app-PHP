-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 27, 2024 at 11:54 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `file_path`, `reply_to`, `timestamp`) VALUES
(17, 5, 0, 'ddd', NULL, NULL, '2024-09-26 07:01:53'),
(18, 5, 1, 'ssss', NULL, NULL, '2024-09-26 07:03:02'),
(19, 5, 1, 'hiiii', NULL, 18, '2024-09-26 07:03:07'),
(20, 5, 1, 'lets goooo', NULL, 19, '2024-09-26 07:03:14'),
(21, 5, 1, 'hhhhh', NULL, NULL, '2024-09-26 07:03:27'),
(22, 5, 1, 'im gab', NULL, 21, '2024-09-26 07:03:32'),
(23, 5, 1, 'ddddd', NULL, 21, '2024-09-26 07:04:11'),
(24, 5, 1, 'ssss', NULL, NULL, '2024-09-26 07:04:16'),
(25, 5, 1, 'batttt', NULL, 24, '2024-09-26 07:04:24'),
(26, 5, 1, 'gab', NULL, 25, '2024-09-26 07:08:15'),
(27, 5, 1, 'letttttt', NULL, 22, '2024-09-26 07:08:42'),
(28, 5, 1, 'ggggg', NULL, 27, '2024-09-26 07:10:23'),
(29, 5, 1, 'dddd', NULL, 28, '2024-09-26 07:12:09'),
(30, 5, 1, 'gsasdasdad', NULL, 29, '2024-09-26 07:14:14'),
(31, 5, 1, 'hi', NULL, 30, '2024-09-26 07:18:34'),
(32, 5, 1, 'what you foing', NULL, NULL, '2024-09-26 07:18:47'),
(33, 5, 1, 'yah yah', NULL, 21, '2024-09-26 07:21:37'),
(34, 5, 1, '0', 'c1.JPG', NULL, '2024-09-26 07:24:06'),
(35, 5, 1, '', 'uploads/66f50c46506ce_c1.JPG', NULL, '2024-09-26 07:24:54'),
(36, 5, 1, 'yeah baby', NULL, NULL, '2024-09-26 07:25:04'),
(37, 5, 1, 'gggg', NULL, 36, '2024-09-26 07:26:28'),
(38, 5, 1, '', 'uploads/66f50cac1f740_c1.JPG', NULL, '2024-09-26 07:26:36'),
(39, 5, 1, '', 'uploads/66f50f38e4c61_Capture.JPG', NULL, '2024-09-26 07:37:28'),
(40, 5, 1, 'hiii', NULL, 37, '2024-09-26 07:37:41'),
(41, 4, 1, 'hiii', NULL, NULL, '2024-09-26 13:27:28'),
(42, 4, 1, '', 'uploads/66f56148b2a92_anime website.JPG', NULL, '2024-09-26 13:27:36'),
(43, 4, 1, 'helooo', NULL, 41, '2024-09-26 13:27:49'),
(44, 4, 1, 'supot', NULL, 41, '2024-09-26 13:28:06'),
(45, 4, 1, 'hhhhhhhhhhhhh', NULL, 43, '2024-09-27 09:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'Gabriel Manialong', ''),
(2, 'test', ''),
(3, 'gabriel number2', ''),
(4, 'gab', '$2y$10$0/D5HxppYMM5A43vuBR7h.HY80N89URn0f9/3xBF6OFwR8nZx.UCG'),
(5, 'admin', '$2y$10$S.7NcNqbbz42V4VeyR7Xb.JztIrZGQ5aY4PCfWxIUNwcc8PhEVdmu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
