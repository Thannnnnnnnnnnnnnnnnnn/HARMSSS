-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 08, 2025 at 12:38 AM
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
-- Database: `sub_user_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(255) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `dept_name`) VALUES
(1, 'HR 1 & 2');

-- --------------------------------------------------------

--
-- Table structure for table `department_accounts`
--

DROP TABLE IF EXISTS `department_accounts`;
CREATE TABLE IF NOT EXISTS `department_accounts` (
  `dept_accounts_id` int NOT NULL,
  `department_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`dept_accounts_id`),
  KEY `department_id` (`department_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_audit_trail`
--

DROP TABLE IF EXISTS `department_audit_trail`;
CREATE TABLE IF NOT EXISTS `department_audit_trail` (
  `dept_audit_trail_id` int NOT NULL AUTO_INCREMENT,
  `department_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `department_affected` enum('HR part 1&2') COLLATE utf8mb4_general_ci NOT NULL,
  `module_affected` enum('recruitment and applicant management','new hire on board and self service','learning management and training management','performance management','competency management','succession planning','social recognition') COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dept_audit_trail_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_audit_trail`
--

INSERT INTO `department_audit_trail` (`dept_audit_trail_id`, `department_id`, `user_id`, `action`, `description`, `department_affected`, `module_affected`, `timestamp`) VALUES
(2, 1, 7, 'create', 'Created a new user account', 'HR part 1&2', 'recruitment and applicant management', '2025-05-04 07:53:13'),
(3, 1, 7, 'update', 'Updated user account with the user ID: 12', 'HR part 1&2', 'recruitment and applicant management', '2025-05-04 08:30:14'),
(4, 1, 7, 'delete', 'Deleted user account with the user ID: 12', 'HR part 1&2', 'recruitment and applicant management', '2025-05-04 08:31:13'),
(5, 1, 7, 'update', 'Updated applicant information with the applicant ID: 160', 'HR part 1&2', 'recruitment and applicant management', '2025-05-05 13:55:46'),
(6, 1, 7, 'update', 'Updated interview schedule with the schedule ID: 159for applicant: Alea', 'HR part 1&2', 'recruitment and applicant management', '2025-05-05 14:17:09'),
(7, 1, 14, 'Update', 'Updated application with ID: 161', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 07:46:37'),
(8, 1, 7, 'update', 'admin: 7 just updated a User account with the user_id: 8', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 09:46:40'),
(9, 1, 7, 'update', 'admin: zesymiz just updated a User account with the user_id: 14', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 09:48:34'),
(10, 1, 7, 'create', 'admin: zesymiz just created a new user account with the user ID: 15', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 10:32:08'),
(11, 1, 7, 'delete', 'admin: zesymiz Deleted a user account with the user ID: ', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 10:34:12'),
(12, 1, 7, 'delete', 'admin: zesymiz Deleted a user account with the user ID: 1_POST[\'id\']', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 10:36:39'),
(13, 1, 7, 'create', 'admin: zesymiz just created a new user account with the user ID: 16', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 10:37:42'),
(14, 1, 7, 'delete', 'admin: zesymiz Deleted a user account with the user ID: 16', 'HR part 1&2', 'recruitment and applicant management', '2025-05-07 10:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `department_log_history`
--

DROP TABLE IF EXISTS `department_log_history`;
CREATE TABLE IF NOT EXISTS `department_log_history` (
  `dept_log_id` int NOT NULL,
  `department_id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_type` enum('login','logout','login failed','session timeout') COLLATE utf8mb4_general_ci NOT NULL,
  `failure_reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `login_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` timestamp NOT NULL,
  PRIMARY KEY (`dept_log_id`),
  KEY `department_id` (`department_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_transaction`
--

DROP TABLE IF EXISTS `department_transaction`;
CREATE TABLE IF NOT EXISTS `department_transaction` (
  `dept_transc_id` int NOT NULL AUTO_INCREMENT,
  `department_id` int NOT NULL,
  `user_id` int NOT NULL,
  `transaction_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dept_transc_id`),
  KEY `department_id` (`department_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_transaction`
--

INSERT INTO `department_transaction` (`dept_transc_id`, `department_id`, `user_id`, `transaction_type`, `description`, `timestamp`) VALUES
(1, 1, 14, 'application submission', 'UserID: 14Submitted an application for:Maiores eos molesti', '2025-05-06 08:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
CREATE TABLE IF NOT EXISTS `user_account` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `department_id` int NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','manager','recruiter','hiring manager','applicant') COLLATE utf8mb4_general_ci NOT NULL,
  `register_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `department_id`, `first_name`, `last_name`, `username`, `email`, `password`, `role`, `register_type`, `created_at`, `updated_at`) VALUES
(14, 1, 'Jade', 'Griffith', 'Griffith', 'jelalaz@mailinator.com', '$2y$10$..DiVIPxB1wh.90RSWWwMOMUa/ZQvn08CGgsxzeDiK6kcy1teaXq2', 'applicant', 'standard', '2025-05-06 15:31:43', '2025-05-06 15:31:43'),
(13, 1, 'Rainiel', 'Santos', 'Rainiel Santos', 'rainielsantos7@gmail.com', '', 'applicant', 'google', '2025-05-06 15:28:27', '2025-05-06 15:28:27'),
(8, 1, 'Noelle', 'Kinney', 'hexew', 'jiquw@mailinator.com', '$2y$10$joI.xXN34yJzKGaEHORs4.dnM/djeFn4LpLIj2jhwva4c/6OBA4mq', 'admin', 'created by admin', '2025-05-04 15:37:59', '2025-05-04 15:37:59'),
(7, 1, 'Celeste', 'Preston', 'zesymiz', 'tixas@mailinator.com', '$2y$10$8923LGguSs6EoxWfoTIaSO6IsAvSeXT9981aWBKk2Y3fwpwbmzQs2', 'admin', 'standard', '2025-05-04 15:25:55', '2025-05-04 15:25:55');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
