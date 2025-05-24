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
-- Database: `fin_budget_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgetadjustments`
--

CREATE TABLE `budgetadjustments` (
  `AdjustmentID` int(11) NOT NULL,
  `BudgetID` int(11) NOT NULL,
  `AllocationID` int(11) NOT NULL,
  `BudgetName` varchar(255) NOT NULL,
  `BudgetAllocated` int(11) NOT NULL,
  `DepartmentName` varchar(255) NOT NULL,
  `AdjustmentReason` varchar(255) NOT NULL,
  `AdjustmentAmount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgetadjustments`
--

INSERT INTO `budgetadjustments` (`AdjustmentID`, `BudgetID`, `AllocationID`, `BudgetName`, `BudgetAllocated`, `DepartmentName`, `AdjustmentReason`, `AdjustmentAmount`) VALUES
(39, 20, 42, 'SAHOD', 4000, 'PAYROLL', 'ISSUE', 3000);

-- --------------------------------------------------------

--
-- Table structure for table `budgetallocations`
--

CREATE TABLE `budgetallocations` (
  `AllocationID` int(11) NOT NULL,
  `BudgetID` int(11) NOT NULL,
  `BudgetName` varchar(255) NOT NULL,
  `TotalAmount` int(11) NOT NULL,
  `AllocatedAmount` int(11) NOT NULL,
  `DepartmentName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgetallocations`
--

INSERT INTO `budgetallocations` (`AllocationID`, `BudgetID`, `BudgetName`, `TotalAmount`, `AllocatedAmount`, `DepartmentName`) VALUES
(42, 20, 'SAHOD', 50000, 3000, 'PAYROLL');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `BudgetID` int(11) NOT NULL,
  `BudgetName` varchar(255) NOT NULL,
  `TotalAmount` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`BudgetID`, `BudgetName`, `TotalAmount`, `StartDate`, `EndDate`) VALUES
(20, 'HR ', 50000, '2025-04-15', '2025-04-19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgetadjustments`
--
ALTER TABLE `budgetadjustments`
  ADD PRIMARY KEY (`AdjustmentID`),
  ADD KEY `BudgetID` (`BudgetID`),
  ADD KEY `AllocationID` (`AllocationID`);

--
-- Indexes for table `budgetallocations`
--
ALTER TABLE `budgetallocations`
  ADD PRIMARY KEY (`AllocationID`),
  ADD KEY `BudgetID` (`BudgetID`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`BudgetID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgetadjustments`
--
ALTER TABLE `budgetadjustments`
  MODIFY `AdjustmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `budgetallocations`
--
ALTER TABLE `budgetallocations`
  MODIFY `AllocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `BudgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgetadjustments`
--
ALTER TABLE `budgetadjustments`
  ADD CONSTRAINT `AllocationID` FOREIGN KEY (`AllocationID`) REFERENCES `budgetallocations` (`AllocationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `budgetallocations`
--
ALTER TABLE `budgetallocations`
  ADD CONSTRAINT `BudgetID` FOREIGN KEY (`BudgetID`) REFERENCES `budgets` (`BudgetID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
