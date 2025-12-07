-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 07, 2025 at 04:05 AM
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
-- Database: `ChapelSeminarDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Attendance`
--

CREATE TABLE `Attendance` (
  `AttendanceID` int(10) UNSIGNED NOT NULL,
  `SeminarID` int(10) UNSIGNED NOT NULL,
  `StudentID` int(10) UNSIGNED NOT NULL,
  `AttendanceStatus` varchar(20) DEFAULT 'Present',
  `CheckInTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Attendance`
--

INSERT INTO `Attendance` (`AttendanceID`, `SeminarID`, `StudentID`, `AttendanceStatus`, `CheckInTime`) VALUES
(1, 1, 2, 'Present', '2025-12-07 03:05:06'),
(2, 1, 6, 'Present', '2025-12-07 03:05:06'),
(3, 1, 11, 'Present', '2025-12-07 03:05:06'),
(4, 1, 4, 'Present', '2025-12-07 03:05:06'),
(5, 1, 9, 'Present', '2025-12-07 03:05:06'),
(6, 1, 10, 'Present', '2025-12-07 03:05:06'),
(7, 1, 5, 'Present', '2025-12-07 03:05:06'),
(8, 2, 3, 'Present', '2025-12-07 03:05:06'),
(9, 2, 8, 'Present', '2025-12-07 03:05:06'),
(10, 2, 12, 'Present', '2025-12-07 03:05:06'),
(11, 3, 4, 'Present', '2025-12-07 03:05:06'),
(12, 3, 9, 'Present', '2025-12-07 03:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `Seminar`
--

CREATE TABLE `Seminar` (
  `SeminarID` int(10) UNSIGNED NOT NULL,
  `Topic` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `TargetLevels` varchar(50) DEFAULT 'All',
  `EventDate` date NOT NULL,
  `EventTime` time NOT NULL,
  `Faculty` varchar(100) DEFAULT NULL,
  `VenueID` int(10) UNSIGNED DEFAULT NULL,
  `SpeakerID` int(10) UNSIGNED DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Seminar`
--

INSERT INTO `Seminar` (`SeminarID`, `Topic`, `Description`, `TargetLevels`, `EventDate`, `EventTime`, `Faculty`, `VenueID`, `SpeakerID`, `CreatedAt`) VALUES
(1, 'Tech Trends 2025', 'The Future of AI in Africa', 'All', '2025-12-02', '10:00:00', 'Computing', 1, 1, '2025-12-07 03:05:06'),
(2, 'Legal Ethics', 'Understanding the Constitution', '400,500', '2025-12-05', '14:00:00', 'Law', 2, 2, '2025-12-07 03:05:06'),
(3, 'Engineering Safety', 'Modern Structural Integrity', 'All', '2025-12-07', '09:00:00', 'Engineering', 2, 4, '2025-12-07 03:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `Speaker`
--

CREATE TABLE `Speaker` (
  `SpeakerID` int(10) UNSIGNED NOT NULL,
  `SpeakerName` varchar(255) NOT NULL,
  `SpeakerBio` text DEFAULT NULL,
  `ContactEmail` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Speaker`
--

INSERT INTO `Speaker` (`SpeakerID`, `SpeakerName`, `SpeakerBio`, `ContactEmail`) VALUES
(1, 'Dr. Adewale', 'Expert in Artificial Intelligence and Robotics', NULL),
(2, 'Prof. Chioma', 'Dean of Law, Constitutional Rights Activist', NULL),
(3, 'Dr. Ibrahim', 'International Business Strategist', NULL),
(4, 'Engr. Tunde', 'Civil Engineering Consultant', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `StaffUser`
--

CREATE TABLE `StaffUser` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `StaffID` varchar(50) NOT NULL,
  `Faculty` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE `Student` (
  `StudentID` int(10) UNSIGNED NOT NULL,
  `MatricNo` varchar(50) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Faculty` varchar(100) NOT NULL,
  `Level` int(11) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Student`
--

INSERT INTO `Student` (`StudentID`, `MatricNo`, `FullName`, `Faculty`, `Level`, `Email`, `CreatedAt`) VALUES
(1, '21/001', 'Joy Nwankwo', 'Medicine', 400, 'joy@babcock.edu', '2025-12-07 03:05:06'),
(2, '21/002', 'Kenneth Olagunju', 'Computing', 300, 'ken@babcock.edu', '2025-12-07 03:05:06'),
(3, '21/003', 'Sarah Adebayo', 'Law', 500, 'sarah@babcock.edu', '2025-12-07 03:05:06'),
(4, '21/004', 'David Okon', 'Engineering', 200, 'david@babcock.edu', '2025-12-07 03:05:06'),
(5, '21/005', 'Blessing Udoh', 'Business', 400, 'bless@babcock.edu', '2025-12-07 03:05:06'),
(6, '21/006', 'Michael Sani', 'Computing', 100, 'mike@babcock.edu', '2025-12-07 03:05:06'),
(7, '21/007', 'Esther Okafor', 'Medicine', 300, 'esther@babcock.edu', '2025-12-07 03:05:06'),
(8, '21/008', 'Tola Johnson', 'Law', 400, 'tola@babcock.edu', '2025-12-07 03:05:06'),
(9, '21/009', 'Femi Adeyemi', 'Engineering', 500, 'femi@babcock.edu', '2025-12-07 03:05:06'),
(10, '21/010', 'Grace Okoro', 'Sciences', 200, 'grace@babcock.edu', '2025-12-07 03:05:06'),
(11, '21/011', 'Samuel Kalu', 'Computing', 200, 'sam@babcock.edu', '2025-12-07 03:05:06'),
(12, '21/012', 'Chioma Obi', 'Law', 100, 'chioma@babcock.edu', '2025-12-07 03:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `Venue`
--

CREATE TABLE `Venue` (
  `VenueID` int(10) UNSIGNED NOT NULL,
  `VenueName` varchar(100) NOT NULL,
  `Capacity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Venue`
--

INSERT INTO `Venue` (`VenueID`, `VenueName`, `Capacity`) VALUES
(1, 'Babcock Main Auditorium', 2500),
(2, 'Amphitheatre', 600),
(3, 'New Horizon Hall', 300),
(4, 'Science Complex Hall A', 150);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD PRIMARY KEY (`AttendanceID`),
  ADD UNIQUE KEY `unique_attendance` (`SeminarID`,`StudentID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `Seminar`
--
ALTER TABLE `Seminar`
  ADD PRIMARY KEY (`SeminarID`),
  ADD UNIQUE KEY `unique_booking` (`EventDate`,`EventTime`,`VenueID`),
  ADD KEY `VenueID` (`VenueID`),
  ADD KEY `SpeakerID` (`SpeakerID`);

--
-- Indexes for table `Speaker`
--
ALTER TABLE `Speaker`
  ADD PRIMARY KEY (`SpeakerID`);

--
-- Indexes for table `StaffUser`
--
ALTER TABLE `StaffUser`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `StaffID` (`StaffID`);

--
-- Indexes for table `Student`
--
ALTER TABLE `Student`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `MatricNo` (`MatricNo`);

--
-- Indexes for table `Venue`
--
ALTER TABLE `Venue`
  ADD PRIMARY KEY (`VenueID`),
  ADD UNIQUE KEY `VenueName` (`VenueName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Attendance`
--
ALTER TABLE `Attendance`
  MODIFY `AttendanceID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Seminar`
--
ALTER TABLE `Seminar`
  MODIFY `SeminarID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Speaker`
--
ALTER TABLE `Speaker`
  MODIFY `SpeakerID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `StaffUser`
--
ALTER TABLE `StaffUser`
  MODIFY `UserID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Student`
--
ALTER TABLE `Student`
  MODIFY `StudentID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Venue`
--
ALTER TABLE `Venue`
  MODIFY `VenueID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`SeminarID`) REFERENCES `Seminar` (`SeminarID`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`) ON DELETE CASCADE;

--
-- Constraints for table `Seminar`
--
ALTER TABLE `Seminar`
  ADD CONSTRAINT `seminar_ibfk_1` FOREIGN KEY (`VenueID`) REFERENCES `Venue` (`VenueID`) ON DELETE SET NULL,
  ADD CONSTRAINT `seminar_ibfk_2` FOREIGN KEY (`SpeakerID`) REFERENCES `Speaker` (`SpeakerID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
