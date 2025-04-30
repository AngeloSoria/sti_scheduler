-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 06:11 PM
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
-- Database: `scheduling_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `curriculums`
--

CREATE TABLE `curriculums` (
  `CurriculumID` int(11) NOT NULL,
  `SubjectName` varchar(100) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  `CreditUnit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'BSIT'),
(2, 'BSCS');

-- --------------------------------------------------------

--
-- Table structure for table `facultymembers`
--

CREATE TABLE `facultymembers` (
  `FacultyID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facultymembers`
--

INSERT INTO `facultymembers` (`FacultyID`, `DepartmentID`, `ProgramID`, `UserID`) VALUES
(1, 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `preferredsubjects`
--

CREATE TABLE `preferredsubjects` (
  `PreferredSubjectID` int(11) NOT NULL,
  `FacultyID` int(11) NOT NULL,
  `CurriculumID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `ProgramID` int(11) NOT NULL,
  `ProgramName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`ProgramID`, `ProgramName`) VALUES
(1, 'BSIT'),
(2, 'BSCS');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` int(11) NOT NULL,
  `RoomName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `ScheduleID` int(11) NOT NULL,
  `CurriculumID` int(11) NOT NULL,
  `FacultyID` int(11) NOT NULL,
  `Day` varchar(50) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `RoomID` int(11) NOT NULL,
  `SectionName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `SectionID` int(11) NOT NULL,
  `SectionName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `Role` enum('admin','faculty') NOT NULL DEFAULT 'faculty',
  `ProfilePic` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `FirstName`, `MiddleName`, `LastName`, `Role`, `ProfilePic`) VALUES
(1, 'admin', '$2y$10$glthBtq87Oe1QHJHvmiFz.S3cyEUsB2L7bLTSxuiquN0ShAFlh0yi', 'admin1', '', 'admin', 'admin', NULL),
(2, 'faculty1', '$2y$10$zTm2pJyNThVtexnp7QqGye7qE1fvzezVMzg0d4k0orCEpla6oD4/G', 'fac', '', 'ulty', 'faculty', NULL),
(3, 'faculty2', '$2y$10$jjwhsMbVGOtVfVFhETEhz.dVDK4riXXXlNsf/w.NwRH5YU6cQ0Zvq', 'fac', '', 'ulty2', 'faculty', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD PRIMARY KEY (`CurriculumID`),
  ADD KEY `ProgramID` (`ProgramID`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `facultymembers`
--
ALTER TABLE `facultymembers`
  ADD PRIMARY KEY (`FacultyID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `ProgramID` (`ProgramID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  ADD PRIMARY KEY (`PreferredSubjectID`),
  ADD KEY `FacultyID` (`FacultyID`),
  ADD KEY `CurriculumID` (`CurriculumID`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`ProgramID`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`RoomID`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `CurriculumID` (`CurriculumID`),
  ADD KEY `FacultyID` (`FacultyID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`SectionID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `curriculums`
--
ALTER TABLE `curriculums`
  MODIFY `CurriculumID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `facultymembers`
--
ALTER TABLE `facultymembers`
  MODIFY `FacultyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  MODIFY `PreferredSubjectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `ProgramID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `SectionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD CONSTRAINT `curriculums_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`);

--
-- Constraints for table `facultymembers`
--
ALTER TABLE `facultymembers`
  ADD CONSTRAINT `facultymembers_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `departments` (`DepartmentID`),
  ADD CONSTRAINT `facultymembers_ibfk_2` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`),
  ADD CONSTRAINT `facultymembers_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  ADD CONSTRAINT `preferredsubjects_ibfk_1` FOREIGN KEY (`FacultyID`) REFERENCES `facultymembers` (`FacultyID`),
  ADD CONSTRAINT `preferredsubjects_ibfk_2` FOREIGN KEY (`CurriculumID`) REFERENCES `curriculums` (`CurriculumID`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`CurriculumID`) REFERENCES `curriculums` (`CurriculumID`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`FacultyID`) REFERENCES `facultymembers` (`FacultyID`),
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
