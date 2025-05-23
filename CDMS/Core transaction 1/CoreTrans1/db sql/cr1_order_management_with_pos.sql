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
-- Database: `cr1_order_management_with_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemId` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `MenuItemID` int(11) NOT NULL,
  `OrderType` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `MenuName` varchar(255) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `SubTotal` decimal(10,2) NOT NULL,
  `OrderDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemId`, `OrderID`, `MenuItemID`, `OrderType`, `Location`, `MenuName`, `Quantity`, `Price`, `SubTotal`, `OrderDate`) VALUES
(181, 181, 3, 'Dine-in', 'Table Table #14', 'Lemonade', 1, 80.00, 80.00, '2025-05-19'),
(182, 181, 5, 'Dine-in', 'Table Table #14', 'Hotdog', 1, 60.00, 60.00, '2025-05-19'),
(183, 181, 10, 'Dine-in', 'Table Table #14', 'Bebe nga', 10, 10.00, 100.00, '2025-05-19'),
(184, 181, 8, 'Dine-in', 'Table Table #14', 'Coke', 1, 20.00, 20.00, '2025-05-19'),
(185, 182, 2, 'Room Service', 'Room Room #13', 'Margarita Pizza', 2, 200.50, 401.00, '2025-05-19'),
(186, 182, 3, 'Room Service', 'Room Room #13', 'Lemonade', 2, 80.00, 160.00, '2025-05-19'),
(187, 182, 1, 'Room Service', 'Room Room #13', 'Grilled Chicken Sandwich', 2, 470.00, 940.00, '2025-05-19'),
(188, 182, 5, 'Room Service', 'Room Room #13', 'Hotdog', 1, 60.00, 60.00, '2025-05-19'),
(189, 182, 4, 'Room Service', 'Room Room #13', 'Espresso', 1, 39.28, 39.28, '2025-05-19'),
(190, 182, 6, 'Room Service', 'Room Room #13', 'FriedChicken with Rice', 1, 60.99, 60.99, '2025-05-19'),
(191, 182, 10, 'Room Service', 'Room Room #13', 'Bebe nga', 1, 10.00, 10.00, '2025-05-19'),
(192, 182, 8, 'Room Service', 'Room Room #13', 'Coke', 1, 20.00, 20.00, '2025-05-19'),
(193, 182, 7, 'Room Service', 'Room Room #13', 'Rice', 1, 15.00, 15.00, '2025-05-19'),
(194, 183, 3, 'Room Service', 'Room Room #13', 'Lemonade', 1, 80.00, 80.00, '2025-05-19'),
(195, 184, 2, 'Dine-in', 'Table Table #14', 'Margarita Pizza', 1, 200.50, 200.50, '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL,
  `posid` int(11) DEFAULT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `OrderDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`Order_ID`, `posid`, `CustomerName`, `TotalAmount`, `OrderDate`) VALUES
(181, 1, 'Robert', 260.00, '2025-05-19'),
(182, 1, 'May', 1706.27, '2025-05-19'),
(183, 1, 'Monkey', 80.00, '2025-05-19'),
(184, 1, 'Ashley', 200.50, '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `TransactionID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `TransactionDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`TransactionID`, `OrderID`, `Location`, `Amount`, `TransactionDate`) VALUES
(97, 181, 'Table Table #14', 260.00, '2025-05-19'),
(98, 182, 'Room Room #13', 1706.27, '2025-05-19'),
(99, 183, 'Room Room #13', 80.00, '2025-05-19'),
(100, 184, 'Table Table #14', 200.50, '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `pos`
--

CREATE TABLE `pos` (
  `posid` int(11) NOT NULL,
  `terminal_name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `assigned_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pos`
--

INSERT INTO `pos` (`posid`, `terminal_name`, `location`, `assigned_user_id`) VALUES
(1, 'Waiter Tablet', 'Dining Area', NULL),
(2, 'Front Desk', 'Reception', NULL),
(3, 'Bar', 'Lounge', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`OrderItemId`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `MenuItemID` (`MenuItemID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `orders_ibfk_pos` (`posid`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `pos`
--
ALTER TABLE `pos`
  ADD PRIMARY KEY (`posid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `OrderItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `pos`
--
ALTER TABLE `pos`
  MODIFY `posid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`Order_ID`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`MenuItemID`) REFERENCES `cr1_kitchen_bar_module`.`menuitems` (`MenuItemID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_pos` FOREIGN KEY (`posid`) REFERENCES `pos` (`posid`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`Order_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
