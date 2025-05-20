-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 20, 2025 at 06:09 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u661545712_schedsys_db_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `curriculums`
--

CREATE TABLE `curriculums` (
  `CurriculumID` int(11) NOT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `SubjectArea` varchar(100) DEFAULT NULL,
  `CatalogNo` int(11) DEFAULT NULL,
  `SubjectName` varchar(255) DEFAULT NULL,
  `Units` int(11) DEFAULT NULL,
  `ProgramID` int(11) DEFAULT NULL,
  `YearLevel` enum('1','2','3','4') DEFAULT NULL,
  `Semester` enum('1','2') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculums`
--

INSERT INTO `curriculums` (`CurriculumID`, `CourseID`, `SubjectArea`, `CatalogNo`, `SubjectName`, `Units`, `ProgramID`, `YearLevel`, `Semester`) VALUES
(1, 1628, 'GEDC', 1005, 'Mathematics in the Modern World', 3, 4, '1', '1'),
(2, 1637, 'GEDC', 1008, 'Understanding the Self', 3, 4, '1', '1'),
(3, 1629, 'NSTP', 1008, 'National Service Training Program 1', 3, 4, '1', '1'),
(4, 1653, 'PHED', 1005, 'P.E./PATHFIT 1: Movement Competency Training', 2, 4, '1', '1'),
(5, 1622, 'STIC', 1002, 'Euthenics 1', 1, 4, '1', '1'),
(6, 1658, 'STIC', 1003, 'Computer Productivity Tools', 1, 4, '1', '1'),
(7, 2751, 'PSYC', 1043, 'Introduction to Psychology', 3, 4, '1', '1'),
(8, 1635, 'GEDC', 1002, 'The Contemporary World', 3, 4, '2', '1'),
(9, 1636, 'GEDC', 1003, 'The Entrepreneurial Mind', 3, 4, '2', '1'),
(10, 1633, 'GEDC', 1003, 'Readings in Philippine History', 3, 4, '2', '1'),
(11, 1778, 'PHED', 1007, 'P.E/PATHFIT 3: Individual-Dual Sports', 2, 4, '2', '1'),
(12, 2753, 'PSYC', 1045, 'Developmental Psychology', 3, 4, '2', '1'),
(13, 2754, 'PSYC', 1046, 'Psychological/Biological Psychology', 3, 4, '2', '1'),
(14, 1773, 'GEDC', 1014, 'Rizal\'s Life and Works', 3, 4, '3', '1'),
(15, 2527, 'GEDC', 1045, 'Great Books', 3, 4, '3', '1'),
(16, 2758, 'PSYC', 1050, 'Abnormal Psychology', 3, 4, '3', '1'),
(17, 2759, 'PSYC', 1051, 'Field Methods in Psychology', 5, 4, '3', '1'),
(18, 2760, 'PSYC', 1052, 'Social Psychology', 3, 4, '3', '1'),
(19, 1701, 'STIC', 1007, 'Euthenics 2', 1, 4, '4', '1'),
(20, 2768, 'PSYC', 1060, 'Research in Psychology 2', 3, 4, '4', '1'),
(21, 2767, 'PSYC', 1059, 'Psychology Elective 2', 3, 4, '4', '1'),
(22, 2769, 'PSYC', 1061, 'Psychology Elective 3', 3, 4, '4', '1'),
(23, 1616, 'BUSS', 1001, 'Basic Microeconomics', 3, 3, '1', '1'),
(24, 1635, 'GEDC', 1002, 'The Contemporary World', 3, 3, '1', '1'),
(25, 1622, 'STIC', 1002, 'Euthenics 1', 1, 3, '1', '1'),
(26, 1629, 'NSTP', 1008, 'National Service Training Program 1', 3, 3, '1', '1'),
(27, 1653, 'PHED', 1005, 'P.E./PATHFIT 1: Movement Competency Training', 2, 3, '1', '1'),
(28, 1633, 'GEDC', 1006, 'Readings in Philippine History', 3, 3, '1', '1'),
(29, 1637, 'GEDC', 1008, 'Understanding the Self', 3, 3, '1', '1'),
(30, 2481, 'GEDC', 1041, 'Philippine Popular Culture', 3, 3, '2', '1'),
(31, 1773, 'GEDC', 1014, 'Rizal\'s Life and Works', 3, 3, '2', '1'),
(32, 1651, 'GEDC', 1016, 'Purposive Communication', 3, 3, '2', '1'),
(33, 1778, 'PHED', 1007, 'P.E/PATHFIT 3: Individual-Dual Sports', 2, 3, '2', '1'),
(34, 1813, 'BUSS', 1005, 'Costing and Pricing', 3, 3, '2', '1'),
(35, 1829, 'BUSS', 1007, 'Facilities Management', 3, 3, '2', '1'),
(36, 1962, 'BUSS', 1015, 'Business Research', 3, 3, '3', '1'),
(37, 1976, 'BUSS', 1016, 'Good Governance and Social Responsibility', 3, 3, '3', '1'),
(38, 1990, 'BUSS', 1017, 'International Business Trade', 3, 3, '3', '1'),
(39, 1713, 'CBMC', 1001, 'Operations Management (TQM)', 3, 3, '3', '1'),
(40, 2022, 'BUSS', 1019, 'Managerial Accounting', 3, 3, '3', '1'),
(41, 2527, 'GEDC', 1045, 'Great Books', 3, 3, '3', '1'),
(42, 2182, 'BUSS', 1024, 'Feasibility Study', 3, 3, '4', '1'),
(43, 2193, 'BUSS', 1025, 'Entrepreneurial Management', 3, 3, '4', '1'),
(44, 1991, 'BUSS', 1020, 'Financial Management', 3, 3, '4', '1'),
(45, 2194, 'BUSS', 1026, 'Marketing Management', 3, 3, '4', '1'),
(46, 1701, 'STIC', 1007, 'Euthenics 2', 1, 3, '4', '1'),
(47, 2183, 'BUSS', 1027, 'Special Topics in Operations Management', 3, 3, '4', '1'),
(48, 1625, 'CITE', 1004, 'Introduction to Computing', 3, 2, '1', '1'),
(49, 1620, 'CITE', 1003, 'Computer Programming 1', 3, 2, '1', '1'),
(50, 1635, 'GEDC', 1002, 'The Contemporary World', 3, 2, '1', '1'),
(51, 1622, 'STIC', 1002, 'Euthenics 1', 1, 2, '1', '1'),
(52, 1651, 'GEDC', 1016, 'Purposive Communication', 3, 2, '1', '1'),
(53, 1629, 'NSTP', 1008, 'National Service Training Program 1', 3, 2, '1', '1'),
(54, 1653, 'PHED', 1005, 'P.E./PATHFIT 1: Movement Competency Training', 2, 2, '1', '1'),
(55, 1637, 'GEDC', 1008, 'Understanding the Self', 3, 2, '1', '1'),
(56, 1750, 'COSC', 1003, 'Data Structures and Algorithms', 3, 2, '2', '1'),
(57, 1766, 'COSC', 1006, 'Discrete Structures 2', 3, 2, '2', '1'),
(58, 2481, 'GEDC', 1041, 'Philippine Popular Culture', 3, 2, '2', '1'),
(59, 1778, 'PHED', 1007, 'P.E/PATHFIT 3: Individual-Dual Sports', 2, 2, '2', '1'),
(60, 1633, 'GEDC', 1006, 'Readings in Philippine History', 3, 2, '2', '1'),
(61, 1642, 'COSC', 1001, 'Principles of Communication', 3, 2, '2', '1'),
(62, 1845, 'CITE', 1010, 'Computer Programming 3', 3, 2, '2', '1'),
(63, 1773, 'GEDC', 1014, 'Rizal\'s Life and Works', 3, 2, '2', '1'),
(64, 1964, 'COSC', 1014, 'Theory of Computations with Automata', 3, 2, '3', '1'),
(65, 1749, 'CITE', 1008, 'Application Development and Emerging Technologies', 3, 2, '3', '1'),
(66, 2002, 'INSY', 1010, 'Information Assurance & Security (Cybersecurity Fundamentals)', 3, 2, '3', '1'),
(67, 1745, 'INTE', 1007, 'Quantitative Methods (Data Analysis)', 3, 2, '3', '1'),
(68, 2185, 'COSC', 1023, 'CS Elective 2', 3, 2, '3', '1'),
(69, 2275, 'COSC', 1028, 'Artificial Intelligence', 3, 2, '3', '1'),
(70, 2162, 'COSC', 1021, 'Software Engineering 1', 3, 2, '3', '1'),
(71, 2577, 'COSC', 1048, 'Methods of Research', 3, 2, '3', '1'),
(72, 1831, 'COSC', 1008, 'Platform Technology (Operating Systems)', 3, 2, '4', '1'),
(73, 1741, 'INTE', 1005, 'Network Technology 1', 3, 2, '4', '1'),
(74, 2579, 'COSC', 1050, 'CS Thesis 2', 3, 2, '4', '1'),
(75, 1701, 'STIC', 1007, 'Euthenics 2', 1, 2, '4', '1'),
(76, 1913, 'BUSS', 1013, 'Technopreneurship', 3, 2, '4', '1'),
(77, 1846, 'INSY', 1003, 'Professional Issues in Information Systems and Technology', 3, 2, '4', '1'),
(78, 2087, 'INSY', 1005, 'Information Assurance & Security (Data Privacy)', 3, 2, '4', '1'),
(79, 2298, 'INSY', 1027, 'Software Quality Assurance', 3, 2, '4', '1'),
(80, 1625, 'CITE', 1004, 'Introduction to Computing', 3, 1, '1', '1'),
(81, 1620, 'CITE', 1003, 'Computer Programming 1', 3, 1, '1', '1'),
(82, 1635, 'GEDC', 1002, 'The Contemporary World', 3, 1, '1', '1'),
(83, 1622, 'STIC', 1002, 'Euthenics 1', 1, 1, '1', '1'),
(84, 1651, 'GEDC', 1016, 'Purposive Communication', 3, 1, '1', '1'),
(85, 1629, 'NSTP', 1008, 'National Service Training Program 1', 3, 1, '1', '1'),
(86, 1653, 'PHED', 1005, 'P.E./PATHFIT 1: Movement Competency Training', 2, 1, '1', '1'),
(87, 2481, 'GEDC', 1041, 'Philippine Popular Culture', 3, 1, '1', '1'),
(88, 1637, 'GEDC', 1008, 'Understanding the Self', 3, 1, '1', '1'),
(89, 1750, 'COSC', 1003, 'Data Structures and Algorithms', 3, 1, '2', '1'),
(90, 1633, 'GEDC', 1006, 'Readings in Philippine History', 3, 1, '2', '1'),
(91, 1778, 'PHED', 1007, 'P.E/PATHFIT 3: Individual-Dual Sports', 2, 1, '2', '1'),
(92, 1773, 'GEDC', 1014, 'Rizal\'s Life and Works', 3, 1, '2', '1'),
(93, 1816, 'COSC', 1007, 'Human-Computer Interaction', 3, 1, '2', '1'),
(94, 1836, 'INTE', 1015, 'IT Elective 1', 3, 1, '2', '1'),
(95, 1642, 'COSC', 1001, 'Principles of Communication', 3, 1, '2', '1'),
(96, 1831, 'COSC', 1008, 'Platform Technology (Operating Systems)', 3, 1, '2', '1'),
(97, 1749, 'CITE', 1008, 'Application Development and Emerging Technologies', 3, 1, '3', '1'),
(98, 2003, 'INSY', 1011, 'Advanced Database Systems', 3, 1, '3', '1'),
(99, 2031, 'INTE', 1024, 'Event-Driven Programming', 3, 1, '3', '1'),
(100, 2054, 'INTE', 1025, 'Data and Digital Communications (Data Communications)', 3, 1, '3', '1'),
(101, 1846, 'INSY', 1003, 'Professional Issues in Information Systems and Technology', 3, 1, '3', '1'),
(102, 1907, 'INTE', 1019, 'IT Elective 2', 3, 1, '3', '1'),
(103, 2314, 'INTE', 1056, 'Advanced Systems Integration and Architecture', 3, 1, '3', '1'),
(104, 1701, 'STIC', 1007, 'Euthenics 2', 1, 1, '4', '1'),
(105, 2218, 'INTE', 1039, 'IT Capstone Project 2', 3, 1, '4', '1'),
(106, 2230, 'INTE', 1040, 'IT Elective 4', 3, 1, '4', '1'),
(107, 2241, 'INTE', 1041, 'Computer Graphics Programming', 3, 1, '4', '1'),
(108, 1823, 'INTE', 1013, 'IT Service Management', 3, 1, '4', '1'),
(109, 2087, 'INSY', 1005, 'Information Assurance & Security (Data Privacy)', 3, 1, '4', '1'),
(110, 2126, 'INTE', 1030, 'Network Technology 2', 3, 1, '4', '1'),
(111, 1680, 'GEDC', 1009, 'Ethics', 3, 4, '1', '2'),
(112, 1650, 'GEDC', 1013, 'Science, Technology, and Society', 3, 4, '1', '2'),
(113, 1651, 'GEDC', 1016, 'Purposive Communication', 3, 4, '1', '2'),
(114, 1685, 'NSTP', 1010, 'National Service Training Program 2', 3, 4, '1', '2'),
(115, 1693, 'PHED', 1006, 'P.E./PATHFIT 2 Exercise-based Fitness Activities', 2, 4, '1', '2'),
(116, 2752, 'PSYC', 1044, 'Psychological Statistics', 5, 4, '1', '2'),
(117, 1681, 'GEDC', 1010, 'Art Appreciation', 3, 4, '2', '2'),
(118, 2481, 'GEDC', 1041, 'Philippine Popular Culture', 3, 4, '2', '2'),
(119, 1879, 'PHED', 1008, 'P.E./PATHFIT 4: Team Sports', 2, 4, '2', '2'),
(120, 2755, 'PSYC', 1047, 'Cognitive Psychology', 3, 4, '2', '2'),
(121, 2756, 'PSYC', 1048, 'Experimental Psychology', 5, 4, '2', '2'),
(122, 2757, 'PSYC', 1049, 'Theories of Personality', 3, 4, '2', '2'),
(123, 2761, 'PSYC', 1053, 'Filipino Psychology', 3, 4, '3', '2'),
(124, 2762, 'PSYC', 1054, 'Industrial/Organizational Psychology', 3, 4, '3', '2'),
(125, 2763, 'PSYC', 1055, 'Psychology Assessment', 5, 4, '3', '2'),
(126, 2764, 'PSYC', 1056, 'Psychology Elective 1', 3, 4, '3', '2'),
(127, 2766, 'PSYC', 1058, 'Research in Psychology 1', 3, 4, '3', '2'),
(128, 2765, 'PSYC', 1057, 'Practicum in Psychology - 450 hours OJT', 3, 4, '4', '2'),
(129, 1680, 'GEDC', 1009, 'Ethics', 3, 3, '1', '2'),
(130, 1685, 'NSTP', 1010, 'National Service Training Program 2', 3, 3, '1', '2'),
(131, 1693, 'PHED', 1006, 'P.E./PATHFIT 2 Exercise-based Fitness Activities', 2, 3, '1', '2'),
(132, 1628, 'GEDC', 1005, 'Mathematics in the Modern World', 3, 3, '1', '2'),
(133, 1650, 'GEDC', 1013, 'Science, Technology, and Society', 3, 3, '1', '2'),
(134, 1658, 'STIC', 1003, 'Computer Productivity Tools', 1, 3, '1', '2'),
(135, 1730, 'BUSS', 1004, 'Productivity and Quality Tools', 3, 3, '1', '2'),
(136, 1851, 'BUSS', 1008, 'Business Law (Obligations and Contracts)', 3, 3, '2', '2'),
(137, 1867, 'BUSS', 1011, 'Taxation (Income Taxation)', 3, 3, '2', '2'),
(138, 1636, 'GEDC', 1003, 'The Entrepreneurial Mind', 3, 3, '2', '2'),
(139, 1681, 'GEDC', 1010, 'Art Appreciation', 3, 3, '2', '2'),
(140, 1879, 'PHED', 1008, 'P.E./PATHFIT 4: Team Sports', 2, 3, '2', '2'),
(141, 1914, 'BUSS', 1014, 'Logistics Management', 3, 3, '2', '2'),
(142, 1898, 'BUSS', 1009, 'Human Resource Management', 3, 3, '3', '2'),
(143, 1815, 'CBMC', 1002, 'Strategic Management', 3, 3, '3', '2'),
(144, 1954, 'INSY', 1007, 'Management Information System', 3, 3, '3', '2'),
(145, 2115, 'BUSS', 1021, 'Environmental Management System', 3, 3, '3', '2'),
(146, 2132, 'BUSS', 1022, 'Inventory Management and Control', 3, 3, '3', '2'),
(147, 2140, 'INTE', 1035, 'Project Management', 3, 3, '3', '2'),
(148, 2259, 'OJTC', 1005, 'Practicum (600 hours)', 6, 3, '4', '2'),
(149, 1676, 'CITE', 1006, 'Computer Programming 2', 3, 2, '1', '2'),
(150, 1688, 'COSC', 1002, 'Discrete Structures 1 (Discrete Mathematics)', 3, 2, '1', '2'),
(151, 1681, 'GEDC', 1010, 'Art Appreciation', 3, 2, '1', '2'),
(152, 1685, 'NSTP', 1010, 'National Service Training Program 2', 3, 2, '1', '2'),
(153, 1693, 'PHED', 1006, 'P.E./PATHFIT 2 Exercise-based Fitness Activities', 2, 2, '1', '2'),
(154, 1628, 'GEDC', 1005, 'Mathematics in the Modern World', 3, 2, '1', '2'),
(155, 1650, 'GEDC', 1013, 'Science, Technology, and Society', 3, 2, '1', '2'),
(156, 2556, 'COSC', 1046, 'College Calculus', 3, 2, '1', '2'),
(157, 1854, 'COSC', 1009, 'Design and Analysis of Algorithms', 3, 2, '2', '2'),
(158, 1852, 'CITE', 1011, 'Information Management', 3, 2, '2', '2'),
(159, 1636, 'GEDC', 1003, 'The Entrepreneurial Mind', 3, 2, '2', '2'),
(160, 1680, 'GEDC', 1009, 'Ethics', 3, 2, '2', '2'),
(161, 1879, 'PHED', 1008, 'P.E./PATHFIT 4: Team Sports', 2, 2, '2', '2'),
(162, 1985, 'INTE', 1023, 'Computer Systems Architecture', 3, 2, '2', '2'),
(163, 1816, 'COSC', 1007, 'Human-Computer Interaction', 3, 2, '2', '2'),
(164, 2023, 'COSC', 1015, 'CS Elective 1', 3, 2, '2', '2'),
(165, 2527, 'GEDC', 1045, 'Great Books', 3, 2, '2', '2'),
(166, 2072, 'COSC', 1016, 'Modeling and Simulation', 3, 2, '3', '2'),
(167, 2496, 'COSC', 1042, 'Game Programming', 3, 2, '3', '2'),
(168, 2150, 'COSC', 1020, 'Programming Languages', 3, 2, '3', '2'),
(169, 1993, 'CITE', 1013, 'Computer Organization', 3, 2, '3', '2'),
(170, 2237, 'COSC', 1025, 'Software Engineering 2', 3, 2, '3', '2'),
(171, 2249, 'COSC', 1026, 'CS Elective 3', 3, 2, '3', '2'),
(172, 2578, 'COSC', 1049, 'CS Thesis 1', 3, 2, '3', '2'),
(173, 2580, 'COSC', 1051, 'CS Practicum (300 hours)', 6, 2, '4', '2'),
(174, 1676, 'CITE', 1006, 'Computer Programming 2', 3, 1, '1', '2'),
(175, 1688, 'COSC', 1002, 'Discrete Structures 1 (Discrete Mathematics)', 3, 1, '1', '2'),
(176, 1681, 'GEDC', 1010, 'Art Appreciation', 3, 1, '1', '2'),
(177, 1685, 'NSTP', 1010, 'National Service Training Program 2', 3, 1, '1', '2'),
(178, 1693, 'PHED', 1006, 'P.E./PATHFIT 2 Exercise-based Fitness Activities', 2, 1, '1', '2'),
(179, 1628, 'GEDC', 1005, 'Mathematics in the Modern World', 3, 1, '1', '2'),
(180, 1650, 'GEDC', 1013, 'Science, Technology, and Society', 3, 1, '1', '2'),
(181, 1680, 'GEDC', 1009, 'Ethics', 3, 1, '1', '2'),
(182, 1744, 'INTE', 1006, 'Systems Administration and Maintenance', 3, 1, '1', '2'),
(183, 1852, 'CITE', 1011, 'Information Management', 3, 1, '2', '2'),
(184, 1636, 'GEDC', 1003, 'The Entrepreneurial Mind', 3, 1, '2', '2'),
(185, 1879, 'PHED', 1008, 'P.E./PATHFIT 4: Team Sports', 2, 1, '2', '2'),
(186, 1741, 'INTE', 1005, 'Network Technology 1', 3, 1, '2', '2'),
(187, 1937, 'INTE', 1020, 'Quantitative Methods', 3, 1, '2', '2'),
(188, 1913, 'BUSS', 1013, 'Technopreneurship', 3, 1, '2', '2'),
(189, 1950, 'INTE', 1021, 'Systems Integration and Architecture', 3, 1, '2', '2'),
(190, 1955, 'INTE', 1010, 'Integrative Programming', 3, 1, '2', '2'),
(191, 2494, 'INTE', 1083, 'Web Systems and Technologies', 3, 1, '3', '2'),
(192, 1954, 'INSY', 1007, 'Management Information System', 3, 1, '3', '2'),
(193, 2127, 'INTE', 1031, 'IT Capstone Project 1', 3, 1, '3', '2'),
(194, 2527, 'GEDC', 1045, 'Great Books', 3, 1, '3', '2'),
(195, 2044, 'INTE', 1027, 'IT Elective 3', 3, 1, '3', '2'),
(196, 2495, 'INTE', 1084, 'Mobile Systems and Technologies', 3, 1, '3', '2'),
(197, 2002, 'INSY', 1010, 'Information Assurance & Security (Cybersecurity Fundamentals)', 3, 1, '3', '2'),
(198, 2253, 'INTE', 1043, 'IT Practicum (486 hours)', 9, 1, '4', '2');

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
(26, 1, 1, 38),
(29, 3, 3, 41),
(30, 1, 1, 43),
(31, 3, 3, 44);

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
(167, 29, 139),
(168, 29, 23),
(169, 29, 136),
(170, 26, 98),
(171, 26, 103),
(172, 26, 97),
(173, 26, 176),
(174, 30, 189),
(175, 30, 188),
(176, 31, 139),
(177, 31, 23),
(178, 31, 136);

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
(6, 'LAB 1');

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
  `SectionID` int(11) NOT NULL,
  `SchoolYearSemesterID` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`ScheduleID`, `CurriculumID`, `FacultyID`, `Day`, `StartTime`, `EndTime`, `RoomID`, `SectionID`, `SchoolYearSemesterID`) VALUES
(1, 139, 26, 'Monday', '10:30:00', '12:30:00', 2, 8, 1),
(2, 139, 26, 'Saturday', '10:30:00', '12:30:00', 2, 8, 1),
(3, 23, 26, 'Thursday', '09:30:00', '10:30:00', 2, 8, 1),
(4, 23, 26, 'Friday', '09:30:00', '10:30:00', 2, 8, 1),
(5, 136, 26, 'Monday', '14:30:00', '15:30:00', 2, 8, 1),
(6, 139, 31, 'Monday', '08:00:00', '10:00:00', 1, 9, 2),
(7, 139, 31, 'Wednesday', '08:00:00', '10:00:00', 1, 9, 2),
(8, 139, 31, 'Friday', '08:00:00', '10:00:00', 1, 9, 2),
(9, 189, 30, 'Monday', '12:00:00', '13:00:00', 2, 3, 2),
(10, 189, 30, 'Wednesday', '12:00:00', '13:00:00', 2, 3, 2),
(11, 189, 30, 'Friday', '12:00:00', '13:00:00', 2, 3, 2),
(12, 189, 30, 'Thursday', '07:00:00', '08:00:00', 4, 3, 2),
(13, 189, 30, 'Friday', '07:00:00', '08:00:00', 4, 3, 2),
(14, 188, 30, 'Tuesday', '15:00:00', '17:30:00', 6, 6, 2),
(15, 188, 30, 'Thursday', '15:00:00', '17:30:00', 6, 6, 2),
(16, 188, 30, 'Saturday', '15:00:00', '17:30:00', 6, 6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `school_year_semesters`
--

CREATE TABLE `school_year_semesters` (
  `ID` int(11) NOT NULL,
  `SchoolYear` varchar(20) NOT NULL,
  `Semester` enum('1','2') NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_year_semesters`
--

INSERT INTO `school_year_semesters` (`ID`, `SchoolYear`, `Semester`, `IsActive`) VALUES
(1, '2024-2025', '1', 0),
(2, '2024-2025', '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `SectionID` int(11) NOT NULL,
  `SectionName` varchar(255) NOT NULL,
  `ProgramID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`SectionID`, `SectionName`, `ProgramID`) VALUES
(1, 'BSIT 102P', 1),
(3, 'BSIT 101P', 1),
(6, 'BSIT 301P', 1),
(8, 'BSCS 101A', 2),
(9, 'BSBA 101E', 3);

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
(11, 'adminHQ', '$2y$10$/eLBGpL9XIkHVdw7vA9sCewTqHOMX/pDS3IazeIdrrAsrx/cyncYC', 'Via', 'Hiroshi', 'De Ocampo', 'admin', NULL),
(35, 'gelo', '$2y$10$gMX0KPKGXFKLk3JZaXDi0.0qwf/iw0YqthAouaxfKeTqaa.TCgqQa', 'Angelo', '', 'Michellin', 'admin', 'src/uploads/profile_pic/682739bea3c67.jpg'),
(38, 'fac1', '$2y$10$w3aCHCwyIAogCDIUNs7saOwCKHYStKANpGV31WPdegz/l0Yvv5QjK', 'fac1', '', 'fac123', 'faculty', 'src/uploads/profile_pic/6829c2d407861.jpg'),
(41, 'jdoe', '$2y$10$JH9l1lzJx/kILgpGcYEL6.ZX5b16eWN/OwwEq0G57mGNRm9MdqGF6', 'jdoe', '', 'doe123', 'faculty', NULL),
(43, 'Vach', '$2y$10$wKF2HHqhOT884PNsALtL8eXIJicQgH2IbtjCkP0ri1F3cMxY96PhO', 'Vach', '', 'Neriz', 'faculty', NULL),
(44, 'Jim', '$2y$10$4OWiQZqTgZ0Gw52FPn4rnefv/JWV05QFw1OJ9/QBOlxgnh4tLNOGC', 'Jim', '', 'Cruz', 'faculty', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD PRIMARY KEY (`CurriculumID`);

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
  ADD KEY `fk_schedules_sectionid` (`SectionID`),
  ADD KEY `fk_schedules_schoolyearsemesterid` (`SchoolYearSemesterID`);

--
-- Indexes for table `school_year_semesters`
--
ALTER TABLE `school_year_semesters`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`SectionID`),
  ADD KEY `fk_sections_programid` (`ProgramID`);

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
  MODIFY `CurriculumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `facultymembers`
--
ALTER TABLE `facultymembers`
  MODIFY `FacultyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `preferredsubjects`
--
ALTER TABLE `preferredsubjects`
  MODIFY `PreferredSubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

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
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `school_year_semesters`
--
ALTER TABLE `school_year_semesters`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `SectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `fk_schedules_schoolyearsemesterid` FOREIGN KEY (`SchoolYearSemesterID`) REFERENCES `school_year_semesters` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedules_sectionid` FOREIGN KEY (`SectionID`) REFERENCES `sections` (`SectionID`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `fk_sections_programid` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
