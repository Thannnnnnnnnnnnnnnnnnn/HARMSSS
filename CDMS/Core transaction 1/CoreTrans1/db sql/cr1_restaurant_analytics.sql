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
-- Database: `cr1_restaurant_analytics`
--

-- --------------------------------------------------------

--
-- Table structure for table `customerpreferences`
--

CREATE TABLE `customerpreferences` (
  `PreferenceID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `PreferenceDetails` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customerpreferences`
--

INSERT INTO `customerpreferences` (`PreferenceID`, `CustomerID`, `CustomerName`, `PreferenceDetails`) VALUES
(1, 1, 'Alice Johnson', 'Vegan, No spicy food'),
(2, 2, 'Bob Smith', 'Gluten-free, Low sugar'),
(3, 3, 'Charlie Lee', 'Prefers seafood, Extra spicy'),
(4, 4, 'Diana Cruz', 'No dairy, Likes grilled items'),
(5, 5, 'Ethan Kim', 'Vegetarian, Loves pasta');

-- --------------------------------------------------------

--
-- Table structure for table `salesreports`
--

CREATE TABLE `salesreports` (
  `ReportID` int(11) NOT NULL,
  `ReportDate` date NOT NULL,
  `TotalSales` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salesreports`
--

INSERT INTO `salesreports` (`ReportID`, `ReportDate`, `TotalSales`) VALUES
(1, '2025-02-01', 12500),
(2, '2025-02-02', 13900),
(3, '2025-02-03', 11200),
(4, '2025-02-04', 14800),
(5, '2025-02-05', 13250);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customerpreferences`
--
ALTER TABLE `customerpreferences`
  ADD PRIMARY KEY (`PreferenceID`);

--
-- Indexes for table `salesreports`
--
ALTER TABLE `salesreports`
  ADD PRIMARY KEY (`ReportID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customerpreferences`
--
ALTER TABLE `customerpreferences`
  MODIFY `PreferenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `salesreports`
--
ALTER TABLE `salesreports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
