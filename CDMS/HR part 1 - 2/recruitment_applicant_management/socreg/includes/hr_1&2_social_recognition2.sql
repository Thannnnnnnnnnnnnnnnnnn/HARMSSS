-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 17, 2025 at 12:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr_1&2_social_recognition2`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `AwardID` int(11) NOT NULL,
  `AwardName` varchar(255) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`AwardID`, `AwardName`, `CategoryID`, `Description`, `CreatedAt`) VALUES
(1, 'Team Player of the Month', 1, 'Awarded to employees who demonstrate exceptional teamwork', '2025-04-03 15:46:39'),
(2, 'Innovation Champion', 2, 'Recognizing employees who bring innovative ideas to life', '2025-04-03 15:46:39'),
(3, 'Customer Service Star', 3, 'Awarded for outstanding customer service and support', '2025-04-03 15:46:39'),
(4, 'Leadership Excellence', 4, 'Recognizing exceptional leadership and management skills', '2025-04-03 15:46:39'),
(5, 'Employee of the Month', 5, 'Recognizing outstanding dedication and consistent excellence this month!', '2025-04-03 15:46:39'),
(43, 'dasdsad', 3, 'dasdsa', '2025-05-17 10:15:24'),
(44, 'asda', 3, 'asdasd', '2025-05-17 10:19:19'),
(45, 'asda', 3, 'asdasd', '2025-05-17 10:20:19'),
(46, 'adsadasdasdsa', 3, 'dsadasdasd', '2025-05-17 10:22:20'),
(47, 'zxcz', 2, 'asdasdasd', '2025-05-17 10:40:52');

--
-- Triggers `awards`
--
DELIMITER $$
CREATE TRIGGER `after_award_insert` AFTER INSERT ON `awards` FOR EACH ROW BEGIN
    INSERT INTO notifications (type, reference_id, title, message)
    VALUES ('award', NEW.AwardID, 'New Award Created', CONCAT(NEW.AwardName, ' award has been created'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `Department_ID` int(11) NOT NULL,
  `Dept_Name` varchar(255) NOT NULL,
  `Dept_Status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `Dept_Email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`Department_ID`, `Dept_Name`, `Dept_Status`, `Dept_Email`) VALUES
(1, 'Human Resources 1&2', 'Active', 'hr1and2@example.com'),
(2, 'Human Resources 3&4', 'Active', 'hr3and4@example.com'),
(3, 'Core Transaction 1', 'Active', 'coretrans1@example.com'),
(4, 'Core Transaction 2', 'Active', 'coretrans2@example.com'),
(5, 'Core Transaction 3', 'Active', 'coretrans3@example.com'),
(6, 'Logistics 1', 'Active', 'logistics1@example.com'),
(7, 'Logistics 2', 'Active', 'logistics2@example.com'),
(8, 'Financials', 'Active', 'financials@example.com'),
(9, 'User Management', 'Active', 'usermgmt@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `department_accounts`
--

CREATE TABLE `department_accounts` (
  `dept_accounts_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_accounts`
--

INSERT INTO `department_accounts` (`dept_accounts_id`, `department_id`, `user_id`, `name`, `password`, `role`, `status`, `email`) VALUES
(1, 1, 101, 'Super Admin User', 'SAdmin123', 'super admin', 'active', 'superadmin@example.com'),
(12, 1, 106, 'asdasdas', 'asdasdasd', 'super admin', 'active', 'asdsadsa@asdasds.cpo'),
(3, 1, 102, 'Admin User', 'Admin123', 'admin', 'active', 'admin@example.com'),
(4, 1, 103, 'Manager User', 'Manager123', 'manager', 'active', 'manager@example.com'),
(5, 1, 104, 'Staff User', 'Staff123', 'staff', 'active', 'staff@example.com'),
(6, 1, 105, 'Applicant User', 'password123', 'applicant', 'active', 'applicant@example.com'),
(15, 4, 109, 'dasdas', 'dadasdsadasdsadsasad', 'super admin', 'active', 'dasdasdas@asdasdas.com'),
(14, 1, 108, 'dasdasd', 'sadasdasdasdasdasdasd', 'super admin', 'active', 'saddasd@dasdasdas.com');

-- --------------------------------------------------------

--
-- Table structure for table `department_audit_trail`
--

CREATE TABLE `department_audit_trail` (
  `dept_audit_trail_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_audit_trail_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `department_affected` enum('HR part 1 - 2','HR part 3 - 4','Log1','Log2','Core 1','Core 2','Core 3','financials','User Management') NOT NULL,
  `module_affected` enum('recruitment and applicant management','new hire on board and self service','learning management and training management','performance management','competency management','succession planning','social recognition','user management') NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_audit_trail`
--

INSERT INTO `department_audit_trail` (`dept_audit_trail_id`, `department_id`, `user_id`, `user_audit_trail_id`, `action`, `department_affected`, `module_affected`, `role`, `user_name`) VALUES
(1, 1, 4, 0, 'User Login', '', '', 'manager', 'Manager User'),
(2, 1, 4, 0, 'Add Award', '', '', 'manager', 'Manager User'),
(3, 0, 4, 0, 'Add Recognition', '', '', 'manager', 'Manager User'),
(4, 1, 3, 0, 'User Login', '', '', 'admin', 'Admin User'),
(5, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(6, 1, 3, 0, 'Add User', '', '', 'admin', 'Admin User'),
(7, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(8, 1, 3, 0, 'Delete User', '', '', 'admin', 'Admin User'),
(9, 1, 3, 0, 'Delete User', '', '', 'admin', 'Admin User'),
(10, 1, 3, 0, 'Delete User', '', '', 'admin', 'Admin User'),
(11, 1, 3, 0, 'Delete User', '', '', 'admin', 'Admin User'),
(12, 1, 3, 0, 'Delete User', '', '', 'admin', 'Admin User'),
(13, 1, 3, 0, 'Add User', '', '', 'admin', 'Admin User'),
(14, 1, 3, 0, 'Add User', '', '', 'admin', 'Admin User'),
(15, 1, 3, 0, 'Add Award', '', '', 'admin', 'Admin User'),
(16, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(17, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(18, 1, 3, 0, 'Delete User', 'User Management', 'user management', 'admin', 'Admin User'),
(19, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(20, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(21, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(22, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(23, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(24, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(25, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(26, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(27, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(28, 1, 3, 0, 'Delete User', 'Core 1', 'performance management', 'admin', 'Admin User'),
(29, 1, 3, 0, 'Add User', '', '', 'admin', 'Admin User'),
(30, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(31, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(32, 1, 3, 0, 'User Login', 'User Management', '', 'admin', 'Admin User'),
(33, 0, 3, 0, 'Add User', '', '', 'admin', 'Admin User'),
(34, 8, 13, 0, 'User Login', 'User Management', '', 'super admin', 'asd'),
(35, 8, 13, 0, 'Delete Award', '', '', 'super admin', 'asd'),
(36, 1, 3, 0, 'User Login', 'User Management', '', 'admin', 'Admin User'),
(37, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(38, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(39, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(40, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(41, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(42, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(43, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(44, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(45, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(46, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(47, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(48, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(49, 1, 3, 0, 'Add User', 'HR part 1 - 2', '', 'admin', 'Admin User'),
(50, 1, 3, 0, 'Delete User', 'HR part 1 - 2', 'user management', 'admin', 'Admin User'),
(51, 1, 3, 0, 'Add Award', 'Core 1', 'social recognition', 'admin', 'Admin User'),
(52, 1, 3, 0, 'Delete Award', '', '', 'admin', 'Admin User'),
(53, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(54, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', '', 'admin', 'Admin User'),
(55, 4, 3, 0, 'Add User', 'Core 2', '', 'admin', 'Admin User'),
(56, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(57, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', '', 'admin', 'Admin User'),
(58, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(59, 1, 3, 0, 'Add Recognition', '', '', 'admin', 'Admin User'),
(60, 1, 3, 0, 'Delete Recognition', '', '', 'admin', 'Admin User'),
(61, 1, 3, 0, 'Add Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(62, 1, 3, 0, 'Delete Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(63, 1, 3, 0, 'Update Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(64, 1, 3, 0, 'Update Award', 'HR part 1 - 2', '', 'admin', 'Admin User'),
(65, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(66, 1, 3, 0, 'Add Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(67, 1, 3, 0, 'Update Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(68, 1, 3, 0, 'Delete Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(69, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(70, 1, 3, 0, 'User Login', 'User Management', '', 'admin', 'Admin User'),
(71, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(72, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(73, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(74, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(75, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(76, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(77, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(78, 1, 3, 0, 'Add Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(79, 1, 3, 0, 'Update Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(80, 1, 3, 0, 'Delete Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(81, 6, 3, 0, 'Add User', 'Log1', '', 'admin', 'Admin User'),
(82, 1, 3, 0, 'Delete User', 'HR part 1 - 2', 'user management', 'admin', 'Admin User'),
(83, 1, 3, 0, 'Delete Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(84, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(85, 1, 3, 0, 'Delete Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(86, 1, 3, 0, 'Add Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(87, 1, 3, 0, 'Delete Category', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(88, 1, 3, 0, 'Update Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(89, 1, 3, 0, 'Delete Recognition', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(90, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(91, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(92, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(93, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(94, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(95, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(96, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(97, 1, 3, 0, 'Add Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(98, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User'),
(99, 1, 3, 0, 'Update Award', 'HR part 1 - 2', 'social recognition', 'admin', 'Admin User');

-- --------------------------------------------------------

--
-- Table structure for table `department_log_history`
--

CREATE TABLE `department_log_history` (
  `dept_log_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `user_log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `failure_reason` varchar(255) NOT NULL,
  `role` enum('super admnin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_log_history`
--

INSERT INTO `department_log_history` (`dept_log_id`, `department_id`, `user_log_id`, `user_id`, `failure_reason`, `role`, `user_name`) VALUES
(1, 1, 0, 4, 'none', 'manager', 'Manager User'),
(2, 1, 0, 3, 'none', 'admin', 'Admin User'),
(3, 1, 0, 3, 'none', 'admin', 'Admin User'),
(4, 1, 0, 3, 'none', 'admin', 'Admin User'),
(5, 1, 0, 3, 'none', 'admin', 'Admin User'),
(6, 1, 0, 3, 'none', 'admin', 'Admin User'),
(7, 1, 0, 3, 'none', 'admin', 'Admin User'),
(8, 1, 0, 3, 'none', 'admin', 'Admin User'),
(9, 1, 0, 3, 'none', 'admin', 'Admin User'),
(10, 1, 0, 3, 'none', 'admin', 'Admin User'),
(11, 1, 0, 3, 'none', 'admin', 'Admin User'),
(12, 1, 0, 3, 'none', 'admin', 'Admin User'),
(13, 1, 0, 3, 'none', 'admin', 'Admin User'),
(14, 1, 0, 3, 'none', 'admin', 'Admin User'),
(15, 1, 0, 3, 'none', 'admin', 'Admin User'),
(16, 0, 0, 3, 'none', 'admin', 'Admin User'),
(17, 8, 0, 13, 'none', '', 'asd'),
(18, 1, 0, 3, 'none', 'admin', 'Admin User'),
(19, 1, 0, 3, 'none', 'admin', 'Admin User'),
(20, 1, 0, 3, 'none', 'admin', 'Admin User'),
(21, 1, 0, 3, 'none', 'admin', 'Admin User'),
(22, 1, 0, 3, 'none', 'admin', 'Admin User'),
(23, 1, 0, 3, 'none', 'admin', 'Admin User'),
(24, 1, 0, 3, 'none', 'admin', 'Admin User');

-- --------------------------------------------------------

--
-- Table structure for table `department_transaction`
--

CREATE TABLE `department_transaction` (
  `dept_transc_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_transc_id` int(11) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_transaction`
--

INSERT INTO `department_transaction` (`dept_transc_id`, `department_id`, `user_id`, `user_transc_id`, `transaction_type`, `role`, `user_name`) VALUES
(0, 1, 3, 0, 'Add User', 'admin', 'Admin User');

-- --------------------------------------------------------

--
-- Table structure for table `employeerecognition`
--

CREATE TABLE `employeerecognition` (
  `RecognitionID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `Employee name` varchar(255) NOT NULL,
  `AwardID` int(11) NOT NULL,
  `Awards name` varchar(255) NOT NULL,
  `RecognitionDate` datetime DEFAULT current_timestamp(),
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeerecognition`
--

INSERT INTO `employeerecognition` (`RecognitionID`, `EmployeeID`, `Employee name`, `AwardID`, `Awards name`, `RecognitionDate`, `Description`) VALUES
(2, 2, 'Sarah Johnson', 4, 'Leadership Excellence', '2025-04-25 00:00:00', 'Outstanding leadership in HR initiatives'),
(17, 1, '', 2, '', '2025-05-10 00:00:00', 'Recognizing employees who bring innovative ideas to life'),
(18, 8, '', 1, '', '2025-05-10 00:00:00', 'Awarded to employees who demonstrate exceptional teamwork'),
(19, 5, '', 2, '', '2025-05-10 00:00:00', 'Recognizing employees who bring innovative ideas to life'),
(20, 3, '', 5, '', '2025-05-10 00:00:00', 'sdvfggfbhgfhn'),
(21, 1, '', 2, '', '2025-05-10 00:00:00', 'Recognizing employees who bring innovative ideas to life'),
(22, 7, '', 4, '', '2025-05-16 00:00:00', 'Recognizing exceptional leadership and management skills');

--
-- Triggers `employeerecognition`
--
DELIMITER $$
CREATE TRIGGER `after_recognition_insert` AFTER INSERT ON `employeerecognition` FOR EACH ROW BEGIN
    INSERT INTO notifications (type, reference_id, title, message)
    SELECT 'recognition', NEW.RecognitionID, 'New Recognition', 
           CONCAT(e.`Employee name`, ' received the ', a.AwardName, ' award')
    FROM employees e
    JOIN awards a ON a.AwardID = NEW.AwardID
    WHERE e.EmployeeID = NEW.EmployeeID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `EmployeeID` int(11) NOT NULL,
  `Employee name` varchar(255) NOT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `HireDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`EmployeeID`, `Employee name`, `Department`, `Position`, `Email`, `Phone`, `HireDate`) VALUES
(1, 'John Doe', 'IT', 'Software Engineer', 'john.doe@example.com', '1234567890', '2023-01-15'),
(2, 'Jane Smith', 'HR', 'HR Manager', 'jane.smith@example.com', '0987654321', '2023-02-20'),
(3, 'Mike Johnson', 'Sales', 'Sales Representative', 'mike.johnson@example.com', '1122334455', '2023-03-10'),
(4, 'Sarah Williams', 'Marketing', 'Marketing Specialist', 'sarah.williams@example.com', '5566778899', '2023-04-05'),
(5, 'David Brown', 'Finance', 'Financial Analyst', 'david.brown@example.com', '9988776655', '2023-05-12'),
(6, 'John Smith', 'IT', 'Senior Developer', 'john.smith@example.com', '1234567890', '2023-01-15'),
(7, 'Sarah Johnson', 'HR', 'HR Manager', 'sarah.johnson@example.com', '0987654321', '2023-02-20'),
(8, 'Michael Brown', 'Sales', 'Sales Manager', 'michael.brown@example.com', '1122334455', '2023-03-10'),
(9, 'Emily Davis', 'Marketing', 'Marketing Specialist', 'emily.davis@example.com', '5566778899', '2023-04-05'),
(10, 'David Wilson', 'Finance', 'Financial Analyst', 'david.wilson@example.com', '9988776655', '2023-05-12');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `reference_id`, `title`, `message`, `read`, `created_at`) VALUES
(1, 'award', 1, 'New Award Created', 'Team Player of the Month award has been created', 1, '2025-05-10 14:52:17'),
(2, 'recognition', 1, 'New Recognition', 'John Smith received the Team Player of the Month award', 1, '2025-05-10 14:52:17'),
(3, 'category', 1, 'New Category', 'Teamwork category has been created', 1, '2025-05-10 14:52:17'),
(4, 'recognition', 17, 'New Recognition', 'John Doe received the Innovation Champion award', 0, '2025-05-10 15:12:00'),
(5, 'recognition', 18, 'New Recognition', 'Michael Brown received the Team Player of the Month award', 0, '2025-05-10 15:15:19'),
(6, 'recognition', 19, 'New Recognition', 'David Brown received the Innovation Champion award', 0, '2025-05-10 15:20:51'),
(7, 'recognition', 20, 'New Recognition', 'Mike Johnson received the Employee of the Month award', 0, '2025-05-10 15:27:45'),
(8, 'award', 15, 'New Award Created', 'vfdgdsfgf award has been created', 0, '2025-05-14 22:58:53'),
(9, 'award', 17, 'New Award Created', 'jhgjghjkgh award has been created', 0, '2025-05-14 23:04:11'),
(10, 'award', 18, 'New Award Created', 'Employee of the Month award has been created', 0, '2025-05-14 23:04:59'),
(11, 'recognition', 21, 'New Recognition', 'John Doe received the Innovation Champion award', 0, '2025-05-16 07:10:16'),
(12, 'award', 19, 'New Award Created', 'dsafdgfdg award has been created', 0, '2025-05-16 07:30:42'),
(13, 'award', 20, 'New Award Created', 'dsafdgfdg award has been created', 0, '2025-05-16 07:36:26'),
(14, 'recognition', 22, 'New Recognition', 'Sarah Johnson received the Leadership Excellence award', 0, '2025-05-16 08:00:24'),
(15, 'award', 21, 'New Award Created', 'dasdsad award has been created', 0, '2025-05-16 09:42:25'),
(16, 'award', 22, 'New Award Created', 'dasdsad award has been created', 0, '2025-05-16 09:43:34'),
(17, 'award', 23, 'New Award Created', 'asdasdas award has been created', 0, '2025-05-16 10:08:41'),
(18, 'award', 24, 'New Award Created', 'asdasdsa award has been created', 0, '2025-05-16 10:13:05'),
(19, 'award', 25, 'New Award Created', 'sdsadas award has been created', 0, '2025-05-16 10:14:54'),
(20, 'award', 26, 'New Award Created', 'asdasdsada award has been created', 0, '2025-05-16 10:18:25'),
(21, 'award', 27, 'New Award Created', 'asdasdas award has been created', 0, '2025-05-16 10:27:29'),
(22, 'award', 28, 'New Award Created', 'asdasdasd award has been created', 0, '2025-05-16 10:39:38'),
(23, 'award', 29, 'New Award Created', 'dasdas award has been created', 0, '2025-05-16 17:47:37'),
(24, 'award', 30, 'New Award Created', 'asdasd award has been created', 0, '2025-05-16 17:54:57'),
(25, 'award', 31, 'New Award Created', 'dasdas award has been created', 0, '2025-05-16 17:59:10'),
(26, 'award', 32, 'New Award Created', 'asdasda award has been created', 0, '2025-05-16 18:04:10'),
(27, 'award', 33, 'New Award Created', 'dsadasd award has been created', 0, '2025-05-16 18:04:49'),
(28, 'award', 34, 'New Award Created', 'dsadas award has been created', 0, '2025-05-16 18:08:16'),
(29, 'award', 35, 'New Award Created', 'dsadsadas award has been created', 0, '2025-05-16 18:11:42'),
(30, 'award', 36, 'New Award Created', 'dasdasd award has been created', 0, '2025-05-16 18:17:08'),
(31, 'award', 37, 'New Award Created', 'ffvd award has been created', 0, '2025-05-16 18:21:11'),
(32, 'award', 38, 'New Award Created', 'asdasdsa award has been created', 0, '2025-05-16 18:29:38'),
(33, 'recognition', 23, 'New Recognition', 'Jane Smith received the Employee of the Month award', 0, '2025-05-16 18:32:02'),
(34, 'recognition', 24, 'New Recognition', 'Emily Davis received the Innovation Champion award', 0, '2025-05-16 18:34:38'),
(35, 'recognition', 25, 'New Recognition', 'Emily Davis received the Innovation Champion award', 0, '2025-05-16 18:35:19'),
(36, 'category', 12, 'New Category', 'adasd category has been created', 0, '2025-05-16 18:39:26'),
(37, 'award', 39, 'New Award Created', 'asdasd award has been created', 0, '2025-05-17 06:54:08'),
(38, 'award', 40, 'New Award Created', 'asdadas award has been created', 0, '2025-05-17 07:08:00'),
(39, 'award', 41, 'New Award Created', 'adsadsa award has been created', 0, '2025-05-17 07:08:08'),
(40, 'category', 13, 'New Category', 'asda category has been created', 0, '2025-05-17 07:08:59'),
(41, 'award', 42, 'New Award Created', 'asdasdda award has been created', 0, '2025-05-17 08:58:21'),
(42, 'category', 14, 'New Category', 'asdasdsadsad category has been created', 0, '2025-05-17 09:03:11'),
(43, 'award', 43, 'New Award Created', 'dasdsad award has been created', 0, '2025-05-17 10:15:24'),
(44, 'award', 44, 'New Award Created', 'asda award has been created', 0, '2025-05-17 10:19:19'),
(45, 'award', 45, 'New Award Created', 'asda award has been created', 0, '2025-05-17 10:20:19'),
(46, 'award', 46, 'New Award Created', 'adsa award has been created', 0, '2025-05-17 10:22:20'),
(47, 'award', 47, 'New Award Created', 'adasdasd award has been created', 0, '2025-05-17 10:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `recognitioncategories`
--

CREATE TABLE `recognitioncategories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recognitioncategories`
--

INSERT INTO `recognitioncategories` (`CategoryID`, `CategoryName`, `Description`, `CreatedAt`) VALUES
(1, 'Teamwork', 'Recognizing employees who excel in collaboration and team spirit', '2025-04-03 15:46:39'),
(2, 'Innovation', 'Awarding creative thinking and innovative solutions', '2025-04-03 15:46:39'),
(3, 'Customer Service', 'Recognizing exceptional customer service and support', '2025-04-03 15:46:39'),
(4, 'Leadership', 'Acknowledging outstanding leadership qualities', '2025-04-03 15:46:39'),
(5, 'Excellence', 'Celebrating overall excellence in performance', '2025-04-03 15:46:39');

--
-- Triggers `recognitioncategories`
--
DELIMITER $$
CREATE TRIGGER `after_category_insert` AFTER INSERT ON `recognitioncategories` FOR EACH ROW BEGIN
    INSERT INTO notifications (type, reference_id, title, message)
    VALUES ('category', NEW.CategoryID, 'New Category', CONCAT(NEW.CategoryName, ' category has been created'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deparment` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `department_id`, `role`, `password`, `name`, `deparment`, `status`, `email`) VALUES
(1, 1, 'admin', 'password123', 'Test Admin', 'HR', 'active', 'admin@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_audit_trail`
--

CREATE TABLE `user_audit_trail` (
  `user_audit_trail_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `department_affected` enum('HR part 1 - 2','HR part part 3 - 4','Log1','Log2','Core 1','core 2','core 3','financials') NOT NULL,
  `module_affected` enum('recruitment and applicant management','new hire on board and self service','learning management and training management','performance management','competency management','succession management','social recognition') NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_log_history`
--

CREATE TABLE `user_log_history` (
  `user_log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_status` enum('active','failed') NOT NULL,
  `log_date/time` datetime NOT NULL,
  `failure_reason` enum('wrong password','username & password is wrong') DEFAULT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_transaction`
--

CREATE TABLE `user_transaction` (
  `user_transc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `transaction_date/time` datetime NOT NULL,
  `role` enum('super admin','admin','manager','staff','applicant') NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`AwardID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`Department_ID`);

--
-- Indexes for table `department_accounts`
--
ALTER TABLE `department_accounts`
  ADD PRIMARY KEY (`dept_accounts_id`);

--
-- Indexes for table `department_audit_trail`
--
ALTER TABLE `department_audit_trail`
  ADD PRIMARY KEY (`dept_audit_trail_id`),
  ADD KEY `user_audit_trail_id` (`user_audit_trail_id`);

--
-- Indexes for table `department_log_history`
--
ALTER TABLE `department_log_history`
  ADD PRIMARY KEY (`dept_log_id`);

--
-- Indexes for table `department_transaction`
--
ALTER TABLE `department_transaction`
  ADD PRIMARY KEY (`dept_transc_id`);

--
-- Indexes for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  ADD PRIMARY KEY (`RecognitionID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `AwardID` (`AwardID`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_reference` (`type`,`reference_id`);

--
-- Indexes for table `recognitioncategories`
--
ALTER TABLE `recognitioncategories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_audit_trail`
--
ALTER TABLE `user_audit_trail`
  ADD PRIMARY KEY (`user_audit_trail_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_log_history`
--
ALTER TABLE `user_log_history`
  ADD PRIMARY KEY (`user_log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_transaction`
--
ALTER TABLE `user_transaction`
  ADD PRIMARY KEY (`user_transc_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `AwardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `Department_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `department_accounts`
--
ALTER TABLE `department_accounts`
  MODIFY `dept_accounts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `department_audit_trail`
--
ALTER TABLE `department_audit_trail`
  MODIFY `dept_audit_trail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `department_log_history`
--
ALTER TABLE `department_log_history`
  MODIFY `dept_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  MODIFY `RecognitionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `recognitioncategories`
--
ALTER TABLE `recognitioncategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `awards`
--
ALTER TABLE `awards`
  ADD CONSTRAINT `awards_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `recognitioncategories` (`CategoryID`);

--
-- Constraints for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  ADD CONSTRAINT `employeerecognition_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employees` (`EmployeeID`),
  ADD CONSTRAINT `employeerecognition_ibfk_2` FOREIGN KEY (`AwardID`) REFERENCES `awards` (`AwardID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
