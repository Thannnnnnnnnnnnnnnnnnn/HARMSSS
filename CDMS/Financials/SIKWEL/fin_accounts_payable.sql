-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Apr 26, 2025 at 07:24 AM
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
-- Database: `fin_accounts_payable`
--

-- --------------------------------------------------------

--
-- Table structure for table `payableinvoices`
--

CREATE TABLE `payableinvoices` (
  `PayableInvoiceID` int(11) NOT NULL,
  `AllocationID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `BudgetName` varchar(255) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Types` varchar(500) NOT NULL,
  `Amount` int(11) NOT NULL,
  `StartDate` date NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Approved','Pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payableinvoices`
--

INSERT INTO `payableinvoices` (`PayableInvoiceID`, `AllocationID`, `AccountID`, `BudgetName`, `Department`, `Types`, `Amount`, `StartDate`, `Status`) VALUES
(2, 40, 0, 'Pasahod', 'Payroll', 'Vendor', 250000, '2025-04-06', ''),
(3, 41, 0, 'SAMPLE', 'CORE 2', 'Vendor', 250000, '2025-04-06', 'Pending'),
(4, 42, 0, 'SAHOD', 'PAYROLL', 'Vendor', 45000, '2025-04-15', 'Pending'),
(5, 42, 0, 'SAHOD', 'PAYROLL', 'Vendor', 45000, '2025-04-15', '');

-- --------------------------------------------------------

--
-- Table structure for table `paymentschedules`
--

CREATE TABLE `paymentschedules` (
  `ScheduleID` int(11) NOT NULL,
  `PayableInvoiceID` int(11) NOT NULL,
  `PaymentSchedule` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymentschedules`
--

INSERT INTO `paymentschedules` (`ScheduleID`, `PayableInvoiceID`, `PaymentSchedule`) VALUES
(25, 2, '2025-04-06'),
(26, 5, '2025-04-15');

-- --------------------------------------------------------

--
-- Table structure for table `vendorpayments`
--

CREATE TABLE `vendorpayments` (
  `PayablePaymentID` int(11) NOT NULL,
  `PayableInvoiceID` int(11) NOT NULL,
  `PaymentStatus` enum('Upcoming',' Overdue','Completed') NOT NULL DEFAULT 'Upcoming',
  `AmountPaid` int(11) NOT NULL,
  `PaymentMethod` varchar(287) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendorpayments`
--

INSERT INTO `vendorpayments` (`PayablePaymentID`, `PayableInvoiceID`, `PaymentStatus`, `AmountPaid`, `PaymentMethod`) VALUES
(30, 2, 'Completed', 250000, 'Bank Transfer'),
(31, 5, 'Completed', 49000, 'Cash'),
(32, 5, 'Completed', 1000, 'Cash');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payableinvoices`
--
ALTER TABLE `payableinvoices`
  ADD PRIMARY KEY (`PayableInvoiceID`),
  ADD KEY `BudgetID` (`AllocationID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `paymentschedules`
--
ALTER TABLE `paymentschedules`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `PayableInvoiceID` (`PayableInvoiceID`);

--
-- Indexes for table `vendorpayments`
--
ALTER TABLE `vendorpayments`
  ADD PRIMARY KEY (`PayablePaymentID`),
  ADD KEY `PayableInvoiceID` (`PayableInvoiceID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payableinvoices`
--
ALTER TABLE `payableinvoices`
  MODIFY `PayableInvoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paymentschedules`
--
ALTER TABLE `paymentschedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `vendorpayments`
--
ALTER TABLE `vendorpayments`
  MODIFY `PayablePaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
