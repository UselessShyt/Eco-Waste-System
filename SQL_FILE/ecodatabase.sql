-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2024 at 05:59 AM
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
-- Database: `ecodatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `community`
--

CREATE TABLE `community` (
  `Com_Id` int(11) NOT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `Address` varchar(200) DEFAULT NULL,
  `Admin_Id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE `issue` (
  `issue_Id` int(11) NOT NULL,
  `issue_type` varchar(200) NOT NULL,
  `Description` varchar(200) DEFAULT NULL,
  `issue_Date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `Notice_Id` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Message` varchar(200) DEFAULT NULL,
  `notice_date` date NOT NULL,
  `com_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `Sch_Id` int(11) NOT NULL,
  `sch-date` date NOT NULL,
  `sch-time` time NOT NULL,
  `waste_type` varchar(50) NOT NULL,
  `Com_Id` int(11) NOT NULL,
  `sch-quantity` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `role` varchar(10) NOT NULL,
  `com_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_schedule`
--

CREATE TABLE `users_schedule` (
  `User_Id` int(11) NOT NULL,
  `Sch_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waste`
--

CREATE TABLE `waste` (
  `waste_type` varchar(50) NOT NULL,
  `waste_handling` varchar(100) NOT NULL,
  `Com_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community`
--
ALTER TABLE `community`
  ADD PRIMARY KEY (`Com_Id`),
  ADD KEY `com_fk` (`Admin_Id`);

--
-- Indexes for table `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`issue_Id`),
  ADD KEY `issue_fk` (`user_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`Notice_Id`),
  ADD KEY `notice_fk` (`com_Id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`Sch_Id`),
  ADD KEY `Sch_fk_waste` (`waste_type`),
  ADD KEY `Sch_fk_com` (`Com_Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD KEY `user_fk` (`com_id`);

--
-- Indexes for table `users_schedule`
--
ALTER TABLE `users_schedule`
  ADD PRIMARY KEY (`User_Id`,`Sch_Id`),
  ADD KEY `US_FK_Sch` (`Sch_Id`);

--
-- Indexes for table `waste`
--
ALTER TABLE `waste`
  ADD PRIMARY KEY (`waste_type`),
  ADD KEY `waste_fk` (`Com_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community`
--
ALTER TABLE `community`
  MODIFY `Com_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue`
--
ALTER TABLE `issue`
  MODIFY `issue_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `Notice_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `Sch_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community`
--
ALTER TABLE `community`
  ADD CONSTRAINT `com_fk` FOREIGN KEY (`Admin_Id`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notice_fk` FOREIGN KEY (`com_Id`) REFERENCES `community` (`Com_Id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `Sch_fk_com` FOREIGN KEY (`Com_Id`) REFERENCES `community` (`Com_Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Sch_fk_waste` FOREIGN KEY (`waste_type`) REFERENCES `waste` (`waste_type`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `user_fk` FOREIGN KEY (`com_id`) REFERENCES `community` (`Com_Id`) ON DELETE CASCADE;

--
-- Constraints for table `users_schedule`
--
ALTER TABLE `users_schedule`
  ADD CONSTRAINT `US_FK_Sch` FOREIGN KEY (`Sch_Id`) REFERENCES `schedule` (`Sch_Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `US_FK_Users` FOREIGN KEY (`User_Id`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `waste`
--
ALTER TABLE `waste`
  ADD CONSTRAINT `waste_fk` FOREIGN KEY (`Com_Id`) REFERENCES `community` (`Com_Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
