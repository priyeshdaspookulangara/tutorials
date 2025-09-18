-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2024 at 05:20 AM
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
-- Database: `tutorial_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `premium_content`
--

CREATE TABLE `premium_content` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `name`, `description`) VALUES
(1, 'PHP', 'A popular general-purpose scripting language that is especially suited to web development.'),
(2, 'Python', 'An interpreted, high-level and general-purpose programming language.'),
(3, 'Java', 'A high-level, class-based, object-oriented programming language that is designed to have as few implementation dependencies as possible.'),
(4, 'C++', 'A general-purpose programming language created by Bjarne Stroustrup as an extension of the C programming language, or \"C with Classes\".');

-- --------------------------------------------------------

--
-- Table structure for table `tutorials`
--

CREATE TABLE `tutorials` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorials`
--

INSERT INTO `tutorials` (`id`, `topic_id`, `title`, `is_premium`) VALUES
(1, 1, 'PHP Variables', 0),
(2, 1, 'PHP Arrays', 0),
(3, 1, 'PHP Functions', 1),
(4, 2, 'Python Variables', 0),
(5, 2, 'Python Lists', 0),
(6, 2, 'Python Functions', 1),
(7, 3, 'Java Variables', 0),
(8, 3, 'Java Arrays', 0),
(9, 3, 'Java Methods', 1),
(10, 4, 'C++ Variables', 0),
(11, 4, 'C++ Arrays', 0),
(12, 4, 'C++ Functions', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_pages`
--

CREATE TABLE `tutorial_pages` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `page_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorial_pages`
--

INSERT INTO `tutorial_pages` (`id`, `tutorial_id`, `page_number`, `title`, `content`) VALUES
(1, 1, 1, 'Introduction to PHP Variables', 'This is the first page of the PHP variables tutorial.'),
(2, 1, 2, 'PHP Variable Types', 'This is the second page of the PHP variables tutorial.'),
(3, 2, 1, 'Introduction to PHP Arrays', 'This is the first page of the PHP arrays tutorial.'),
(4, 2, 2, 'PHP Indexed Arrays', 'This is the second page of the PHP arrays tutorial.'),
(5, 4, 1, 'Introduction to Python Variables', 'This is the first page of the Python variables tutorial.'),
(6, 4, 2, 'Python Variable Types', 'This is the second page of the Python variables tutorial.'),
(7, 5, 1, 'Introduction to Python Lists', 'This is the first page of the Python lists tutorial.'),
(8, 5, 2, 'Python List Methods', 'This is the second page of the Python lists tutorial.'),
(9, 7, 1, 'Introduction to Java Variables', 'This is the first page of the Java variables tutorial.'),
(10, 7, 2, 'Java Variable Types', 'This is the second page of the Java variables tutorial.'),
(11, 8, 1, 'Introduction to Java Arrays', 'This is the first page of the Java arrays tutorial.'),
(12, 8, 2, 'Java Array Methods', 'This is the second page of the Java arrays tutorial.'),
(13, 10, 1, 'Introduction to C++ Variables', 'This is the first page of the C++ variables tutorial.'),
(14, 10, 2, 'C++ Variable Types', 'This is the second page of the C++ variables tutorial.'),
(15, 11, 1, 'Introduction to C++ Arrays', 'This is the first page of the C++ arrays tutorial.'),
(16, 11, 2, 'C++ Array Methods', 'This is the second page of the C++ arrays tutorial.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `premium_content`
--
ALTER TABLE `premium_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tutorials`
--
ALTER TABLE `tutorials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `tutorial_pages`
--
ALTER TABLE `tutorial_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

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
-- AUTO_INCREMENT for table `premium_content`
--
ALTER TABLE `premium_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tutorials`
--
ALTER TABLE `tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tutorial_pages`
--
ALTER TABLE `tutorial_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `premium_content`
--
ALTER TABLE `premium_content`
  ADD CONSTRAINT `premium_content_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutorials`
--
ALTER TABLE `tutorials`
  ADD CONSTRAINT `tutorials_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutorial_pages`
--
ALTER TABLE `tutorial_pages`
  ADD CONSTRAINT `tutorial_pages_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
