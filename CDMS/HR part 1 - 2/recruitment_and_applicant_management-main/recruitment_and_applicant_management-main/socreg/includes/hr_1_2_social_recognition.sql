-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Apr 09, 2025 at 07:43 PM
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
-- Database: `hr_1&2_social_recognition2`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `AwardID` int(11) NOT NULL,
  `AwardName` varchar(255) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`AwardID`, `AwardName`, `CategoryID`, `Description`, `CreatedAt`) VALUES
(1, 'Team Player of the Month', 1, 'Awarded to employees who demonstrate exceptional teamwork', '2025-04-03 15:46:39'),
(2, 'Innovation Champion', 2, 'Recognizing employees who bring innovative ideas to life', '2025-04-03 15:46:39'),
(3, 'Customer Service Star', 3, 'Awarded for outstanding customer service and support', '2025-04-03 15:46:39'),
(4, 'Leadership Excellence', 4, 'Recognizing exceptional leadership and management skills', '2025-04-03 15:46:39'),
(5, 'Employee of the Month', 5, 'Overall excellence in performance and contribution', '2025-04-03 15:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `employeerecognition`
--

CREATE TABLE `employeerecognition` (
  `RecognitionID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `Employee name` varchar(255) NOT NULL,
  `AwardID` int(11) NOT NULL,
  `Awards name` varchar(255) NOT NULL,
  `RecognitionDate` datetime DEFAULT current_timestamp(),
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeerecognition`
--

INSERT INTO `employeerecognition` (`RecognitionID`, `EmployeeID`, `Employee name`, `AwardID`, `Awards name`, `RecognitionDate`, `Description`) VALUES
(1, 1, 'John Smith', 1, 'Team Player of the Month', '2025-04-09 00:00:00', 'Exceptional teamwork in the recent project'),
(2, 2, 'Sarah Johnson', 4, 'Leadership Excellence', '2024-03-05 14:30:00', 'Outstanding leadership in HR initiatives');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `EmployeeID` int(11) NOT NULL,
  `Employee name` varchar(255) NOT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `HireDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`EmployeeID`, `Employee name`, `Department`, `Position`, `Email`, `Phone`, `HireDate`) VALUES
(1, 'John Doe', 'IT', 'Software Engineer', 'john.doe@example.com', '1234567890', '2023-01-15'),
(2, 'Jane Smith', 'HR', 'HR Manager', 'jane.smith@example.com', '0987654321', '2023-02-20'),
(3, 'Mike Johnson', 'Sales', 'Sales Representative', 'mike.johnson@example.com', '1122334455', '2023-03-10'),
(4, 'Sarah Williams', 'Marketing', 'Marketing Specialist', 'sarah.williams@example.com', '5566778899', '2023-04-05'),
(5, 'David Brown', 'Finance', 'Financial Analyst', 'david.brown@example.com', '9988776655', '2023-05-12'),
(6, 'John Smith', 'IT', 'Senior Developer', 'john.smith@example.com', '1234567890', '2023-01-15'),
(7, 'Sarah Johnson', 'HR', 'HR Manager', 'sarah.johnson@example.com', '0987654321', '2023-02-20'),
(8, 'Michael Brown', 'Sales', 'Sales Manager', 'michael.brown@example.com', '1122334455', '2023-03-10'),
(9, 'Emily Davis', 'Marketing', 'Marketing Specialist', 'emily.davis@example.com', '5566778899', '2023-04-05'),
(10, 'David Wilson', 'Finance', 'Financial Analyst', 'david.wilson@example.com', '9988776655', '2023-05-12');

-- --------------------------------------------------------

--
-- Table structure for table `recognitioncategories`
--

CREATE TABLE `recognitioncategories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recognitioncategories`
--

INSERT INTO `recognitioncategories` (`CategoryID`, `CategoryName`, `Description`, `CreatedAt`) VALUES
(1, 'Teamwork', 'Recognizing employees who excel in collaboration and team spirit', '2025-04-03 15:46:39'),
(2, 'Innovation', 'Awarding creative thinking and innovative solutions', '2025-04-03 15:46:39'),
(3, 'Customer Service', 'Recognizing exceptional customer service and support', '2025-04-03 15:46:39'),
(4, 'Leadership', 'Acknowledging outstanding leadership qualities', '2025-04-03 15:46:39'),
(5, 'Excellence', 'Celebrating overall excellence in performance', '2025-04-03 15:46:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`AwardID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  ADD PRIMARY KEY (`RecognitionID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `AwardID` (`AwardID`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `recognitioncategories`
--
ALTER TABLE `recognitioncategories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `AwardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  MODIFY `RecognitionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recognitioncategories`
--
ALTER TABLE `recognitioncategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `awards`
--
ALTER TABLE `awards`
  ADD CONSTRAINT `awards_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `recognitioncategories` (`CategoryID`);

--
-- Constraints for table `employeerecognition`
--
ALTER TABLE `employeerecognition`
  ADD CONSTRAINT `employeerecognition_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employees` (`EmployeeID`),
  ADD CONSTRAINT `employeerecognition_ibfk_2` FOREIGN KEY (`AwardID`) REFERENCES `awards` (`AwardID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
