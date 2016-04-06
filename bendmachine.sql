-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2016 at 12:20 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bendmachine`
--
CREATE DATABASE IF NOT EXISTS `bendmachine` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bendmachine`;

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE IF NOT EXISTS `machines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `machine` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `material` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `assortment` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `machine`, `material`, `assortment`, `quantity`) VALUES
(1, 'Станок №1 тестовый', 'Сталь 30', 'Круг 20', '360'),
(2, 'Станок №1 тестовый', 'Сталь 50', 'Круг 20', '444'),
(3, 'Станок №1 тестовый', 'Сталь 50', 'Тестовый сортамент', '333'),
(4, 'Станок другой тестовый', 'Сталь 30', 'Круг 20', '2000'),
(5, 'Станок другой тестовый', 'Сталь 50', 'Круг 20', '550'),
(6, 'Станок другой тестовый', 'Сталь 50', 'Шайба', '450');

-- --------------------------------------------------------

--
-- Table structure for table `machines_quantity`
--

CREATE TABLE IF NOT EXISTS `machines_quantity` (
  `machine` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `img` varchar(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`machine`,`quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `machines_quantity`
--

INSERT INTO `machines_quantity` (`machine`, `img`, `quantity`) VALUES
('Станок №1 тестовый', 'cm_25.jpg', '0'),
('Станок другой тестовый', 'sar.jpg', '0');

-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE IF NOT EXISTS `storage` (
  `assortment` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `material` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(10,0) NOT NULL DEFAULT '0',
  `critical_value` decimal(10,0) DEFAULT NULL,
  `unit` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastWithdraw` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assortment`,`material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `storage`
--

INSERT INTO `storage` (`assortment`, `material`, `quantity`, `critical_value`, `unit`, `updated`, `lastWithdraw`) VALUES
('Круг 20', 'Сталь 30', '0', NULL, 'мм.', '2015-12-25 17:37:31', '0'),
('Круг 20', 'Сталь 40', '0', NULL, 'мм.', '2015-12-25 12:39:00', '0'),
('Круг 20', 'Сталь 50', '23232', '111', 'мм.', '2015-11-26 16:25:11', '0'),
('Круг 20', 'Тестовый материал', '0', NULL, 'мм.', '2015-12-25 17:37:31', '0'),
('Ромб 40', 'Сталь 30', '12000', '5000', 'мм.', '2015-12-22 11:29:37', '0'),
('Тестовый сортамент', 'Сталь 30', '0', NULL, 'мм.', '2015-12-25 17:37:31', '0'),
('Тестовый сортамент', 'Сталь 50', '0', NULL, 'мм.', '2015-12-25 18:04:33', '0'),
('Шайба', 'Сталь 30', '7850', '5000', 'мм.', '2015-12-22 10:25:13', '0'),
('Шайба', 'Сталь 40', '0', NULL, 'мм.', '2015-12-25 17:46:43', '0'),
('Шайба', 'Сталь 50', '2000', '1500', 'мм.', '2015-11-26 16:24:07', '0');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
