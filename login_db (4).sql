-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 10:03 AM
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
-- Database: `login_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `coordinator_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `eventBooker` varchar(255) NOT NULL,
  `age_limit` int(11) DEFAULT NULL,
  `gender_restriction` enum('Male','Female','Both') DEFAULT 'Both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `category`, `event_date`, `event_time`, `coordinator_name`, `description`, `image_path`, `created_at`, `eventBooker`, `age_limit`, `gender_restriction`) VALUES
(54, '1', 'test', '2024-11-27', '14:40:00', 'Jose', 'adwa', 'uploads/kandila.jpg', '2024-11-16 09:26:03', '', 0, 'Both'),
(55, '2', 'test', '2024-11-26', '21:30:00', 'Marie', 'ad', 'uploads/mass.jpg', '2024-11-16 09:26:27', '', NULL, 'Both'),
(56, '3', 'test', '2024-12-03', '22:10:00', 'Mariah', 'test male', 'uploads/Confirmation.jpg', '2024-11-16 10:08:12', '', 0, 'Male'),
(57, '4 age', 'test', '2024-11-25', '22:30:00', 'Marie', 'age req. 15', 'uploads/Baptism.jpg', '2024-11-16 10:27:15', '', 15, 'Both'),
(58, '5 fem', 'test', '2024-11-25', '18:36:00', 'Jr', 'fem', 'uploads/Confirmation.jpg', '2024-11-16 10:32:03', '', 0, 'Female'),
(61, '8', 'test', '2024-11-29', '06:30:00', '', 'edit test', 'uploads/Confirmation.jpg', '2024-11-17 06:37:26', '', 0, 'Both');

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `event_id`, `member_email`, `registration_date`) VALUES
(94, 55, 'test@gmail.com', '2024-11-16 19:00:08'),
(95, 56, 'test@gmail.com', '2024-11-16 19:00:11'),
(96, 56, 'jojo123@gmail.com', '2024-11-17 13:56:01'),
(97, 57, 'jojo123@gmail.com', '2024-11-17 13:56:09'),
(98, 54, 'jojo123@gmail.com', '2024-11-17 14:36:32'),
(99, 54, 'girlypop@gmail.com', '2024-11-17 15:30:42'),
(100, 55, 'girlypop@gmail.com', '2024-11-17 15:35:41'),
(101, 61, 'girlypop@gmail.com', '2024-11-17 15:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(255) NOT NULL,
  `fName` varchar(255) NOT NULL,
  `lName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'member',
  `about` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `fName`, `lName`, `email`, `password`, `user_type`, `about`, `profile_pic`, `gender`, `birthdate`, `age`) VALUES
(18, 'Super', 'Admin', 'superadmin@gmail.com', 'haha', 'Admin', '', '', 'Male', '2004-01-17', 20),
(19, 'test', 'bot', 'test@gmail.com', 'haha', 'Member', '', '', 'Male', '2016-02-19', 8),
(20, 'Girly', 'Pop', 'girlypop@gmail.com', 'haha', 'Member', '', '', 'Female', '2006-06-28', 18),
(21, 'TESTa', 'M', 'tesla@gmail.com', 'HAHA', 'Member', '', '', 'Male', '1969-08-14', 55),
(22, 'Jojo', '123', 'jojo123@gmail.com', 'haha', 'Member', '', '', 'Male', '2000-01-21', 24);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
