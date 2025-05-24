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
-- Database: `fin_collection`
--

-- --------------------------------------------------------

--
-- Table structure for table `acct_receivable`
--

CREATE TABLE `acct_receivable` (
  `ReceivableID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `Status` enum('Downpayment','FullyPaid','Reservation','Settled') NOT NULL,
  `IsViewed` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acct_receivable`
--

INSERT INTO `acct_receivable` (`ReceivableID`, `InvoiceID`, `Status`, `IsViewed`) VALUES
(105, 116, 'Settled', 1),
(106, 117, 'Downpayment', 1);

-- --------------------------------------------------------

--
-- Table structure for table `collection_payments`
--

CREATE TABLE `collection_payments` (
  `PaymentID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `AmountPay` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collection_payments`
--

INSERT INTO `collection_payments` (`PaymentID`, `InvoiceID`, `TotalAmount`, `AmountPay`) VALUES
(111, 116, 5000.00, 5000.00),
(112, 117, 1300.00, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `InvoiceID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `GuestName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`InvoiceID`, `AccountID`, `GuestName`) VALUES
(116, 0, 'ROY'),
(117, 0, 'MAMARK');

-- --------------------------------------------------------

--
-- Table structure for table `paymentmethods`
--

CREATE TABLE `paymentmethods` (
  `MethodID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `PaymentType` enum('Credit/Debit','Cash') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymentmethods`
--

INSERT INTO `paymentmethods` (`MethodID`, `InvoiceID`, `PaymentType`) VALUES
(106, 116, 'Credit/Debit'),
(107, 117, 'Credit/Debit');

-- --------------------------------------------------------

--
-- Table structure for table `receivableschedule`
--

CREATE TABLE `receivableschedule` (
  `ScheduleID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `StartDate` datetime NOT NULL,
  `EndDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receivableschedule`
--

INSERT INTO `receivableschedule` (`ScheduleID`, `InvoiceID`, `StartDate`, `EndDate`) VALUES
(109, 116, '2025-04-16 11:12:00', '2025-04-16 11:12:00'),
(110, 117, '2025-04-16 11:34:00', '2025-04-18 11:34:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acct_receivable`
--
ALTER TABLE `acct_receivable`
  ADD PRIMARY KEY (`ReceivableID`),
  ADD KEY `acct_receivable_ibfk_1` (`InvoiceID`);

--
-- Indexes for table `collection_payments`
--
ALTER TABLE `collection_payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `collection_payments_ibfk_1` (`InvoiceID`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`InvoiceID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `paymentmethods`
--
ALTER TABLE `paymentmethods`
  ADD PRIMARY KEY (`MethodID`),
  ADD KEY `invoiceID` (`InvoiceID`),
  ADD KEY `InvoiceID_2` (`InvoiceID`);

--
-- Indexes for table `receivableschedule`
--
ALTER TABLE `receivableschedule`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acct_receivable`
--
ALTER TABLE `acct_receivable`
  MODIFY `ReceivableID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `collection_payments`
--
ALTER TABLE `collection_payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `InvoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `paymentmethods`
--
ALTER TABLE `paymentmethods`
  MODIFY `MethodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `receivableschedule`
--
ALTER TABLE `receivableschedule`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acct_receivable`
--
ALTER TABLE `acct_receivable`
  ADD CONSTRAINT `acct_receivable_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoices` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `collection_payments`
--
ALTER TABLE `collection_payments`
  ADD CONSTRAINT `collection_payments_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoices` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
