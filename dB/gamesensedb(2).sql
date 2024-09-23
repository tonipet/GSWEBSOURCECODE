-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2024 at 02:09 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamesensedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `UserId` int(11) NOT NULL,
  `FullName` varchar(256) DEFAULT NULL,
  `IDNumber` varchar(256) DEFAULT NULL,
  `EmailAddress` varchar(256) DEFAULT NULL,
  `Password` varchar(256) DEFAULT NULL,
  `Usertype` varchar(256) DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `SectionUID` varchar(256) DEFAULT NULL,
  `Section` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UserId`, `FullName`, `IDNumber`, `EmailAddress`, `Password`, `Usertype`, `Active`, `SectionUID`, `Section`) VALUES
(2, 'Juan Dela TOre', '10001', 'admin@gmail.com', '$2y$10$b6ywU5UOnCbNCs8gvWOtRujn5d0mLynNCmxTrUgovAr6NMiZs.agm', 'admin', 1, '1722342332900', 'Section 2 - 1st Year'),
(6, 'Faculty 1', '10002', 'TEST@gmail.com', '$2y$10$r5qUXI7i5ZWoP8vm7m8qkODohQnPMJunnO1YgaYHgi2G4fnZzbl4e', 'user', 1, '1723549399092', '1st Year - Section 1'),
(7, 'Faculty 2', '10003', 'TEST@gmail.com', '$2y$10$KLqh7naeoedZp/h9X.EveuofpERyU2sAHrJMvG1CgR7TTsXnpODa.', 'user', 1, '1723549387677', '1st Year - Section 2');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_androidstudentprofile`
--

CREATE TABLE `tbl_androidstudentprofile` (
  `ID` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `Gender` varchar(256) NOT NULL,
  `NoofHours` varchar(256) NOT NULL,
  `ParentName` varchar(256) NOT NULL,
  `Phone` varchar(256) NOT NULL,
  `SectionID` varchar(256) NOT NULL,
  `StudentName` varchar(256) NOT NULL,
  `uid` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_androidstudentsection`
--

CREATE TABLE `tbl_androidstudentsection` (
  `ID` int(11) NOT NULL,
  `UID` varchar(256) NOT NULL,
  `sectionDescription` varchar(256) NOT NULL,
  `sectionName` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_finalgrades`
--

CREATE TABLE `tbl_finalgrades` (
  `ID` int(11) NOT NULL,
  `UID` varchar(256) NOT NULL,
  `FinalGrade` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grades`
--

CREATE TABLE `tbl_grades` (
  `ID` int(11) NOT NULL,
  `ProfileID` varchar(256) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `q1` decimal(5,2) DEFAULT 0.00,
  `q2` decimal(5,2) DEFAULT 0.00,
  `q3` decimal(5,2) DEFAULT 0.00,
  `q4` decimal(5,2) DEFAULT 0.00,
  `act1` decimal(5,2) DEFAULT 0.00,
  `act2` decimal(5,2) DEFAULT 0.00,
  `act3` decimal(5,2) DEFAULT 0.00,
  `final_grades` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`UserId`);

--
-- Indexes for table `tbl_androidstudentprofile`
--
ALTER TABLE `tbl_androidstudentprofile`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_androidstudentsection`
--
ALTER TABLE `tbl_androidstudentsection`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_finalgrades`
--
ALTER TABLE `tbl_finalgrades`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_androidstudentprofile`
--
ALTER TABLE `tbl_androidstudentprofile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_androidstudentsection`
--
ALTER TABLE `tbl_androidstudentsection`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_finalgrades`
--
ALTER TABLE `tbl_finalgrades`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
