-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 09, 2020 at 08:34 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tasks_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tasks`
--

DROP TABLE IF EXISTS `tbl_tasks`;
CREATE TABLE IF NOT EXISTS `tbl_tasks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'task id - primary key',
  `title` varchar(255) NOT NULL COMMENT 'task title',
  `description` mediumtext COMMENT 'task description',
  `deadline` datetime DEFAULT NULL COMMENT 'task deadline date',
  `completed` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'task completion status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='tasks table';

--
-- Truncate table before insert `tbl_tasks`
--

TRUNCATE TABLE `tbl_tasks`;
--
-- Dumping data for table `tbl_tasks`
--

INSERT INTO `tbl_tasks` (`id`, `title`, `description`, `deadline`, `completed`) VALUES
(1, 'task 1', 'description 1', NULL, 'N'),
(2, 'task 2', 'description 2', '2020-02-15 13:30:00', 'N'),
(3, 'task 3', 'description 3', '2020-02-17 11:29:00', 'Y'),
(4, 'task 4', 'description 4', '2020-02-21 18:23:00', 'N'),
(5, 'task 5', 'description 5', NULL, 'Y'),
(6, 'task 6', 'description 6', '2020-02-13 10:12:00', 'N');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
