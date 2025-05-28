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
-- Database: `fin_disbursement`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `ApprovalID` int(11) NOT NULL,
  `AllocationID` int(11) NOT NULL,
  `RequestID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `ApproverID` int(11) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `DateOfApproval` datetime DEFAULT NULL,
  `RejectReason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`ApprovalID`, `AllocationID`, `RequestID`, `Amount`, `ApproverID`, `Status`, `DateOfApproval`, `RejectReason`) VALUES
(47, 42, 49, 45000.00, 101, 'Approved', '2025-04-15 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `disbursementrequests`
--

CREATE TABLE `disbursementrequests` (
  `RequestID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `AllocationID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `DateOfRequest` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disbursementrequests`
--

INSERT INTO `disbursementrequests` (`RequestID`, `EmployeeID`, `AllocationID`, `Amount`, `DateOfRequest`, `Status`) VALUES
(49, 101, 42, 45000.00, '2025-04-15 11:23:44', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `EmployeeID` int(11) NOT NULL,
  `EmployeeName` varchar(255) NOT NULL,
  `Types` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`EmployeeID`, `EmployeeName`, `Types`) VALUES
(101, 'John Doe', 'Vendor'),
(102, 'Jane Smith', 'Employee'),
(103, 'Alice Johnson', 'Employee'),
(105, 'Shin Ryujin', 'Vendor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`ApprovalID`),
  ADD KEY `RequestID` (`RequestID`,`ApproverID`),
  ADD KEY `AllocationID` (`AllocationID`);

--
-- Indexes for table `disbursementrequests`
--
ALTER TABLE `disbursementrequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `EmployeeID` (`EmployeeID`,`AllocationID`),
  ADD KEY `fk_xallocationID` (`AllocationID`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `ApprovalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `disbursementrequests`
--
ALTER TABLE `disbursementrequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `fk_allocationID` FOREIGN KEY (`AllocationID`) REFERENCES `fin_budget_management`.`budgetallocations` (`AllocationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `disbursementrequests`
--
ALTER TABLE `disbursementrequests`
  ADD CONSTRAINT `fk_xallocationID` FOREIGN KEY (`AllocationID`) REFERENCES `fin_budget_management`.`budgetallocations` (`AllocationID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
