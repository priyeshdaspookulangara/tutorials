-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2024 at 07:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `tutorial_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--
CREATE TABLE `topics` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--
INSERT INTO `topics` (`id`) VALUES (1), (2), (3), (4);

-- --------------------------------------------------------

--
-- Table structure for table `topic_translations`
--
CREATE TABLE `topic_translations` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `language` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topic_translations`
--
INSERT INTO `topic_translations` (`id`, `topic_id`, `language`, `name`, `description`) VALUES
(1, 1, 'en', 'PHP', 'A popular general-purpose scripting language that is especially suited to web development.'),
(2, 1, 'es', 'PHP', 'Un popular lenguaje de scripting de propósito general que se adapta especialmente al desarrollo web.'),
(3, 2, 'en', 'Python', 'An interpreted, high-level and general-purpose programming language.'),
(4, 2, 'es', 'Python', 'Un lenguaje de programación interpretado, de alto nivel y de propósito general.'),
(5, 3, 'en', 'Java', 'A high-level, class-based, object-oriented programming language.'),
(6, 3, 'es', 'Java', 'Un lenguaje de programación de alto nivel, basado en clases y orientado a objetos.'),
(7, 4, 'en', 'C++', 'A general-purpose programming language created by Bjarne Stroustrup.'),
(8, 4, 'es', 'C++', 'Un lenguaje de programación de propósito general creado por Bjarne Stroustrup.');

-- --------------------------------------------------------

--
-- Table structure for table `tutorials`
--
CREATE TABLE `tutorials` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorials`
--
INSERT INTO `tutorials` (`id`, `topic_id`, `is_premium`) VALUES
(1, 1, 0), (2, 1, 0), (3, 1, 1),
(4, 2, 0), (5, 2, 0), (6, 2, 1),
(7, 3, 0), (8, 3, 0), (9, 3, 1),
(10, 4, 0), (11, 4, 0), (12, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_translations`
--
CREATE TABLE `tutorial_translations` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `language` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorial_translations`
--
INSERT INTO `tutorial_translations` (`id`, `tutorial_id`, `language`, `title`) VALUES
(1, 1, 'en', 'PHP Variables'), (2, 1, 'es', 'Variables en PHP'),
(3, 2, 'en', 'PHP Arrays'), (4, 2, 'es', 'Arrays en PHP'),
(5, 3, 'en', 'PHP Functions'), (6, 3, 'es', 'Funciones en PHP'),
(7, 4, 'en', 'Python Variables'), (8, 4, 'es', 'Variables en Python'),
(9, 5, 'en', 'Python Lists'), (10, 5, 'es', 'Listas en Python'),
(11, 6, 'en', 'Python Functions'), (12, 6, 'es', 'Funciones en Python');

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_pages`
--
CREATE TABLE `tutorial_pages` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `page_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorial_pages`
--
INSERT INTO `tutorial_pages` (`id`, `tutorial_id`, `page_number`) VALUES
(1, 1, 1), (2, 1, 2), (3, 2, 1), (4, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_page_translations`
--
CREATE TABLE `tutorial_page_translations` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorial_page_translations`
--
INSERT INTO `tutorial_page_translations` (`id`, `page_id`, `language`, `title`, `content`) VALUES
(1, 1, 'en', 'Introduction to PHP Variables', 'This is the first page of the PHP variables tutorial.'),
(2, 1, 'es', 'Introducción a las Variables en PHP', 'Esta es la primera página del tutorial de variables en PHP.'),
(3, 2, 'en', 'PHP Variable Types', 'This is the second page of the PHP variables tutorial.'),
(4, 2, 'es', 'Tipos de Variables en PHP', 'Esta es la segunda página del tutorial de variables en PHP.'),
(5, 3, 'en', 'Introduction to PHP Arrays', 'This is the first page of the PHP arrays tutorial.'),
(6, 3, 'es', 'Introducción a los Arrays en PHP', 'Esta es la primera página del tutorial de arrays en PHP.');

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

ALTER TABLE `topics` ADD PRIMARY KEY (`id`);
ALTER TABLE `topic_translations` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `topic_lang_unique` (`topic_id`, `language`);
ALTER TABLE `tutorials` ADD PRIMARY KEY (`id`), ADD KEY `topic_id` (`topic_id`);
ALTER TABLE `tutorial_translations` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `tutorial_lang_unique` (`tutorial_id`, `language`);
ALTER TABLE `tutorial_pages` ADD PRIMARY KEY (`id`), ADD KEY `tutorial_id` (`tutorial_id`);
ALTER TABLE `tutorial_page_translations` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `page_lang_unique` (`page_id`, `language`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `topics` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `topic_translations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `tutorials` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `tutorial_translations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `tutorial_pages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `tutorial_page_translations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `topic_translations` ADD CONSTRAINT `topic_translations_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;
ALTER TABLE `tutorials` ADD CONSTRAINT `tutorials_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;
ALTER TABLE `tutorial_translations` ADD CONSTRAINT `tutorial_translations_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`) ON DELETE CASCADE;
ALTER TABLE `tutorial_pages` ADD CONSTRAINT `tutorial_pages_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`) ON DELETE CASCADE;
ALTER TABLE `tutorial_page_translations` ADD CONSTRAINT `tutorial_page_translations_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `tutorial_pages` (`id`) ON DELETE CASCADE;
COMMIT;
