-- phpMyAdmin SQL Dump
-- version 4.2.0-dev
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2014 at 06:15 AM
-- Server version: 5.5.34-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `insight`
--

-- --------------------------------------------------------

--
-- Table structure for table `commits`
--

CREATE TABLE IF NOT EXISTS `commits` (
  `hash` varchar(40) NOT NULL,
  `message` text NOT NULL,
  `additions` int(10) NOT NULL,
  `deletions` int(10) NOT NULL,
  `files_affected` int(6) NOT NULL,
  `timestamp` varchar(15) NOT NULL,
  `author` varchar(256) NOT NULL,
  `identifier` varchar(20) NOT NULL,
  UNIQUE KEY `id` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Commit Details';

-- --------------------------------------------------------

--
-- Table structure for table `language_stats`
--

CREATE TABLE IF NOT EXISTS `language_stats` (
  `project_id` int(10) NOT NULL,
  `language` varchar(20) NOT NULL,
  `percentage` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Language Stats';

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `mail` varchar(256) NOT NULL,
  `github_id` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `year` int(1) NOT NULL,
  `since` datetime NOT NULL,
  `group` varchar(10) NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Member details' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `homepage` varchar(256) NOT NULL,
  `identifier` varchar(20) NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Project Details' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
