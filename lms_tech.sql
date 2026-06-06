-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2026 at 10:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_tech`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(175, 1, 'INSERT', 'Admin added a new Teacher: Test Teacher', '::1', '2026-05-09 09:06:51'),
(176, 1, 'INSERT', 'Admin added a new Manager: Test Manager', '::1', '2026-05-09 09:07:23'),
(177, 1, 'STATUS_CHANGE', 'Admin changed status of  (Test Studnet) to Enabled/Approved', NULL, '2026-05-09 09:08:52'),
(178, 38, 'LOGIN', 'User logged into the system', '::1', '2026-05-09 09:09:20'),
(179, 1, 'ASSIGN', 'Admin assigned teacher (Test Teacher) to course (Full Stack Web Development)', '::1', '2026-05-09 09:09:33'),
(180, 1, 'ASSIGN', 'Admin assigned teacher (Test Teacher) to course (Python Programming)', '::1', '2026-05-09 09:09:42'),
(181, 38, 'INSERT', 'Teacher added material: Intro Full Stack (Part 1) to Full Stack Web Development', '::1', '2026-05-09 09:10:33'),
(182, 38, 'INSERT', 'Teacher added assignment: Ass 1 (Part 1) to Full Stack Web Development', '::1', '2026-05-09 09:10:47'),
(183, 38, 'INSERT', 'Teacher added quiz: Quize 1 (Part 1) to Full Stack Web Development', '::1', '2026-05-09 09:11:02'),
(184, 38, 'INSERT', 'Teacher submitted attendance for course (Full Stack Web Development) on date: 2026-05-09', '::1', '2026-05-09 09:11:18'),
(185, 38, 'UPDATE', 'Teacher updated grade (30%) for Test Studnet in Full Stack Web Development (Mid Exam 1)', '::1', '2026-05-09 09:12:18'),
(186, 38, 'LOGOUT', 'User logged out', '::1', '2026-05-09 09:12:21'),
(187, 40, 'LOGIN', 'User logged into the system', '::1', '2026-05-09 09:13:23'),
(188, 40, 'INSERT', 'Student (A Student) submitted assignment:  for course (Full Stack Web Development)', '::1', '2026-05-09 09:13:44'),
(189, 40, 'INSERT', 'Student (A Student) submitted assignment:  for course (Full Stack Web Development)', '::1', '2026-05-09 09:14:01'),
(190, 40, 'LOGOUT', 'User logged out', '::1', '2026-05-09 09:14:16'),
(191, 38, 'LOGIN', 'User logged into the system', '::1', '2026-05-09 09:14:27'),
(192, 38, 'UPDATE', 'Teacher updated grade (10%) for Test Studnet in Full Stack Web Development (Quize 1)', '::1', '2026-05-09 09:14:43'),
(193, 38, 'UPDATE', 'Teacher updated grade (19%) for Test Studnet in Full Stack Web Development (Ass 1)', '::1', '2026-05-09 09:14:57'),
(194, 38, 'LOGOUT', 'User logged out', '::1', '2026-05-09 09:25:05'),
(195, 40, 'LOGIN', 'User logged into the system', '::1', '2026-05-09 09:25:47'),
(196, 40, 'LOGOUT', 'User logged out', '::1', '2026-05-09 09:26:44'),
(197, 39, 'LOGIN', 'User logged into the system', '::1', '2026-05-09 09:26:55'),
(198, 39, 'LOGOUT', 'User logged out', '::1', '2026-05-09 09:54:08'),
(199, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 04:56:19'),
(200, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 05:09:55'),
(201, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-25 05:10:21'),
(202, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 05:10:33'),
(203, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-25 05:13:08'),
(204, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 05:13:21'),
(205, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-25 05:14:26'),
(206, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 05:15:50'),
(207, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-25 05:16:02'),
(208, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-25 05:16:47'),
(209, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-25 05:17:19'),
(210, 1, 'LOGIN', 'User logged into the system', '::1', '2026-05-30 14:19:45'),
(211, 1, 'LOGOUT', 'User logged out', '::1', '2026-05-30 14:20:20'),
(212, 1, 'LOGIN', 'User logged into the system', '::1', '2026-06-06 07:51:23'),
(213, 1, 'STATUS_CHANGE', 'Admin changed status of  (Test Teacher) to Disabled', NULL, '2026-06-06 07:52:51'),
(214, 1, 'STATUS_CHANGE', 'Admin changed status of  (Test Teacher) to Enabled/Approved', NULL, '2026-06-06 07:53:00'),
(215, 1, 'STATUS_CHANGE', 'Admin changed status of  (Test Studnet) to Disabled', NULL, '2026-06-06 07:53:43'),
(216, 1, 'STATUS_CHANGE', 'Admin changed status of  (Test Studnet) to Enabled/Approved', NULL, '2026-06-06 07:53:56'),
(217, 1, 'LOGOUT', 'User logged out', '::1', '2026-06-06 08:00:43'),
(218, 1, 'LOGIN', 'User logged into the system', '::1', '2026-06-06 08:01:01'),
(219, 1, 'LOGOUT', 'User logged out', '::1', '2026-06-06 08:01:15'),
(220, 38, 'LOGIN', 'User logged into the system', '::1', '2026-06-06 08:01:39'),
(221, 38, 'INSERT', 'Teacher submitted attendance for course (Full Stack Web Development) on date: 2026-06-06', '::1', '2026-06-06 08:05:37'),
(222, 38, 'LOGOUT', 'User logged out', '::1', '2026-06-06 08:10:22'),
(223, 1, 'LOGIN', 'User logged into the system', '::1', '2026-06-06 08:10:29'),
(224, 1, 'LOGOUT', 'User logged out', '::1', '2026-06-06 08:10:40'),
(225, 40, 'LOGIN', 'User logged into the system', '::1', '2026-06-06 08:10:54'),
(226, 40, 'LOGOUT', 'User logged out', '::1', '2026-06-06 08:16:12');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('Present','Absent','Late') NOT NULL,
  `attendance_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `course_id`, `student_id`, `status`, `attendance_date`, `created_at`) VALUES
(23, 38, 40, 'Present', '2026-05-09', '2026-05-09 09:11:18'),
(24, 38, 40, 'Present', '2026-06-06', '2026-06-06 08:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `certificate_serial` varchar(50) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `student_id`, `teacher_id`, `course_name`, `certificate_serial`, `issue_date`, `status`) VALUES
(10, 40, 38, 'Full Stack Web Development', 'techiftiin-0001-2026', '2026-05-09', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','archived') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `teacher_id`, `created_at`) VALUES
(38, 'Full Stack Web Development', 'Popular Course', 38, '2026-05-09 09:05:03'),
(39, 'Artificial Intelligence AI', 'Popular Course', NULL, '2026-05-09 09:05:23'),
(40, 'Python Programming', 'Popular Course', 38, '2026-05-09 09:05:41'),
(41, 'Prompt Engineering', 'NEW', NULL, '2026-05-09 09:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `assessment_name` varchar(100) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `score_out_of_100` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `course_id`, `student_id`, `assessment_name`, `weight`, `score_out_of_100`) VALUES
(25, 38, 40, 'Mid Exam 1', 40, 30),
(26, 38, 40, 'Quize 1', 10, 10),
(27, 38, 40, 'Ass 1', 20, 19);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `part_number` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content_type` enum('pdf','video','text','quiz') DEFAULT NULL,
  `file_path_or_link` text DEFAULT NULL,
  `category` enum('material','assignment','quiz','exam') DEFAULT 'material'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `part_number`, `title`, `content_type`, `file_path_or_link`, `category`) VALUES
(24, 38, 1, 'Intro Full Stack', 'pdf', 'uploads/1778317833_Daily_Sales_Report_06-05-2026 (2).pdf', 'material'),
(25, 38, 1, 'Ass 1', 'pdf', 'uploads/1778317847_Report_Bilan_Ahmed_2026-05-06.pdf', 'assignment'),
(26, 38, 1, 'Quize 1', 'pdf', 'uploads/1778317862_Daily_Sales_Report_05-05-2026 (1).pdf', 'quiz');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `lesson_id`, `student_id`, `file_path`, `submitted_at`) VALUES
(6, 25, 40, 'uploads/submissions/1778318024_Daily_Sales_Report_06-05-2026 (3).pdf', '2026-05-09 09:13:44'),
(7, 26, 40, 'uploads/submissions/1778318041_Daily_Sales_Report_06-05-2026 (1).pdf', '2026-05-09 09:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student','manager') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  `course_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `gender`, `password`, `role`, `created_at`, `status`, `course_id`) VALUES
(1, 'Ahmed Bache', 'admin@lms.com', NULL, NULL, '$2y$10$qgnI3MbUCUnkbiWtzYPMB.NePsM4kIAe5eGBHNs1UQYPUvIZoE8je', 'admin', '2026-04-20 14:40:24', 1, NULL),
(38, 'Test Teacher', 'testt@gmail.com', '+25377054254', 'Male', '$2y$10$lIQMLblnfNGYijfhpgh9XeDmdYA.9TJ2kOwtn5rpaC3Ke2thPxq6q', 'teacher', '2026-05-09 09:06:51', 1, NULL),
(39, 'Test Manager', 'testm@gmail.com', '+25377054254', 'Female', '$2y$10$ULQWq6F/9tCK/UkRtYM1euirglejcLTis2lq9gVqYez3p9ma3U0Ia', 'manager', '2026-05-09 09:07:23', 1, NULL),
(40, 'Test Studnet', 'tests@lms.com', '+25377054254', 'Male', '$2y$10$WA4Pn1E1ug5Kkx2EWOzrO.iqUT1lTeVIdMvcynnGKhQ1jlpXJ/CxW', 'student', '2026-05-09 09:08:42', 1, '38,41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`course_id`,`student_id`,`attendance_date`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_serial` (`certificate_serial`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grade` (`course_id`,`student_id`,`assessment_name`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
