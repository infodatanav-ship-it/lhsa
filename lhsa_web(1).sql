-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 12:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lhsa_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(5) UNSIGNED NOT NULL,
  `user_id` int(5) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `parent_id` int(5) UNSIGNED DEFAULT NULL,
  `is_folder` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `filename`, `stored_name`, `size`, `uploaded_at`, `parent_id`, `is_folder`) VALUES
(2, 2, '20210116_160032.jpg', 'c28e5199f1e14e9b7e9e08eaf2410171.jpg', 4792531, '2025-09-11 13:02:00', NULL, 0),
(5, 1, 'Account-documents.zip', '3b911db9c9c8e5bce692cfbde246401c.zip', 80296, '2025-09-11 23:11:54', NULL, 0),
(10, 4, 'AmurTiger_FACTS_Biology.pdf', 'c7766f5c40fd909d757909c3e9c8e823.pdf', 558982, '2025-09-15 16:12:55', NULL, 0),
(31, 1, 'AnotherFold', '', 0, '2025-09-22 15:32:03', NULL, 1),
(32, 1, 'MyFolder', '', 0, '2025-09-22 15:32:19', NULL, 1),
(37, 1, '1686314163868.jpeg', '978658357ef9b6fede118d8c7077463f.jpeg', 107003, '2025-09-22 16:17:12', NULL, 0),
(38, 1, '1686638491817.jpeg', 'f9fbd707b1cc4cd00e678c009a0603b3.jpeg', 291760, '2025-09-22 16:17:24', NULL, 0),
(39, 1, 'DeskTop_01.txt', 'b9e09568cf7a8dc28be4492932c6fe49.txt', 104, '2025-09-22 16:58:26', NULL, 0),
(40, 1, 'Hichiriki.png', '87cd45bf5a6280cb59ab91101e090da5.png', 142862, '2025-09-22 16:59:05', NULL, 0),
(41, 1, 'map-japan.png', 'ff496327e717f5f5921ea5416effdf2c.png', 123250, '2025-09-22 17:00:53', NULL, 0),
(42, 1, 'Login_Signup_Logout.png', '40c46d3ebf953c33e43ea33149d58b19.png', 250039, '2025-09-22 17:02:57', 31, 0),
(44, 1, 'tdc_logo-resize.png', '46a61565185531687490f33564ec9a26.png', 74629, '2025-09-22 17:04:40', 32, 0);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('file','directory') NOT NULL,
  `size` bigint(20) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `name`, `type`, `size`, `parent_id`, `file_path`, `created_at`) VALUES
(1, 'Documents', 'directory', 0, NULL, '/Documents', '2025-09-22 17:40:42'),
(2, 'Images', 'directory', 0, NULL, '/Images', '2025-09-22 17:40:42'),
(3, 'Projects', 'directory', 0, NULL, '/Projects', '2025-09-22 17:40:42'),
(4, 'resume.pdf', 'file', 2048576, 1, '/Documents/resume.pdf', '2025-09-22 17:40:42'),
(5, 'notes.txt', 'file', 10240, 1, '/Documents/notes.txt', '2025-09-22 17:40:42'),
(6, 'Vacation', 'directory', 0, 2, '/Images/Vacation', '2025-09-22 17:40:42'),
(7, 'profile.jpg', 'file', 1048576, 2, '/Images/profile.jpg', '2025-09-22 17:40:42'),
(8, 'beach.png', 'file', 2097152, 6, '/Images/Vacation/beach.png', '2025-09-22 17:40:42'),
(9, 'Web App', 'directory', 0, 3, '/Projects/Web App', '2025-09-22 17:40:42'),
(10, 'script.js', 'file', 51200, 9, '/Projects/Web App/script.js', '2025-09-22 17:40:42'),
(11, 'Reunion', 'directory', 0, 1, '/Documents/Reunion', '2025-10-02 17:53:55'),
(12, 'datanav-portfolio.pdf', 'file', 293000, 1, 'Documents', '2025-10-03 18:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(5) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created_at`) VALUES
(1, 'Admins', '2025-09-15 13:32:47'),
(3, '1991', '2025-09-15 13:49:36'),
(4, '1952', '2025-09-15 16:06:26'),
(5, '1953', '2025-09-15 16:06:45'),
(6, '1951', '2025-09-15 16:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

CREATE TABLE `group_users` (
  `group_id` int(5) UNSIGNED NOT NULL,
  `user_id` int(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_users`
--

INSERT INTO `group_users` (`group_id`, `user_id`) VALUES
(3, 2),
(6, 1),
(6, 2),
(6, 4),
(6, 14);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`) VALUES
(12, 'dashboard.view'),
(7, 'docs.delete'),
(6, 'docs.upload'),
(5, 'docs.view'),
(9, 'groups.create'),
(11, 'groups.delete'),
(10, 'groups.edit'),
(8, 'groups.view'),
(2, 'users.create'),
(4, 'users.delete'),
(3, 'users.edit'),
(1, 'users.view');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role` enum('admin','user') NOT NULL,
  `permission_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role`, `permission_id`) VALUES
('admin', 1),
('admin', 2),
('admin', 3),
('admin', 4),
('admin', 5),
('admin', 6),
('admin', 7),
('admin', 8),
('admin', 9),
('admin', 10),
('admin', 11),
('user', 5),
('user', 6),
('user', 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'bpaulse', 'bevanpaulse@gmail.com', '$2y$12$GStFa7dnA9uLY7SpyXDE.ujboSQg4kJT3lMZN/fT/og8lyjstWJTK', 'admin', '2025-09-11 12:55:02'),
(2, 'ginger', 'ccoctober@gmail.com', '$2y$12$DD4uTYatrhaV7CKoVlxQf.xrZ59z5xQinrrnptuxzYFeN9PHiO7eS', 'user', '2025-09-11 12:58:00'),
(3, 'ebpaulse', 'elibaileypaulse@gmail.com', '$2y$12$xSbkTjf7eIiMbJpC7F1KSOjR5fJZI1kGZD5q0huMk7J0hs22kBV1q', 'user', '2025-09-11 22:36:14'),
(4, 'samos', 'stanamos@gmail.com', '$2y$12$PIzTt7RfS3HvaWcLYk3./OVwFUFvbbAdTvGD0hsSYwya9hcr.kF1S', 'admin', '2025-09-15 15:58:00'),
(5, 'kwilliams', 'keith@gmail.com', '$2y$12$kKboM.8u4vokw5elCuSLre.mk4ATWyQEeECr2ws7mFjkC4rSCYTFO', 'admin', '2025-09-16 13:21:40'),
(6, 'user1', 'user1@gmail.com', '$2y$12$lKBmrwKM0FSF/QC9HWBKmu54KtLRxBK9vFa49gZSkYado1X3.UKM2', 'user', '2025-09-16 13:29:51'),
(7, 'user2', 'user2@gmail.com', '$2y$12$.qKqXKf3LKJH1MEz3wRpOuPaPfv98aicaHQQo0xKAsfJWadBir7VW', 'user', '2025-09-16 13:30:10'),
(8, 'user3', 'user3@gmail.com', '$2y$12$Zj1SwPRcCoh3Vc75xOD5b.WmCeJpjx5.Pg2r3vXQwY0e5nV1cTmi2', 'user', '2025-09-16 13:30:30'),
(9, 'user4', 'user4@gmail.com', '$2y$12$2iLgTdj1P3sYqlr0kaZ/POSNURvBUeZmRkM6fBKjofiiFlXSL02wG', 'user', '2025-09-16 13:30:54'),
(10, 'user5', 'user5@gmail.com', '$2y$12$0RjLBqacggm5PHZkOY/TVeOPOpbaKGP1IXFTh2rxgwqSOpmakj9q6', 'user', '2025-09-16 13:31:32'),
(11, 'user6', 'user6@gmail.com', '$2y$12$sPP8oNy7mVPFvGbDeUZVmu5XdjP6CnCpxB7UxVkvbKif/cr6EkLoe', 'user', '2025-09-16 13:31:50'),
(12, 'user7', 'user7@gmail.com', '$2y$12$2fMfbalvhIK6K506TjdUEO54iidxnxqxoag4iZ8SzkZqxlexLnnAy', 'user', '2025-09-16 13:32:53'),
(13, 'user8', 'user8@gmail.com', '$2y$12$iI0b9Dz0HQsX8BEI5LyuteXamZ208Z2nzJD52RGzDZpv2S7Apdy/u', 'user', '2025-09-16 13:33:12'),
(14, 'bevanpq', 'bevan@datanav.co.za', '$2y$12$YngU209ixM49MpcZdaqLmOiotVwOlqlw3RHlSZtXrGvteo8qGi0Ii', 'user', '2025-09-29 11:05:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `group_users`
--
ALTER TABLE `group_users`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `files` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_users`
--
ALTER TABLE `group_users`
  ADD CONSTRAINT `group_users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
