-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 27, 2019 at 09:49 PM
-- Server version: 5.7.24
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arte_tech_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hourly_rate` double NOT NULL,
  `transport_cost` double NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `user_id`, `company_name`, `hourly_rate`, `transport_cost`, `telephone`) VALUES
(2, 15, 'Nawang Production', 15, 1.5, '0488 010 685'),
(4, 21, 'In The Pocket', 22, 0.55555, '0488010685'),
(5, 27, 'Very Nice Company', 10, 2, '0114 5494 '),
(6, 29, 'CC', 0, 0, '00');

-- --------------------------------------------------------

--
-- Table structure for table `complain`
--

CREATE TABLE `complain` (
  `id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_rate`
--

CREATE TABLE `freelancer_rate` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hour_rate` double NOT NULL,
  `transport_cost` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `freelancer_rate`
--

INSERT INTO `freelancer_rate` (`id`, `user_id`, `hour_rate`, `transport_cost`) VALUES
(1, 25, 12.4, 2.5),
(2, 26, 13, 3),
(3, 31, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20191205001227', '2019-12-05 00:12:31'),
('20191206235303', '2019-12-06 23:53:25'),
('20191207151822', '2019-12-07 15:18:28'),
('20191207183917', '2019-12-07 18:39:23'),
('20191208205532', '2019-12-08 20:56:15'),
('20191210224240', '2019-12-10 22:42:55'),
('20191213131738', '2019-12-13 13:17:45'),
('20191213134326', '2019-12-13 13:43:36'),
('20191213134415', '2019-12-13 13:44:20'),
('20191213140247', '2019-12-13 14:02:53'),
('20191214140627', '2019-12-17 10:40:25'),
('20191218105434', '2019-12-18 10:55:04'),
('20191225223858', '2019-12-25 22:39:37'),
('20191226102638', '2019-12-26 10:26:46'),
('20191226211238', '2019-12-26 21:13:08'),
('20191227214641', '2019-12-27 21:47:21');

-- --------------------------------------------------------

--
-- Table structure for table `period`
--

CREATE TABLE `period` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_confirm` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `period`
--

INSERT INTO `period` (`id`, `client_id`, `start_date`, `end_date`, `is_confirm`) VALUES
(1, 2, '2019-12-01', '2019-12-12', 1),
(8, 2, '2019-12-11', '2019-12-20', 1),
(16, 4, '2019-12-23', '2019-12-31', 0),
(17, 2, '2019-12-23', '2019-12-30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `salary_type`
--

CREATE TABLE `salary_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bonus_rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_type`
--

INSERT INTO `salary_type` (`id`, `type_name`, `bonus_rate`) VALUES
(1, 'SATURDAY', 1.5),
(2, 'SUNDAY', 2),
(3, 'WEEKDAY', 1.2);

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `used` longtext COLLATE utf8mb4_unicode_ci,
  `transport_km` double DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `total_hours` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_cost` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `user_id`, `client_id`, `date`, `start_time`, `end_time`, `description`, `used`, `transport_km`, `period_id`, `total_hours`, `total_cost`) VALUES
(1, 20, 2, '2019-12-10', '18:00:00', '22:00:00', 'Task Edit Updated Check------- \r\nTask Edit Updated Check------- \r\n', 'Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe Brain.exe ', 50.45, 1, '04:00', 135.675),
(3, 20, 4, '2019-12-17', '14:00:00', '20:00:00', 'Updated Hahaa', 'ttttttttttttttttttttttttttttttt', 999, 8, '06:00', 686.99445),
(4, 20, 2, '2019-12-19', '04:44:00', '08:00:00', 'Router unplugged and plugged in again. FIXED', 'Hand', 0.1, 8, '03:16', 49.15),
(5, 25, 2, '2019-12-27', '03:45:00', '16:55:00', 'laaaaaaaaaaaaa', 'sddddd', 999, 17, '13:10', 1516),
(6, 25, 4, '2019-12-24', '10:00:00', '19:00:00', 'Hello Chooooo', 'Brain.exe', 999, 16, '09:00', 752.99445),
(7, 25, 2, '2019-12-25', '07:01:00', '16:01:00', 'Desctsdklndslkfnsd ', 'sadsa dsa d', 223, 17, '09:00', 469.5),
(8, 25, 2, '2019-12-28', '07:00:00', '16:00:00', 'zxcxzcxzcxzccxvcrewr', 'sdfdsf', 324, 17, '09:00', 621),
(9, 25, 4, '2019-12-27', '09:00:00', '17:00:00', 'Fixed tv', 'Brain', 2, 16, '08:00', 177.1111),
(10, 25, 5, '2019-12-31', '09:06:00', '16:54:00', 'Just Walking', 'Dead', 5, NULL, '07:48', 88);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `api_token`, `nickname`) VALUES
(15, 'd@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$LjFZa2t2YmovUGV3dEh5cQ$XP6PbayksAQFfmDE2LvQlWoaq1IMmT4ZaOGG+8m5QQM', NULL, 'Nawang Tendar'),
(20, '0007@gmail.com', '[\"ROLE_EMPLOYEE\"]', '$argon2id$v=19$m=65536,t=4,p=1$dEdFblg5M1ZUakQzQVVxNg$mjRxREl1CXcduHrCFQ0pdRj1n52iB+rqQbxOffu/6vU', NULL, 'James Bond'),
(21, 'itp@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$L0xjOXN5Z3ZybTVsaHdBSg$bUiK3R+MFXuyAIWBinOuFrDqPUMCWkZvcEompt7xPm0', NULL, 'Laura'),
(22, 'nn@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$cE1LU0JOaG9YQS9KR1p2Ug$MUE6gLPfIxOj9QAjGATIK0u856j/8H5B4nv/b9Qtj1Q', NULL, 'Na Naaa'),
(24, 'a@gmail.com', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$UDFGTWhkanZCRzN3V283Tg$M/IiiLEDe1gqOdop/gJIj3c+n+SpxZwKpNjHH/ZjKLo', NULL, 'Wicked Man'),
(25, 'freelancer@gmail.com', '[\"ROLE_FREELANCER\"]', '$argon2id$v=19$m=65536,t=4,p=1$Zlp6U3pWSjN3eDBlNHFrUg$UG/uTWHy2ll3zRqDOLf/zG/60JgkUdZOBfkvtLLlFzQ', NULL, 'Nawang'),
(26, 'freelaa@gmail.com', '[\"ROLE_FREELANCER\"]', '$argon2id$v=19$m=65536,t=4,p=1$R2RTRkFRd3JCaDBkUThwNA$OmLamHsbHBDMZTiHkVuoSA/ME/xXuKu30mNbn+je7KU', NULL, 'John De Freelance'),
(27, 'c@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$aG5Qc2VuSVFPWVNaT015aA$vJ81oMYCm7AVmPe65vXNwltOPMpHrmSJtxVktuY83W0', NULL, 'Nicola Tesla'),
(29, 'cc@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$NU8wNWRVai9JSEJSYy9xUg$qAsP3fd/ycCXyxZvonNCVfQ9+7nm9Ci9DpKvnGtTAQs', NULL, 'CoCo'),
(30, 'e@gmail.com', '[\"ROLE_EMPLOYEE\"]', '$argon2id$v=19$m=65536,t=4,p=1$cm91SVhEeWsuUjVwMEVYTQ$UMyduanEsu7nxkJpO3N3Ala1of6EFelcgMSsPQNNM+E', NULL, 'John Wick'),
(31, 'nf@gmail.com', '[\"ROLE_FREELANCER\"]', '$argon2id$v=19$m=65536,t=4,p=1$MTdvR2ZJdmVRZjg3anl2Yg$MPtDU6OGjWlcGkQ9NWqsTk2i5AI/yZ9aF3uCEOPR8Sc', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C7440455A76ED395` (`user_id`);

--
-- Indexes for table `complain`
--
ALTER TABLE `complain`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_2DD0CD6BEC8B7ADE` (`period_id`);

--
-- Indexes for table `freelancer_rate`
--
ALTER TABLE `freelancer_rate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6A9A7623A76ED395` (`user_id`);

--
-- Indexes for table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `period`
--
ALTER TABLE `period`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C5B81ECE19EB6921` (`client_id`);

--
-- Indexes for table `salary_type`
--
ALTER TABLE `salary_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_527EDB25A76ED395` (`user_id`),
  ADD KEY `IDX_527EDB2519EB6921` (`client_id`),
  ADD KEY `IDX_527EDB25EC8B7ADE` (`period_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D6497BA2F5EB` (`api_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `complain`
--
ALTER TABLE `complain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `freelancer_rate`
--
ALTER TABLE `freelancer_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `period`
--
ALTER TABLE `period`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `salary_type`
--
ALTER TABLE `salary_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C7440455A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `complain`
--
ALTER TABLE `complain`
  ADD CONSTRAINT `FK_2DD0CD6BEC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `period` (`id`);

--
-- Constraints for table `freelancer_rate`
--
ALTER TABLE `freelancer_rate`
  ADD CONSTRAINT `FK_6A9A7623A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `period`
--
ALTER TABLE `period`
  ADD CONSTRAINT `FK_C5B81ECE19EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`);

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_527EDB2519EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_527EDB25A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_527EDB25EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `period` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
