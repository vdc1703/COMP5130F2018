-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 13, 2018 at 06:03 PM
-- Server version: 5.5.25a
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `img`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_album`
--

CREATE TABLE IF NOT EXISTS `tbl_album` (
  `album_id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `album_title` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_img`
--

CREATE TABLE IF NOT EXISTS `tbl_img` (
  `img_id` int(25) NOT NULL AUTO_INCREMENT,
  `img_name` varchar(30) NOT NULL DEFAULT '',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `album_id` int(25) NOT NULL DEFAULT '0',
  `img_title` varchar(35) NOT NULL DEFAULT '',
  PRIMARY KEY (`img_id`),
  UNIQUE KEY `filename` (`img_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_img`
--

INSERT INTO `tbl_img` (`img_id`, `img_name`, `user_id`, `album_id`, `img_title`) VALUES
(1, '88445650459907175425.png', 1, 0, 'moderne.classique.or...'),
(2, '884456504599017175425.png', 1, 0, 'moderne.classique.or...');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_session`
--

CREATE TABLE IF NOT EXISTS `tbl_session` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `session_start` int(10) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `user_id` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email_address` varchar(255) NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `register_time` int(10) NOT NULL DEFAULT '0',
  `user_group` varchar(20) NOT NULL DEFAULT 'normal_user',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `email_address`, `ip_address`, `register_time`, `user_group`) VALUES
(1, 'vdc', '098f6bcd4621d373cade4e832627b4f6', 'chuong_vu@student.uml.edu', '::1', 1540831431, 'admin');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
