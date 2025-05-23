-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 16, 2025 at 02:38 PM
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
-- Database: `cr3_re`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `GuestID` int(11) NOT NULL,
  `rating` varchar(25) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`FeedbackID`, `GuestID`, `rating`, `comment`, `feedback_date`) VALUES
(59, 1, '5', 'good hotel with pool is so fun', '2025-05-15 06:37:43');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `GuestID` int(11) NOT NULL,
  `guest_name` varchar(25) NOT NULL,
  `check_in` timestamp(6) NULL DEFAULT NULL,
  `check_out` timestamp(6) NULL DEFAULT NULL,
  `phone` varchar(25) NOT NULL,
  `address` varchar(150) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(25) NOT NULL,
  `gender` varchar(25) NOT NULL,
  `nationality` varchar(25) NOT NULL,
  `reservation` varchar(25) NOT NULL,
  `status` varchar(25) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`GuestID`, `guest_name`, `check_in`, `check_out`, `phone`, `address`, `date_of_birth`, `email`, `gender`, `nationality`, `reservation`, `status`, `user_id`) VALUES
(1, 'Jericho Maghilom', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '09202515164', 'qwe', '2002-12-08', 'jerichomaghilom08@gmail.c', 'Male', 'Filipino', '', '', 0),
(23, 'Jericho Maghilom99', '0000-00-00 00:00:00.000000', '2025-05-13 12:02:51.000000', '09202515164', '63 Sout Maya Drive Philam Homes', '2025-04-26', 'jerichomaghilom08@gmail.c', 'male', 'male', '2081', NULL, 0),
(25, 'John Doe', '2025-05-13 12:08:10.000000', NULL, '09171234567', '123 Sample St, QC', '1990-05-10', 'john@example.com', 'Male', 'Filipino', 'Online', 'Checked-in', 0),
(26, 'Maria Santos', '2025-05-13 12:08:10.000000', NULL, '09181234567', '456 Sample Ave, Manila', '1985-08-22', 'maria@example.com', 'Female', 'Filipino', 'Walk-in', 'Checked-in', 0),
(27, 'Pedro Cruz', '2025-05-13 12:08:10.000000', NULL, '09191234567', '789 Sample Rd, Makati', '1992-01-30', 'pedro@example.com', 'Male', 'Filipino', 'Phone', 'Checked-in', 0),
(28, 'Anna Reyes', '2025-05-13 12:08:10.000000', NULL, '09201234567', '321 Sample Blvd, Pasig', '1994-03-14', 'anna@example.com', 'Female', 'Filipino', 'Online', 'Checked-in', 0),
(29, 'Carlos Dela Cruz', '2025-05-13 12:08:10.000000', NULL, '09211234567', '654 Sample Dr, Taguig', '1988-12-12', 'carlos@example.com', 'Male', 'Filipino', 'Walk-in', 'Checked-in', 0),
(30, 'Ella Fernando', '2025-05-10 02:00:00.000000', '2025-05-12 04:00:00.000000', '09221234567', '987 Sample Ln, Cebu City', '1993-07-07', 'ella@example.com', 'Female', 'Filipino', 'Online', 'Checked-out', 0),
(31, 'Miguel Lopez', '2025-05-08 07:30:00.000000', '2025-05-09 02:00:00.000000', '09231234567', '852 Sample St, Davao', '1987-09-17', 'miguel@example.com', 'Male', 'Filipino', 'Phone', 'Checked-out', 0),
(32, 'Lea Dizon', '2025-04-30 05:00:00.000000', '2025-05-01 03:00:00.000000', '09241234567', '741 Sample Ave, Baguio', '1995-11-05', 'lea@example.com', 'Female', 'Filipino', 'Walk-in', 'Checked-out', 0),
(33, 'Nico Ramos', '2025-05-01 06:45:00.000000', '2025-05-03 01:00:00.000000', '09251234567', '963 Sample St, Batangas', '1990-06-06', 'nico@example.com', 'Male', 'Filipino', 'Online', 'Checked-out', 0),
(34, 'Bianca Torres', '2025-05-04 02:00:00.000000', '2025-05-06 02:30:00.000000', '09261234567', '159 Sample Cir, Iloilo', '1991-02-28', 'bianca@example.com', 'Female', 'Filipino', 'Phone', 'Checked-out', 0),
(39, 'guest', NULL, NULL, '0955164546', '63 Sout Maya Drive Philam Homes', '2025-05-16', 'guest@gmail.com', 'Male', 'filipino', '', NULL, 72);

-- --------------------------------------------------------

--
-- Table structure for table `interactions`
--

CREATE TABLE `interactions` (
  `InteractionID` int(11) NOT NULL,
  `GuestID` int(11) DEFAULT NULL,
  `interaction_type` varchar(25) DEFAULT NULL,
  `interaction_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `interaction_status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interactions`
--

INSERT INTO `interactions` (`InteractionID`, `GuestID`, `interaction_type`, `interaction_date`, `description`, `interaction_status`) VALUES
(22, 1, 'Service Request', '2025-05-14', '123123', 'completed'),
(23, 1, 'Inquiry', '2025-05-14', '123123', 'completed'),
(24, 1, 'Inquiry', '2025-05-14', '123123', 'cancelled'),
(25, 1, 'Complaint', '2025-05-15', 'helo helo', 'escalated'),
(29, 72, 'Service Request', '2025-05-16', '123', 'Pending'),
(30, 39, 'Service Request', '2025-05-16', '123', 'Pending'),
(31, 39, 'Service Request', '2025-05-16', 'pahingi towel', 'Pending'),
(32, 39, 'Inquiry', '2025-05-16', 'balbalbalbdlblabld', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `username`, `password`, `fname`, `email`, `role`, `created_at`) VALUES
(10, 'admin@gmail.com', '$2y$10$OmG5W5ZErLBmGh1eN./nt.1Cb3oocDn5u3BP9CfNrTBZcWm8NQEIC', NULL, NULL, 'admin', '2025-05-16 09:53:46'),
(70, 'staff@gmail.com', 'staff', 'echo', 'staff@gmail.com', 'staff', '2025-05-16 10:11:59'),
(72, 'guest@gmal.com', '$2y$10$AbuyyFt5IJBnolU/isbqGuzbB9Z5mD7Gjo2TkVcpx74E5UZTs73bq', NULL, NULL, 'guest', '2025-05-16 10:18:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `GuestsID` (`GuestID`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`GuestID`);

--
-- Indexes for table `interactions`
--
ALTER TABLE `interactions`
  ADD PRIMARY KEY (`InteractionID`),
  ADD KEY `GuestID` (`GuestID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `GuestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `interactions`
--
ALTER TABLE `interactions`
  MODIFY `InteractionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
