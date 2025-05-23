-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 19, 2025 at 08:21 PM
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
-- Database: `cr1_usm`
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
(1, 'C120306', 's22122422', 'Robert Barredo Quilenderino', '#QU8080', 'staff', 'Active', 'robertbarredoquilenderino@gmail.com'),
(2, 'C120306', 's22019081', 'Ma Althea Balcos', '#OR8080', 'staff', 'Active', 'orilla.maaltheabalcos@gmail.com'),
(3, 'C120306', 's22015844', 'Richard Gulmatico', '#GU8080', 'staff', 'Active', 'richardgulmatico28@gmail.com'),
(4, 'C120306', 's22018986', 'Cyrus Lumacang', '#LU8080', 'staff', 'Active', 'cyrusjohnlumacang1001@gmail.com'),
(5, 'C120306', 'a12345678', 'CRAdmin', '#COAD80809', 'admin', 'Active', 'admncoreone@gmail.com'),
(6, 'C120306', 'm12345678', 'CRManager', '#COMA80809', 'manager', 'Active', 'mngercoreone@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `department_logs`
--

CREATE TABLE `department_logs` (
  `Log_ID` int(11) NOT NULL,
  `Department_ID` varchar(50) DEFAULT NULL,
  `User_ID` varchar(50) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Role` enum('super admin','admin','manager','staff') DEFAULT NULL,
  `Log_Status` enum('Success','Failed') DEFAULT NULL,
  `Date_Time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_logs`
--

INSERT INTO `department_logs` (`Log_ID`, `Department_ID`, `User_ID`, `Name`, `Role`, `Log_Status`, `Date_Time`) VALUES
(1, 'C120306', 's22122422', 'Robert Barredo Quilenderino', 'staff', 'Success', '2025-05-19 16:42:12'),
(2, 'C120306', 's22122422', 'Robert Barredo Quilenderino', 'staff', 'Success', '2025-05-19 16:42:50'),
(3, NULL, 's22015844', NULL, NULL, 'Failed', '2025-05-19 16:43:34'),
(4, 'C120306', 's22015844', 'Richard Gulmatico', 'staff', 'Success', '2025-05-19 16:43:45'),
(5, 'C120306', 's22122422', 'Robert Barredo Quilenderino', 'staff', 'Success', '2025-05-19 17:34:20'),
(6, 'C120306', 's22122422', 'Robert Barredo Quilenderino', 'staff', 'Success', '2025-05-19 17:42:01'),
(7, NULL, 'dasssssssssssssssssssssssssss', NULL, NULL, 'Failed', '2025-05-19 18:13:57');

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
-- Indexes for dumped tables
--

--
-- Indexes for table `department_accounts`
--
ALTER TABLE `department_accounts`
  ADD PRIMARY KEY (`Dept_Accounts_ID`);

--
-- Indexes for table `department_logs`
--
ALTER TABLE `department_logs`
  ADD PRIMARY KEY (`Log_ID`);

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
-- AUTO_INCREMENT for table `department_logs`
--
ALTER TABLE `department_logs`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
