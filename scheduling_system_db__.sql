-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 05:43 PM
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
  `CreditUnit` int(11) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `Year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculums`
--

INSERT INTO `curriculums` (`CurriculumID`, `SubjectName`, `CreditUnit`, `ProgramID`, `Year`) VALUES
(753, 'Math in Modern World', 99, 5, 1),
(755, 'NSTP 1', 3, 5, 1),
(756, 'PE 1', 3, 5, 1),
(757, 'Euthenics 1', 3, 5, 1),
(758, 'Risk Management as Applied to SSS', 3, 5, 1),
(759, 'Quality Serv Mngt in TH', 3, 5, 2),
(760, 'Art Appreciation', 3, 5, 2),
(761, 'Tour & Travel Management', 3, 5, 2),
(762, 'Sustainable Tourism', 3, 5, 2),
(763, 'Accommodation', 3, 5, 2),
(764, 'PE 3', 3, 5, 2),
(765, 'Foreign Language 1', 3, 5, 2),
(766, 'Entrepreneurial Mind', 3, 5, 3),
(767, 'Applied Business Tools', 3, 5, 3),
(768, 'Greatbooks', 3, 5, 3),
(769, 'TH marketing', 3, 5, 3),
(770, 'Multicultural Diversity', 3, 5, 3),
(771, 'Operations Management (TQM)', 3, 5, 3),
(772, 'Prof Dev and Applied Ethics', 3, 5, 3),
(773, 'Airline Operations 1', 3, 5, 4),
(774, 'Research in Tourism', 3, 5, 4),
(775, 'Euthenics 2', 3, 5, 4),
(776, 'Contemporary World', 3, 5, 4),
(777, 'Rizal\'s Life & Works', 3, 5, 4),
(778, 'Math in the Modern World', 3, 4, 1),
(779, 'Euthenics', 3, 4, 1),
(780, 'PE 1', 3, 4, 1),
(781, 'Understanding the Self', 3, 4, 1),
(782, 'NSTP 1', 3, 4, 1),
(783, 'Intro to Psychology', 3, 4, 1),
(784, 'Computer Productivity Tools', 3, 4, 1),
(785, 'Readings in Phil History', 3, 3, 1),
(786, 'The Contemporary World', 3, 3, 1),
(787, 'Understanding the Self', 3, 3, 1),
(788, 'NSTP 1', 3, 3, 1),
(789, 'Basic Microeconomics', 3, 3, 1),
(790, 'Euthenics', 3, 3, 1),
(791, 'PE 1', 3, 3, 1),
(792, 'Math in the Modern World', 3, 3, 2),
(793, 'Facilities Management', 3, 3, 2),
(794, 'The Entrepreneurial Mind', 3, 3, 2),
(795, 'Rizal\'s Life & Works', 3, 3, 2),
(796, 'PE 3', 3, 3, 2),
(797, 'Euthenics', 3, 3, 2),
(798, 'PE 1', 3, 3, 2),
(799, 'Costing and Pricing', 3, 3, 2),
(800, 'Business Research', 3, 3, 3),
(801, 'International Business and Trade', 3, 3, 3),
(802, 'Greatbooks', 3, 3, 3),
(803, 'Operations Management', 3, 3, 3),
(804, 'Good Governance', 3, 3, 3),
(805, 'Managerial Accounting', 3, 3, 3),
(806, 'Special Topics in Operations Management', 3, 3, 4),
(807, 'Marketing Management', 3, 3, 4),
(808, 'Euthenics 2', 3, 3, 4),
(809, 'Financial Management', 3, 3, 4),
(810, 'Feasibility Study', 3, 3, 4),
(811, 'Entrepreneurial Management', 3, 3, 4),
(812, 'Purposive Communication', 3, 2, 1),
(813, 'Understanding the Self', 3, 2, 1),
(814, 'Intro to Computing', 3, 2, 1),
(815, 'Computer Programming 1', 3, 2, 1),
(816, 'The Contemporary World', 3, 2, 1),
(817, 'NSTP 1', 3, 2, 1),
(818, 'Euthenics 1', 3, 2, 1),
(819, 'PE 1', 3, 2, 1),
(820, 'Entrepreneurial Mind', 3, 2, 2),
(821, 'Discrete Structures 2', 3, 2, 2),
(822, 'Readings in Phil History', 3, 2, 2),
(823, 'Rizal\'s Life and Works', 3, 2, 2),
(824, 'Principles of Communication', 3, 2, 2),
(825, 'Data Structures and Algorithm', 3, 2, 2),
(826, 'Computer Programming 3', 3, 2, 2),
(827, 'PE 3', 3, 2, 2),
(828, 'Quantitative Methods', 3, 2, 3),
(829, 'Artificial Intelligence', 3, 2, 3),
(830, 'CS ELE 2', 3, 2, 3),
(831, 'Theory of Computation', 3, 2, 3),
(832, 'App Dev', 3, 2, 3),
(833, 'Info Assurance and Sec Cybersecurity', 3, 2, 3),
(834, 'Methods of Research', 3, 2, 3),
(835, 'Software Engineering 1', 3, 2, 3),
(836, 'Technopreneurship', 3, 2, 4),
(837, 'Software Quality Assurance', 3, 2, 4),
(838, 'Network Technology 1', 3, 2, 4),
(839, 'Platform Technology', 3, 2, 4),
(840, 'Euthenics 2', 3, 2, 4),
(841, 'CS Thesis 2', 3, 2, 4),
(842, 'Info Assurance and Security (Data Privacy)', 3, 2, 4),
(843, 'Professional Issues in IS and Tech', 3, 2, 4),
(844, 'Understanding the Self', 3, 1, 1),
(845, 'Purposive Communication', 3, 1, 1),
(846, 'Phil Pop Culture', 3, 1, 1),
(847, 'Euthenics', 3, 1, 1),
(848, 'Euthenics 1', 3, 1, 1),
(849, 'Intro to Computing', 3, 1, 1),
(850, 'The Contemporary World', 3, 1, 1),
(851, 'NSTP 1', 3, 1, 1),
(852, 'Computer Programming 1', 3, 1, 1),
(853, 'PE 1', 3, 1, 1),
(854, 'Human Computer Interaction', 3, 1, 2),
(855, 'Readings in Phil History', 3, 1, 2),
(856, 'Platform Technology', 3, 1, 2),
(857, 'Principles of Communication', 3, 1, 2),
(858, 'Rizal\'s Life and Works', 3, 1, 2),
(859, 'PE 3', 3, 1, 2),
(860, 'Data Structures and algorithm', 3, 1, 2),
(861, 'IT ele 1 - OOP', 3, 1, 2),
(862, 'Advanced Database System', 3, 1, 3),
(863, 'Professional Issues in IS', 3, 1, 3),
(864, 'Event Driven Prog', 3, 1, 3),
(865, 'Data and Digital Comm', 3, 1, 3),
(866, 'Advanced System Integration and Architecture', 3, 1, 3),
(867, 'IT ele 2 - Enterprise Archi', 3, 1, 3),
(868, 'App Dev', 3, 1, 3),
(869, 'IT Service Management', 3, 1, 4),
(870, 'Euthenics 2', 3, 1, 4),
(871, 'Computer Graphics', 3, 1, 4),
(872, 'IT Capstone 2', 3, 1, 4),
(873, 'Info Assurance and Security - Data Privacy', 3, 1, 4),
(874, 'Network Technology', 3, 1, 4),
(875, 'IT Ele 4 - Game Dev', 3, 1, 4),
(876, 'EXAAA', 12, 3, 2),
(877, 'sample', 3, 4, 1),
(878, 'sampleeeeee', 2, 4, 2),
(879, 'SAMPLE', 1, 3, 3);

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
(2, 'BSCS'),
(3, 'BSBA'),
(5, 'BSTM');

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
(8, 3, 3, 13),
(11, 1, 1, 17),
(24, 1, 1, 36),
(25, 5, 5, 37);

-- --------------------------------------------------------

--
-- Table structure for table `preferredsubjects`
--

CREATE TABLE `preferredsubjects` (
  `PreferredSubjectID` int(11) NOT NULL,
  `FacultyID` int(11) NOT NULL,
  `CurriculumID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preferredsubjects`
--

INSERT INTO `preferredsubjects` (`PreferredSubjectID`, `FacultyID`, `CurriculumID`) VALUES
(1, 0, 862),
(88, 8, 789),
(89, 8, 800),
(90, 8, 799),
(105, 11, 862),
(106, 11, 866),
(107, 11, 871),
(152, 24, 862),
(153, 24, 866),
(154, 24, 860),
(155, 24, 874),
(156, 25, 763),
(157, 25, 773),
(158, 25, 767);

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
(2, 'BSCS'),
(3, 'BSBA'),
(4, 'BAPSYCH'),
(5, 'BSTM');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` int(11) NOT NULL,
  `RoomName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `RoomName`) VALUES
(1, '212'),
(2, '102'),
(3, '103'),
(4, '104'),
(6, 'Laboratory 1');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `ScheduleID` int(11) NOT NULL,
  `CurriculumID` int(11) NOT NULL,
  `FacultyID` int(11) NOT NULL,
  `Day` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `RoomID` int(11) NOT NULL,
  `SectionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`ScheduleID`, `CurriculumID`, `FacultyID`, `Day`, `StartTime`, `EndTime`, `RoomID`, `SectionID`) VALUES
(76, 789, 8, 'Monday', '10:30:00', '12:30:00', 2, 1),
(77, 789, 8, 'Tuesday', '10:30:00', '12:30:00', 2, 1),
(78, 789, 8, 'Wednesday', '10:30:00', '12:30:00', 2, 1),
(79, 789, 8, 'Thursday', '10:30:00', '12:30:00', 2, 1),
(80, 789, 8, 'Friday', '10:30:00', '12:30:00', 2, 1),
(81, 789, 8, 'Saturday', '10:30:00', '12:30:00', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `SectionID` int(11) NOT NULL,
  `SectionName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`SectionID`, `SectionName`) VALUES
(1, 'BSIT 102P'),
(3, 'BSIT 101P'),
(6, 'BSIT 301P');

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
  `ProfilePic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `FirstName`, `MiddleName`, `LastName`, `Role`, `ProfilePic`) VALUES
(11, 'adminHQ', '$2y$10$/eLBGpL9XIkHVdw7vA9sCewTqHOMX/pDS3IazeIdrrAsrx/cyncYC', 'Via', '', 'De Ocampo', 'admin', NULL),
(13, 'juan09', '$2y$10$mCDDngCuXhNLO1HMwCECouh61598swvo.DdV4ajZXj5uQ.JFzK5su', 'Juan', '', 'Dela Cruz', 'faculty', NULL),
(17, 'Via', '$2y$10$4p75wqPAbvZWfaGxn1QXgeHr6t1XuU4W0wugkULMt1vMZ/7jPKcWW', 'Viaa', 'D', 'De Ocampo', 'faculty', NULL),
(34, 'AASASAAS', '$2y$10$hY9LGJ3qF5oV7iVCcY3NquSIKSYxOYEkYf8FIiHMaXP8QM7nsm7nK', 'ASDASD', 'ASDSA', 'DASDASD', 'admin', NULL),
(35, 'gelo', '$2y$10$gMX0KPKGXFKLk3JZaXDi0.0qwf/iw0YqthAouaxfKeTqaa.TCgqQa', 'Angelo', '', 'Michellin', 'admin', 'src/uploads/profile_pic/682739bea3c67.jpg'),
(36, 'Kaizen', '$2y$10$aj5uIxwBzoKZJXwMBUOP/eBGspnVUKYkOPwDgwixmhYXfWIHOfupW', 'Vin', '', 'Szchiro', 'faculty', NULL),
(37, 'Vach', '$2y$10$Iz9jTX4t8YwglDVSNXI1x.z7hLZPMuXj0IxkY1mSG29Rygi.F3Hny', 'Vach', '', 'Neriz', 'faculty', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD PRIMARY KEY (`CurriculumID`),
  ADD KEY `fk_programid` (`ProgramID`);

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
  ADD KEY `fk_facultymembers_departmentid` (`DepartmentID`),
  ADD KEY `fk_facultymembers_programid` (`ProgramID`),
  ADD KEY `fk_facultymembers_userid` (`UserID`);

--
-- Indexes for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  ADD PRIMARY KEY (`PreferredSubjectID`),
  ADD KEY `fk_preferredsubjects_curriculumid` (`CurriculumID`),
  ADD KEY `fk_preferredsubjects_facultyid` (`FacultyID`);

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
  ADD KEY `fk_schedules_curriculumid` (`CurriculumID`),
  ADD KEY `fk_schedules_facultyid` (`FacultyID`),
  ADD KEY `fk_schedules_roomid` (`RoomID`),
  ADD KEY `fk_schedules_sectionid` (`SectionID`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`SectionID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `curriculums`
--
ALTER TABLE `curriculums`
  MODIFY `CurriculumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=880;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `facultymembers`
--
ALTER TABLE `facultymembers`
  MODIFY `FacultyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  MODIFY `PreferredSubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `ProgramID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `SectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD CONSTRAINT `fk_programid` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`) ON DELETE CASCADE;

--
-- Constraints for table `facultymembers`
--
ALTER TABLE `facultymembers`
  ADD CONSTRAINT `fk_facultymembers_departmentid` FOREIGN KEY (`DepartmentID`) REFERENCES `departments` (`DepartmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_facultymembers_programid` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_facultymembers_userid` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_curriculumid` FOREIGN KEY (`CurriculumID`) REFERENCES `curriculums` (`CurriculumID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedules_facultyid` FOREIGN KEY (`FacultyID`) REFERENCES `facultymembers` (`FacultyID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedules_roomid` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedules_sectionid` FOREIGN KEY (`SectionID`) REFERENCES `sections` (`SectionID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
