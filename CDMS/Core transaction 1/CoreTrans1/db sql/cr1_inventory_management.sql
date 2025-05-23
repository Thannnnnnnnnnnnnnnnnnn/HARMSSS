-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 19, 2025 at 08:20 PM
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
-- Database: `cr1_inventory_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `InventoryID` int(11) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `StockLevel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`InventoryID`, `ItemName`, `StockLevel`) VALUES
(11, 'Buns', 500),
(12, 'Tamotoes', 100),
(13, 'Coke ', 1100);

-- --------------------------------------------------------

--
-- Table structure for table `reorder_levels`
--

CREATE TABLE `reorder_levels` (
  `ReorderLevelID` int(11) NOT NULL,
  `InventoryID` int(11) NOT NULL,
  `ReorderLevel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reorder_levels`
--

INSERT INTO `reorder_levels` (`ReorderLevelID`, `InventoryID`, `ReorderLevel`) VALUES
(18, 11, 100),
(19, 11, 200),
(20, 11, 200),
(21, 12, 100),
(22, 13, 400),
(23, 13, 400);

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `movementID` int(11) NOT NULL,
  `InventoryID` int(11) NOT NULL,
  `MovementType` enum('IN','OUT') NOT NULL,
  `Quantity` int(11) NOT NULL,
  `MovementDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`movementID`, `InventoryID`, `MovementType`, `Quantity`, `MovementDate`) VALUES
(7, 11, 'IN', 500, '2025-05-19 01:56:08'),
(8, 12, 'IN', 100, '2025-05-19 02:10:18'),
(9, 13, 'IN', 1000, '2025-05-19 02:10:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`InventoryID`);

--
-- Indexes for table `reorder_levels`
--
ALTER TABLE `reorder_levels`
  ADD PRIMARY KEY (`ReorderLevelID`),
  ADD KEY `InventoryID` (`InventoryID`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`movementID`),
  ADD KEY `InventoryID` (`InventoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reorder_levels`
--
ALTER TABLE `reorder_levels`
  MODIFY `ReorderLevelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `movementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reorder_levels`
--
ALTER TABLE `reorder_levels`
  ADD CONSTRAINT `reorder_levels_ibfk_1` FOREIGN KEY (`InventoryID`) REFERENCES `inventory` (`InventoryID`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`InventoryID`) REFERENCES `inventory` (`InventoryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
