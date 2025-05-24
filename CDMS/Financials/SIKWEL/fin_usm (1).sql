-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Apr 26, 2025 at 07:25 AM
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
-- Database: `fin_usm`
--

-- --------------------------------------------------------

--
-- Table structure for table `department_accounts`
--

CREATE TABLE `department_accounts` (
  `Dept_Accounts_ID` int(11) NOT NULL,
  `Department_ID` varchar(50) DEFAULT NULL,
  `User_ID` varchar(50) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` enum('super admin','admin','manager','staff') DEFAULT NULL,
  `Status` enum('Active','Inactive') DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_accounts`
--

INSERT INTO `department_accounts` (`Dept_Accounts_ID`, `Department_ID`, `User_ID`, `Name`, `Password`, `Role`, `Status`, `Email`) VALUES
(1, 'F20309', 's254225000904', 'John Mark Balacy', '#BA2548080', 'staff', 'Active', 'ryujinboss4@gmail.com'),
(2, 'F20309', 's254223290904', 'Ric Jason Altamante', '#RAIN2548080', 'staff', 'Active', 'altamantericjason@gmail.com'),
(3, 'F20309', 's254124910904', 'Noriel Agbon', ' #AG2548080', 'staff', 'Active', 'norielagbon@duck.com'),
(4, 'F20309', 's254191860904', 'Jonh Paul Ogabar ', '#OG2548080', 'staff', 'Active', 'johnpaulogabar@gmail.com'),
(5, 'F20309', 's254105470904', 'John Roy Vinson Dadap', '#DA2548080', 'staff', 'Active', ' jdadap960@gmail.com'),
(6, 'F20309', 's254166290904', 'Angel Parcon', '#PA2548080', 'staff', 'Active', 'angelparcon0414@gmail.com'),
(7, 'F20309', 'A254203090902', 'Fin_admin', '#FIAD80809', 'admin', 'Active', 'admin@finusm.com'),
(8, 'F20309', 'M254203090903', 'Fin_manager', '#FAMA80809', 'manager', 'Active', 'manager@finusm.com');

-- --------------------------------------------------------

--
-- Table structure for table `department_log_history`
--

CREATE TABLE `department_log_history` (
  `Dept_Log_ID` int(11) NOT NULL,
  `Department_ID` varchar(50) DEFAULT NULL,
  `User_LogID` int(11) DEFAULT NULL,
  `User_ID` varchar(50) DEFAULT NULL,
  `Failure_reason` varchar(255) DEFAULT NULL,
  `Log_Status` varchar(50) DEFAULT NULL,
  `Log_Date_Time` varchar(100) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `Name` text DEFAULT NULL,
  `Attempt_type` varchar(100) DEFAULT NULL,
  `Attempt_count` int(11) DEFAULT NULL,
  `Cooldown_until` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_log_history`
--

INSERT INTO `department_log_history` (`Dept_Log_ID`, `Department_ID`, `User_LogID`, `User_ID`, `Failure_reason`, `Log_Status`, `Log_Date_Time`, `Role`, `Name`, `Attempt_type`, `Attempt_count`, `Cooldown_until`) VALUES
(1, 'F20309', 9, 's254223290904', NULL, 'Success', '2025-04-25 22:17:05', 'staff', 'Ric Jason Altamante', 'Login', NULL, NULL),
(2, 'F20309', 10, 's254223290904', NULL, 'Success', '2025-04-25 22:17:21', 'staff', 'Ric Jason Altamante', 'Login', NULL, NULL),
(3, 'F20309', 11, 's254223290904', NULL, 'Success', '2025-04-25 22:18:11', 'staff', 'Ric Jason Altamante', 'Login', NULL, NULL),
(4, 'F20309', 12, 's254166290904', NULL, 'Success', '2025-04-25 22:18:41', 'staff', 'Angel Parcon', 'Login', NULL, NULL),
(5, 'F20309', 13, 's254225000904', 'Invalid password', 'Failed', '2025-04-25 22:20:33', 'staff', 'John Mark Balacy', 'Login', NULL, NULL),
(6, 'F20309', 14, 's254166290904', NULL, 'Success', '2025-04-25 22:20:37', 'staff', 'Angel Parcon', 'Login', NULL, NULL),
(7, 'F20309', 15, 's254225000904', NULL, 'Success', '2025-04-25 23:09:01', 'staff', 'John Mark Balacy', 'Login', NULL, NULL),
(8, 'F20309', 16, 's254191860904', NULL, 'Success', '2025-04-25 23:10:33', 'staff', 'Jonh Paul Ogabar ', 'Login', NULL, NULL),
(9, 'F20309', 17, 's254225000904', NULL, 'Success', '2025-04-25 23:19:14', 'staff', 'John Mark Balacy', 'Login', NULL, NULL),
(10, 'F20309', 18, 's254223290904', NULL, 'Success', '2025-04-25 23:20:17', 'staff', 'Ric Jason Altamante', 'Login', NULL, NULL),
(11, 'F20309', 19, 'M254203090903', NULL, 'Success', '2025-04-25 23:20:51', 'manager', 'Fin_manager', 'Login', NULL, NULL),
(12, 'F20309', 20, 's254124910904', 'Invalid password', 'Failed', '2025-04-25 23:23:15', 'staff', 'Noriel Agbon', 'Login', NULL, NULL),
(13, 'F20309', 21, 's254124910904', 'Invalid password', 'Failed', '2025-04-25 23:23:27', 'staff', 'Noriel Agbon', 'Login', NULL, NULL),
(14, 'F20309', 22, 's254124910904', 'Invalid password', 'Failed', '2025-04-25 23:23:53', 'staff', 'Noriel Agbon', 'Login', NULL, NULL),
(15, 'F20309', 23, 's254124910904', 'Invalid password', 'Failed', '2025-04-25 23:24:00', 'staff', 'Noriel Agbon', 'Login', NULL, NULL),
(16, 'F20309', 24, 's254124910904', 'Invalid password', 'Failed', '2025-04-25 23:24:14', 'staff', 'Noriel Agbon', 'Login', NULL, NULL),
(17, 'F20309', 25, 's254191860904', NULL, 'Success', '2025-04-25 23:24:26', 'staff', 'Jonh Paul Ogabar ', 'Login', NULL, NULL),
(18, 'F20309', 26, 's254191860904', NULL, 'Success', '2025-04-25 23:24:48', 'staff', 'Jonh Paul Ogabar ', 'Login', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logs_table`
--

CREATE TABLE `logs_table` (
  `logID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Department_ID` varchar(50) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `Logstatus` enum('Failed','Success') DEFAULT NULL,
  `Role` enum('staff','admin','manager') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs_table`
--

INSERT INTO `logs_table` (`logID`, `User_ID`, `Department_ID`, `Username`, `Logstatus`, `Role`) VALUES
(1, 0, NULL, 'a', 'Failed', NULL),
(2, 0, 'F20309', 's254225000904', 'Success', 'staff'),
(3, 0, NULL, 'a', 'Failed', NULL),
(4, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(5, 0, 'F20309', 's254225000904', 'Failed', 'staff'),
(6, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(7, 0, 'F20309', 's254225000904', 'Failed', 'staff'),
(8, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(9, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(10, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(11, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(12, 0, 'F20309', 's254166290904', 'Success', 'staff'),
(13, 0, 'F20309', 's254225000904', 'Failed', 'staff'),
(14, 0, 'F20309', 's254166290904', 'Success', 'staff'),
(15, 0, 'F20309', 's254225000904', 'Success', 'staff'),
(16, 0, 'F20309', 's254191860904', 'Success', 'staff'),
(17, 0, 'F20309', 's254225000904', 'Success', 'staff'),
(18, 0, 'F20309', 's254223290904', 'Success', 'staff'),
(19, 0, 'F20309', 'M254203090903', 'Success', 'manager'),
(20, 0, 'F20309', 's254124910904', 'Failed', 'staff'),
(21, 0, 'F20309', 's254124910904', 'Failed', 'staff'),
(22, 0, 'F20309', 's254124910904', 'Failed', 'staff'),
(23, 0, 'F20309', 's254124910904', 'Failed', 'staff'),
(24, 0, 'F20309', 's254124910904', 'Failed', 'staff'),
(25, 0, 'F20309', 's254191860904', 'Success', 'staff'),
(26, 0, 'F20309', 's254191860904', 'Success', 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department_accounts`
--
ALTER TABLE `department_accounts`
  ADD PRIMARY KEY (`Dept_Accounts_ID`);

--
-- Indexes for table `department_log_history`
--
ALTER TABLE `department_log_history`
  ADD PRIMARY KEY (`Dept_Log_ID`),
  ADD KEY `User_LogID` (`User_LogID`);

--
-- Indexes for table `logs_table`
--
ALTER TABLE `logs_table`
  ADD PRIMARY KEY (`logID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department_accounts`
--
ALTER TABLE `department_accounts`
  MODIFY `Dept_Accounts_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `department_log_history`
--
ALTER TABLE `department_log_history`
  MODIFY `Dept_Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `logs_table`
--
ALTER TABLE `logs_table`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `department_log_history`
--
ALTER TABLE `department_log_history`
  ADD CONSTRAINT `department_log_history_ibfk_1` FOREIGN KEY (`User_LogID`) REFERENCES `logs_table` (`logID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
