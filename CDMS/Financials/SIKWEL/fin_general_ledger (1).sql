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
-- Database: `fin_general_ledger`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `AccountID` int(11) NOT NULL,
  `AccountName` varchar(255) NOT NULL,
  `AccountType` enum('Asset','Liability') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`AccountID`, `AccountName`, `AccountType`) VALUES
(1, 'Utilities', 'Asset'),
(14, 'asd', 'Liability');

-- --------------------------------------------------------

--
-- Table structure for table `journalentries`
--

CREATE TABLE `journalentries` (
  `EntryID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `TransactionID` int(11) NOT NULL,
  `EntryType` enum('Debit','Credit') NOT NULL,
  `Amount` int(11) NOT NULL,
  `EntryDate` datetime(6) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journalentries`
--

INSERT INTO `journalentries` (`EntryID`, `AccountID`, `TransactionID`, `EntryType`, `Amount`, `EntryDate`, `Description`) VALUES
(33, 0, 0, 'Debit', 0, '0000-00-00 00:00:00.000000', '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `EntryID` int(11) NOT NULL,
  `PaymentID` int(11) NOT NULL,
  `AllocationID` int(11) NOT NULL,
  `AdjustmentID` int(11) NOT NULL,
  `PayablePaymentID` int(11) NOT NULL,
  `TransactionFrom` enum('Guest','Vendor','Budget','Employee') NOT NULL,
  `TransactionDate` date NOT NULL DEFAULT current_timestamp(),
  `BudgetAllocated` varchar(400) NOT NULL,
  `BudgetName` varchar(400) NOT NULL,
  `Allocated_Department` varchar(500) NOT NULL,
  `AdjustmentAmount` varchar(400) NOT NULL,
  `PaymentMethod` varchar(400) NOT NULL,
  `GuestName` varchar(255) DEFAULT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `EntryID`, `PaymentID`, `AllocationID`, `AdjustmentID`, `PayablePaymentID`, `TransactionFrom`, `TransactionDate`, `BudgetAllocated`, `BudgetName`, `Allocated_Department`, `AdjustmentAmount`, `PaymentMethod`, `GuestName`, `TotalAmount`) VALUES
(89, 0, 0, 0, 0, 32, 'Vendor', '2025-04-15', '1000', 'SAHOD', 'PAYROLL', '', 'Cash', NULL, NULL),
(90, 0, 0, 0, 39, 0, 'Budget', '2025-04-26', '4000', 'SAHOD', 'PAYROLL', '3000', '', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `journalentries`
--
ALTER TABLE `journalentries`
  ADD PRIMARY KEY (`EntryID`),
  ADD KEY `AccountID` (`AccountID`),
  ADD KEY `TransactionID` (`TransactionID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `EntryID` (`EntryID`),
  ADD KEY `PaymentID` (`PaymentID`),
  ADD KEY `AllocationID` (`AllocationID`),
  ADD KEY `AdjustmentID` (`AdjustmentID`),
  ADD KEY `PayableID` (`PayablePaymentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `journalentries`
--
ALTER TABLE `journalentries`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
