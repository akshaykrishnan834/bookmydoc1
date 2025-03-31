-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 04:50 PM
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
-- Database: `bookmydoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(1, 'admin@bookmydoc.com', '$2y$10$/q/nIxvZN3ge3VV/sd8gfObNAZQEObhq7VszrOrJ2HX/9Evk2iK2O');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_requests`
--

CREATE TABLE `appointment_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('pending','approved','expired','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `patient_condition` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_requests`
--

INSERT INTO `appointment_requests` (`id`, `user_id`, `doctor_id`, `slot_id`, `appointment_date`, `status`, `created_at`, `fees`, `payment_status`, `patient_condition`, `rejection_reason`) VALUES
(1, 5, 13, 187, '2025-03-27', 'expired', '2025-03-26 06:04:00', 0.00, 'Paid', 'Heavy Fever', NULL),
(3, 6, 13, 194, '2025-03-27', 'expired', '2025-03-26 08:43:06', 0.00, 'Paid', '', NULL),
(5, 6, 15, 203, '2025-03-27', 'expired', '2025-03-26 09:16:09', 0.00, 'pending', '', NULL),
(7, 6, 16, 207, '2025-03-27', 'expired', '2025-03-27 06:09:46', 0.00, 'pending', '', NULL),
(8, 6, 16, 208, '2025-03-27', 'approved', '2025-03-27 06:09:59', 0.00, 'Paid', '', NULL),
(10, 5, 13, 187, '2025-03-28', 'approved', '2025-03-27 06:38:08', 0.00, 'Paid', 'heavy head ache', NULL),
(11, 7, 15, 201, '2025-03-28', 'pending', '2025-03-27 06:39:21', 0.00, 'pending', 'fever , cold and cough', NULL),
(13, 7, 13, 187, '2025-03-29', 'pending', '2025-03-27 06:41:49', 0.00, 'pending', 'cold', NULL),
(14, 5, 13, 191, '2025-03-29', 'approved', '2025-03-27 10:03:11', 0.00, 'Paid', 'cold', NULL),
(17, 5, 13, 194, '2025-04-05', 'pending', '2025-03-27 10:12:40', 0.00, 'pending', 'periodic checkup', NULL),
(19, 5, 13, 192, '2025-03-29', 'pending', '2025-03-27 10:15:12', 0.00, 'pending', 'cold', NULL),
(20, 5, 13, 188, '2025-04-04', 'approved', '2025-03-27 11:05:02', 0.00, 'Paid', '', NULL),
(21, 5, 13, 188, '2025-03-29', 'rejected', '2025-03-27 11:10:10', 0.00, 'pending', 'cold', 'invalid id');

-- --------------------------------------------------------

--
-- Table structure for table `doctorreg`
--

CREATE TABLE `doctorreg` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `age` int(11) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `degree_certificate` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','approved') DEFAULT 'pending',
  `address` varchar(255) DEFAULT NULL,
  `location` varchar(20) DEFAULT NULL,
  `fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rejection_reason` text DEFAULT NULL,
  `action` enum('enabled','disabled') NOT NULL DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctorreg`
--

INSERT INTO `doctorreg` (`id`, `name`, `phone`, `email`, `password`, `created_at`, `age`, `qualifications`, `experience`, `specialization`, `profile_photo`, `degree_certificate`, `status`, `address`, `location`, `fees`, `rejection_reason`, `action`) VALUES
(13, 'Richard A S', '9845725512', 'bookmydoconline@gmail.com', '$2y$10$pmqFmEFclDvGsckY/PwyLeN5QLFoto.QOBqXovC4U/7SWwKf2v13e', '2025-02-24 15:43:06', 45, 'MBBS Md', 12, 'Physician', 'uploads/67bc95d4a1b1d.jpg', 'uploads/degrees/13_degree.pdf', 'approved', 'Doctor\'s Clinic \r\nponkunnam', 'ponkunnam', 100.00, NULL, 'enabled'),
(14, 'Arun Kumar', '9800214572', 'arun@gmail.com', '$2y$10$JnHnaP/XNlsSV.S8sXNit.FMmr6zZEZG.hR1N7Rl/kLhg1USHoAzq', '2025-02-24 15:44:32', 40, 'MBBS MD', 17, 'skin specialist', NULL, 'uploads/degrees/14_degree.pdf', 'approved', 'Sreehari Clinic\r\nPonkunnam', 'ponkunnam', 120.00, NULL, 'enabled'),
(15, 'TJ Kumar', '9292457124', 'kumar@gmail.com', '$2y$10$/OhXupU8FErfCFFt4ACOWenF6UbLFnlkPsVvIbxAxW7BKLNVfeLjy', '2025-02-24 15:45:27', 47, 'MBBS MD', 12, 'Physician', NULL, 'uploads/degrees/15_degree.pdf', 'approved', 'health care clinic \r\nkanjirapally ', 'kanjirapally', 200.00, NULL, 'enabled'),
(16, 'James', '9574812241', 'james@gmail.com', '$2y$10$3fnlLpN6jHtiaqRYFeIXCesXmqMGz6/wOdRd2cxpZ6T8T78zf9X3u', '2025-02-24 15:47:18', 55, 'MBBS MD', 30, 'neuro', NULL, 'uploads/degrees/16_degree.pdf', 'approved', 'N S Clinic \r\npalai', 'palai', 170.00, NULL, 'enabled'),
(17, 'Ajay ', '9574812345', 'ajay@gmail.com', '$2y$10$4PNYlOIB.UkjZDEwau1n9OBAxr6JL1idgTSWsqppUzF3dw28NfjGC', '2025-02-24 15:48:58', 35, 'MBBS', 7, 'pedatrics', 'uploads/67bc95578d8a4.jpg', 'uploads/degrees/17_degree.pdf', 'approved', 'newman\'s clinic \r\ntg road \r\nkottayam ', 'kottayam', 130.00, NULL, 'disabled'),
(27, 'Thomas Jacob', '9901247851', 'tj@gmail.com', '$2y$10$NBl09.v/PjQ71JieK/4JnOP1QgBQW8Ro4L0Nn4aK586vQdUFi4cme', '2025-03-24 04:09:40', 58, 'dms dds', 12, 'dentistry', NULL, 'uploads/degrees/27_degree.pdf', 'pending', 'kottayam', 'kottayam', 0.00, 'invalid', 'enabled');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_id`, `start_time`, `end_time`, `created_at`) VALUES
(187, 13, '10:00:00', '10:15:00', '2025-03-09 16:56:12'),
(188, 13, '10:15:00', '10:30:00', '2025-03-09 16:56:12'),
(189, 13, '10:30:00', '10:45:00', '2025-03-09 16:56:12'),
(190, 13, '10:45:00', '11:00:00', '2025-03-09 16:56:12'),
(191, 13, '11:00:00', '11:15:00', '2025-03-09 16:56:12'),
(192, 13, '11:15:00', '11:30:00', '2025-03-09 16:56:12'),
(193, 13, '11:30:00', '11:45:00', '2025-03-09 16:56:12'),
(194, 13, '11:45:00', '12:00:00', '2025-03-09 16:56:12'),
(195, 14, '14:30:00', '14:45:00', '2025-03-09 16:57:29'),
(196, 14, '14:45:00', '15:00:00', '2025-03-09 16:57:29'),
(197, 14, '15:00:00', '15:15:00', '2025-03-09 16:57:29'),
(198, 14, '15:15:00', '15:30:00', '2025-03-09 16:57:29'),
(199, 15, '13:32:00', '13:47:00', '2025-03-09 16:58:05'),
(200, 15, '13:47:00', '14:02:00', '2025-03-09 16:58:05'),
(201, 15, '14:02:00', '14:17:00', '2025-03-09 16:58:05'),
(202, 15, '14:17:00', '14:32:00', '2025-03-09 16:58:05'),
(203, 15, '14:32:00', '14:47:00', '2025-03-09 16:58:05'),
(204, 15, '14:47:00', '15:02:00', '2025-03-09 16:58:05'),
(205, 15, '15:02:00', '15:17:00', '2025-03-09 16:58:05'),
(206, 16, '11:32:00', '11:47:00', '2025-03-09 16:58:49'),
(207, 16, '11:47:00', '12:02:00', '2025-03-09 16:58:49'),
(208, 16, '12:02:00', '12:17:00', '2025-03-09 16:58:49'),
(209, 16, '12:17:00', '12:32:00', '2025-03-09 16:58:49'),
(210, 16, '12:32:00', '12:47:00', '2025-03-09 16:58:49'),
(211, 17, '16:30:00', '16:45:00', '2025-03-09 16:59:27'),
(212, 17, '16:45:00', '17:00:00', '2025-03-09 16:59:27'),
(213, 17, '17:00:00', '17:15:00', '2025-03-09 16:59:27'),
(214, 17, '17:15:00', '17:30:00', '2025-03-09 16:59:27'),
(215, 17, '17:30:00', '17:45:00', '2025-03-09 16:59:27'),
(216, 17, '17:45:00', '18:00:00', '2025-03-09 16:59:27'),
(217, 17, '18:00:00', '18:15:00', '2025-03-09 16:59:27'),
(218, 17, '18:15:00', '18:30:00', '2025-03-09 16:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `doctor_id`, `patient_id`, `rating`, `feedback_text`, `created_at`) VALUES
(1, 13, 6, 5, 'superb', '2025-03-22 14:23:48'),
(2, 14, 5, 4, 'good doctor', '2025-03-22 14:31:40'),
(3, 15, 5, 3, 'nice', '2025-03-22 14:32:58'),
(4, 13, 7, 4, 'good', '2025-03-22 14:41:56'),
(5, 16, 7, 4, 'good', '2025-03-23 19:38:02'),
(6, 17, 7, 5, 'good ', '2025-03-23 19:38:33'),
(7, 17, 7, 4, 'good ', '2025-03-23 19:38:53'),
(8, 17, 6, 3, 'good ', '2025-03-23 19:53:06'),
(9, 14, 6, 5, 'super ', '2025-03-27 06:45:43'),
(10, 13, 5, 5, 'good', '2025-03-27 18:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `record_filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `patient_id`, `record_filename`, `uploaded_at`) VALUES
(1, 6, 'uploads/67d7160216257_BookMyDoc ABSTRACT.pdf', '2025-03-16 18:18:42'),
(2, 6, 'uploads/medical_record_1.pdf', '2025-03-16 18:23:36'),
(3, 6, 'uploads/medical_record_1.pdf', '2025-03-16 18:23:42'),
(4, 6, 'uploads/new1.pdf', '2025-03-16 18:37:49'),
(5, 6, 'uploads/new1.pdf', '2025-03-16 18:38:47'),
(6, 5, 'uploads/medicalrecord1.pdf', '2025-03-16 18:47:55'),
(7, 7, 'uploads/medicalrecord_dennis.docx', '2025-03-18 06:58:01'),
(8, 6, 'uploads/1742572809_Requirement Gathering.docx', '2025-03-21 16:00:09'),
(9, 6, 'uploads/1742572836_Requirement Gathering.docx', '2025-03-21 16:00:36'),
(10, 7, 'uploads/1742787180_medicalrecord_dennis (1).docx', '2025-03-24 03:33:00'),
(12, 5, 'uploads/xray_scan.docx', '2025-03-27 10:15:55'),
(13, 7, 'uploads/1743070614_13589_IExAHg_Requirement_Gathering_for_the_Online_Gym_Management_System_(1)[1].docx', '2025-03-27 10:16:54'),
(14, 5, 'uploads/1743073871_medical_certificate.pdf', '2025-03-27 11:11:11'),
(15, 5, 'uploads/1743073877_medical_certificate.pdf', '2025-03-27 11:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `patientreg`
--

CREATE TABLE `patientreg` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `medical_records` varchar(255) DEFAULT NULL,
  `action` enum('enabled','disabled') NOT NULL DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patientreg`
--

INSERT INTO `patientreg` (`id`, `name`, `phone`, `email`, `password`, `profile_pic`, `age`, `dob`, `gender`, `medical_records`, `action`) VALUES
(5, 'AKSHAY KRISHNAN', '9986342611', 'akshaykrishnan2027@mca.ajce.in', '$2y$10$t98T5vjVO34.X.CooZSyOuuq..ol9RnjvijluZ4RgAVaGVq3UOErq', 'uploads/profile_pics/5_1742372019.png', 20, '2004-07-12', 'Male', NULL, 'enabled'),
(6, 'rahul', '7052899746', 'rahul@gmail.com', '$2y$10$CWmBIMCKVy/uJg1ZvtMjnuXdkIF.pZ1z1kdfUVM16ebhwRZW3srEy', NULL, 18, '2006-04-12', 'Male', '[\"medical certificate\",\"medical certificate\",{\"name\":\"medical certificate\",\"date_added\":\"2025-03-16 17:55:13\",\"file_name\":\"BookMyDoc ABSTRACT.pdf\",\"file_type\":\"pdf\",\"file_path\":\"uploads\\/patient_6\\/67d70271f26cc_BookMyDoc ABSTRACT.pdf\"}]', 'enabled'),
(7, 'dennis jacob', '9901245874', 'dennisjacob2027@mca.ajce.in', '$2y$10$887.wnW8JK8a8U85elWa2OqCd..u5ojyDhlP5Pgf5DMGyv/OZ5nQm', 'uploads/profile_pics/7_1742268933.png', 21, '2003-11-26', 'Male', NULL, 'enabled'),
(10, 'Jhon Wick', '9875411203', 'jw@gmail.com', '$2y$10$ymMouFF073GrJReJxgr/w.Yx4BLpwbhiOL4mbKUZ3aJzwdd/eXgIG', NULL, 34, '1991-01-16', 'Male', NULL, 'enabled'),
(11, 'Aibal jose', '9645440731', 'aibaljose2027@mca.ajce.in', '$2y$10$KUvYNL8w6.CVA2dbkGcJk.SxaMHnfr6tVu7yL/Pbvjg22xG8kaB1y', NULL, 20, '2004-07-07', 'Male', NULL, 'enabled');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `payment_date` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'Success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `appointment_id`, `doctor_id`, `amount`, `payment_method`, `transaction_id`, `order_id`, `payment_date`, `status`) VALUES
(44, 1, 13, 112.00, 'Razorpay', 'pay_QBJWvIn9Xni4T3', 'order_QBJWnkblSCCH1Y', '2025-03-26 11:36:24', 'Success'),
(45, 3, 13, 112.00, 'Razorpay', 'pay_QBi8HA5U1DnjhW', 'order_QBi6sW3MxjJRhQ', '2025-03-27 11:40:23', 'Success'),
(46, 8, 16, 189.00, 'Razorpay', 'pay_QBiRTr49aeZ6Ge', 'order_QBiRNi8s3ChwZx', '2025-03-27 11:58:34', 'Success'),
(47, 10, 13, 112.00, 'Razorpay', 'pay_QBm2V3gjQcdlCu', 'order_QBm2NUD4gTKsTi', '2025-03-27 15:29:42', 'Success'),
(48, 14, 13, 112.00, 'Razorpay', 'pay_QBm7R08vcRGHV9', 'order_QBm7LUkoeRP2SF', '2025-03-27 15:34:22', 'Success'),
(49, 20, 13, 112.00, 'Razorpay', 'pay_QBnDsPRVd3g6WF', 'order_QBnDclgYJFQ6CK', '2025-03-27 16:39:10', 'Success');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `doctorreg`
--
ALTER TABLE `doctorreg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patientreg`
--
ALTER TABLE `patientreg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `doctorreg`
--
ALTER TABLE `doctorreg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `patientreg`
--
ALTER TABLE `patientreg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  ADD CONSTRAINT `appointment_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `patientreg` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_requests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctorreg` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_requests_ibfk_3` FOREIGN KEY (`slot_id`) REFERENCES `doctor_availability` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctorreg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctorreg` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patientreg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patientreg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment_requests` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctorreg` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
