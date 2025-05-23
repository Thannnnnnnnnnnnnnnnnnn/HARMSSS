-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 04:39 PM
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
-- Database: `hr_1&2_performance_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appraisals`
--

CREATE TABLE `appraisals` (
  `AppraisalID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `ActionDescription` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `Feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`FeedbackID`, `EmployeeID`, `Feedback`) VALUES
(8, 12226, 'this person is normal'),
(56, 13333, '76567567');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `GoalID` int(11) NOT NULL,
  `Goal` varchar(255) NOT NULL,
  `Deadline` date DEFAULT NULL,
  `KPI_Connection` varchar(255) DEFAULT NULL,
  `Formula` text DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Not Started',
  `Department` varchar(100) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Target_Value` varchar(100) DEFAULT NULL,
  `RequiredRating` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`GoalID`, `Goal`, `Deadline`, `KPI_Connection`, `Formula`, `Status`, `Department`, `EmployeeID`, `Target_Value`, `RequiredRating`) VALUES
(7, 'Increase Average Employee Rating to 8', '2025-05-06', NULL, NULL, 'Not Started', 'HR', 12223, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `kpis`
--

CREATE TABLE `kpis` (
  `KPIID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `AvgRating` decimal(3,2) NOT NULL,
  `PerformanceCategory` varchar(255) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpis`
--

INSERT INTO `kpis` (`KPIID`, `EmployeeID`, `AvgRating`, `PerformanceCategory`, `DateCreated`) VALUES
(0, 12223, 9.99, 'Above Expectations', '2025-04-15 04:06:41');

-- --------------------------------------------------------

--
-- Table structure for table `performancereviews`
--

CREATE TABLE `performancereviews` (
  `ReviewID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `ReviewDate` date NOT NULL,
  `Rating` varchar(50) NOT NULL,
  `Comments` text DEFAULT NULL,
  `Reviewer` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performancereviews`
--

INSERT INTO `performancereviews` (`ReviewID`, `EmployeeID`, `ReviewDate`, `Rating`, `Comments`, `Reviewer`) VALUES
(88, 13333, '2025-05-16', '10', 'Exceeds Expectations', 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `User_ID` int(11) NOT NULL,
  `Department_ID` int(11) DEFAULT NULL,
  `Role` enum('super admin','admin','manager','staff') DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Status` enum('Active','Inactive') DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`User_ID`, `Department_ID`, `Role`, `Password`, `Name`, `Department`, `Status`, `Email`) VALUES
(0, NULL, 'super admin', '$2y$10$fUGVdwDqrgq61c9lUb0mbeeyjDx9HxQwNTuEuwv9Kv01HdEgAX6dq', 'Chrovic', NULL, 'Active', 'rovic.castrodes@gmail.com'),
(1, NULL, 'staff', '$2y$10$aenMaA8/V8ZmzL6YQ45UteW1ADVjY3JNICSMWW0ciXbm9dnYJyPBe', 'Chrovic1212', NULL, 'Active', 'roviccastrodes@yahoo.com'),
(2, NULL, 'staff', '$2y$10$CGSjatbCrlkpICps8GivZOU/m64SjfqWlLXWuRdn/FPiLEtQZjMzK', 'asdasd', NULL, 'Active', 'minechroc@gmail.com'),
(3, NULL, 'manager', '$2y$10$jM0SHzN/fM9a.JxW9rRCo.VABi06egOHV2NLuPK3H27Cd2tzoPkWe', 'heelko', NULL, 'Active', 'jazznelle002@yahoo.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_audit_trail`
--

CREATE TABLE `user_audit_trail` (
  `User_Audit_Trail_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Department_Affected` enum('HR part 1 - 2','HR part 3 - 4','Log1','Log2','Core 1','Core 2','Core 3','Financials') DEFAULT NULL,
  `Module_Affected` varchar(255) DEFAULT NULL,
  `Role` enum('Super admin','admin','manager','staff','guest') DEFAULT NULL,
  `User_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_audit_trail`
--

INSERT INTO `user_audit_trail` (`User_Audit_Trail_ID`, `User_ID`, `Action`, `Department_Affected`, `Module_Affected`, `Role`, `User_name`) VALUES
(51, 0, 'Submitted a performance review for EmployeeID 012223', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(52, 0, 'Deleted a performance review for EmployeeID 12223', '', '', '', ''),
(53, 0, 'Submitted a performance review for EmployeeID 013333', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(54, 0, 'Deleted a performance review for EmployeeID ', '', '', '', ''),
(55, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', '', 'Chrovic'),
(56, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(57, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(58, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(59, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(60, 0, 'Deleted a performance review for EmployeeID 80', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(61, 0, 'Submitted a performance review for EmployeeID 013333', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(62, 0, 'Submitted a performance review for EmployeeID 013333', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(63, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(64, 0, 'Deleted a performance review for EmployeeID ', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(65, 0, 'Deleted a performance review for EmployeeID 13333', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(66, 0, 'Submitted a performance review for EmployeeID 11123', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(67, 0, 'Deleted a performance review for EmployeeID 11123', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(68, 0, 'Submitted a performance review for EmployeeID asdas1233', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(69, 0, 'Deleted a performance review for EmployeeID 0', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(70, 0, 'Submitted a performance review for EmployeeID 012223', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(71, 0, 'Deleted a performance review for EmployeeID 12223', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(72, 0, 'Submitted a performance review for EmployeeID 14444', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(73, 0, 'Deleted a performance review for EmployeeID 14444', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(74, NULL, 'Deleted goal: \'Maintain above-target performance and enhance leadership skills\'', '', 'Goals', NULL, NULL),
(75, NULL, 'Deleted goal: \'Maintain above-target performance and enhance leadership skills\'', '', 'Goals', NULL, NULL),
(76, NULL, 'Added a new goal: \'Maintain above-target performance and enhance leadership skills\'', '', 'Goals', '', 'Unknown'),
(77, NULL, 'Added a new goal: \'jasdjhabsndjhbsakd\'', '', 'Goals', '', 'Unknown'),
(78, 0, 'Deleted goal: \'Maintain above-target performance and enhance leadership skills\'', '', 'Goals', 'Super admin', 'Chrovic'),
(79, NULL, 'Added a new goal: \'uuuuuu\'', '', 'Goals', '', 'Unknown'),
(80, 0, 'Deleted goal: \'uuuuuu\'', '', 'Goals', 'Super admin', 'Chrovic'),
(81, NULL, 'Added a new goal: \'jasdjhabsndjhbsakd\'', '', 'Goals', '', 'Unknown'),
(82, 0, 'Deleted goal: \'jasdjhabsndjhbsakd\'', '', 'Goals', 'Super admin', 'Chrovic'),
(83, NULL, 'Added a new goal: \'jasdjhabsndjhbsakd\'', '', 'Goals', '', 'Unknown'),
(84, 0, 'Deleted goal: \'jasdjhabsndjhbsakd\'', 'HR part 1 - 2', 'Goals', 'Super admin', 'Chrovic'),
(85, 0, 'Added a new goal: \'jhasvduhabsdh\'', 'HR part 1 - 2', 'Goals', 'Super admin', 'Chrovic'),
(86, 0, 'Deleted goal: \'jhasvduhabsdh\'', 'HR part 1 - 2', 'Goals', 'Super admin', 'Chrovic'),
(87, 0, 'Submitted a performance review for EmployeeID 14444', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(88, 0, 'Deleted a performance review for EmployeeID 14444', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(89, 0, 'Submitted a performance review for EmployeeID 013333', 'HR part 1 - 2', 'Performance Module', 'Super admin', 'Chrovic'),
(90, NULL, 'Added appraisal action for EmployeeID: 13333 - Description: asdsadsad', '', 'Appraisals', '', 'Unknown'),
(91, NULL, 'Deleted appraisal action with AppraisalID: 8', '', 'Appraisals', '', 'Unknown'),
(92, 0, 'Added appraisal action for EmployeeID: 13333 - Description: dfasdasdsa', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(93, 0, 'Deleted appraisal action with AppraisalID: 9', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(94, 0, 'Added appraisal action for EmployeeID: 13333 - Description: ghjghjg4535', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(95, 0, 'Deleted appraisal action with AppraisalID: 10 EmployeeID: ', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(96, 0, 'Added appraisal action for EmployeeID: 13333 - Description: asdsadsa', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(97, 0, 'Deleted appraisal action with AppraisalID: 11 EmployeeID ', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(98, 0, 'Added appraisal action for EmployeeID: 13333 - Description: dsadsad', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(99, 0, 'Deleted appraisal action with AppraisalID: 12', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(100, 0, 'Added appraisal action for EmployeeID: 13333 - Description: asdadasd', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic'),
(101, 0, 'Deleted appraisal action with AppraisalID: 13', 'HR part 1 - 2', 'Appraisals', 'Super admin', 'Chrovic');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appraisals`
--
ALTER TABLE `appraisals`
  ADD PRIMARY KEY (`AppraisalID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`GoalID`);

--
-- Indexes for table `kpis`
--
ALTER TABLE `kpis`
  ADD PRIMARY KEY (`KPIID`);

--
-- Indexes for table `performancereviews`
--
ALTER TABLE `performancereviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`User_ID`);

--
-- Indexes for table `user_audit_trail`
--
ALTER TABLE `user_audit_trail`
  ADD PRIMARY KEY (`User_Audit_Trail_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appraisals`
--
ALTER TABLE `appraisals`
  MODIFY `AppraisalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `performancereviews`
--
ALTER TABLE `performancereviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `user_audit_trail`
--
ALTER TABLE `user_audit_trail`
  MODIFY `User_Audit_Trail_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_audit_trail`
--
ALTER TABLE `user_audit_trail`
  ADD CONSTRAINT `user_audit_trail_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user_account` (`User_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
