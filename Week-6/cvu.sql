-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 30, 2018 at 07:08 PM
-- Server version: 5.5.52-MariaDB
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cvu`
--

-- --------------------------------------------------------

--
-- Table structure for table `vdc_admin_cache`
--

CREATE TABLE IF NOT EXISTS `vdc_admin_cache` (
  `cache_id` varchar(70) NOT NULL DEFAULT '',
  `cache_value` text NOT NULL,
  PRIMARY KEY (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_ban_filter`
--

CREATE TABLE IF NOT EXISTS `vdc_ban_filter` (
  `ban_id` int(25) NOT NULL AUTO_INCREMENT,
  `time_banned` int(10) NOT NULL DEFAULT '0',
  `ban_type` tinyint(1) NOT NULL DEFAULT '0',
  `ban_value` text NOT NULL,
  PRIMARY KEY (`ban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_file_logs`
--

CREATE TABLE IF NOT EXISTS `vdc_file_logs` (
  `log_id` int(25) NOT NULL AUTO_INCREMENT,
  `filename` varchar(30) NOT NULL DEFAULT '',
  `filesize` int(20) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `user_agent` varchar(255) NOT NULL,
  `time_uploaded` int(10) NOT NULL DEFAULT '0',
  `gallery_id` int(32) NOT NULL DEFAULT '0',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `original_filename` varchar(255) NOT NULL DEFAULT '',
  `upload_type` varchar(6) NOT NULL DEFAULT 'normal',
  `bandwidth` int(50) NOT NULL DEFAULT '0',
  `image_views` int(32) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vdc_file_logs`
--

INSERT INTO `vdc_file_logs` (`log_id`, `filename`, `filesize`, `ip_address`, `user_agent`, `time_uploaded`, `gallery_id`, `is_private`, `original_filename`, `upload_type`, `bandwidth`, `image_views`) VALUES
(1, '88445650459907175425.png', 490674, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36', 1540832160, 1, 0, 'moderne.classique.original.png', 'normal', 1962696, 4),
(2, '88312270253085597383.jpg', 51807, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36', 1540911446, 1, 0, 'myellipse-mix.jpg', 'normal', 103614, 2);

-- --------------------------------------------------------

--
-- Table structure for table `vdc_file_ratings`
--

CREATE TABLE IF NOT EXISTS `vdc_file_ratings` (
  `rating_id` int(25) NOT NULL AUTO_INCREMENT,
  `filename` varchar(30) NOT NULL DEFAULT '',
  `total_rating` int(5) NOT NULL DEFAULT '5',
  `total_votes` int(30) NOT NULL DEFAULT '1',
  `voted_by` longtext NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `gallery_id` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vdc_file_ratings`
--

INSERT INTO `vdc_file_ratings` (`rating_id`, `filename`, `total_rating`, `total_votes`, `voted_by`, `is_private`, `gallery_id`) VALUES
(1, '88445650459907175425.png', 0, 0, '', 0, 1),
(2, '88312270253085597383.jpg', 0, 0, '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vdc_file_storage`
--

CREATE TABLE IF NOT EXISTS `vdc_file_storage` (
  `file_id` int(25) NOT NULL AUTO_INCREMENT,
  `filename` varchar(30) NOT NULL DEFAULT '',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `gallery_id` int(25) NOT NULL DEFAULT '0',
  `album_id` int(25) NOT NULL DEFAULT '0',
  `file_title` varchar(35) NOT NULL DEFAULT '',
  `viewer_clicks` int(25) NOT NULL DEFAULT '1',
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vdc_file_storage`
--

INSERT INTO `vdc_file_storage` (`file_id`, `filename`, `is_private`, `gallery_id`, `album_id`, `file_title`, `viewer_clicks`) VALUES
(1, '88445650459907175425.png', 0, 1, 0, 'moderne.classique.or...', 1),
(2, '88312270253085597383.jpg', 0, 1, 0, 'myellipse-mix.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vdc_gallery_albums`
--

CREATE TABLE IF NOT EXISTS `vdc_gallery_albums` (
  `album_id` int(25) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(25) NOT NULL DEFAULT '0',
  `album_title` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_robot_info`
--

CREATE TABLE IF NOT EXISTS `vdc_robot_info` (
  `robot_id` int(25) NOT NULL AUTO_INCREMENT,
  `preg_match` varchar(255) NOT NULL,
  `robot_name` varchar(100) NOT NULL,
  PRIMARY KEY (`robot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_robot_logs`
--

CREATE TABLE IF NOT EXISTS `vdc_robot_logs` (
  `log_id` int(25) NOT NULL AUTO_INCREMENT,
  `robot_id` int(25) NOT NULL DEFAULT '0',
  `page_indexed` tinytext NOT NULL,
  `time_indexed` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `user_agent` varchar(255) NOT NULL,
  `http_referer` tinytext NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_site_cache`
--

CREATE TABLE IF NOT EXISTS `vdc_site_cache` (
  `cache_id` varchar(70) NOT NULL DEFAULT '',
  `cache_value` text NOT NULL,
  PRIMARY KEY (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vdc_site_cache`
--

INSERT INTO `vdc_site_cache` (`cache_id`, `cache_value`) VALUES
('page_views', '639');

-- --------------------------------------------------------

--
-- Table structure for table `vdc_site_settings`
--

CREATE TABLE IF NOT EXISTS `vdc_site_settings` (
  `config_key` varchar(70) NOT NULL DEFAULT '',
  `config_value` text NOT NULL,
  PRIMARY KEY (`config_key`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vdc_site_settings`
--

INSERT INTO `vdc_site_settings` (`config_key`, `config_value`) VALUES
('max_results', '20'),
('proxy_images', '1'),
('gallery_viewing', '1'),
('thumbnail_type', 'png'),
('upload_path', 'images/'),
('thumbnail_width', '160'),
('useronly_uploading', '0'),
('max_filesize', '1075000'),
('thumbnail_height', '160'),
('uploading_disabled', '0'),
('advanced_thumbnails', '0'),
('site_name', 'VDC'),
('registration_disabled', '0'),
('max_bandwidth', '2147483648'),
('user_max_filesize', '3145728'),
('date_format', 'F j, Y, g:i:s A'),
('google_analytics', ''),
('user_max_bandwidth', '10737418240'),
('file_extensions', 'jpeg,jpg,gif,png'),
('server_license', '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=http://ww6.mihalism.net" ><META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE,NO_STORE"><META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">\n			<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT"></head><body><script language=''javascript'' type=''text/javascript''>try\n				{\n				    var rurl = ''http://ww6.mihalism.net'';\n					window.top.location.replace(rurl);\n				} catch(exception) {\n					document.write("This page has moved, <A HREF=''http://ww6.mihalism.net?jserror=1''>Click here</A> to go there.");\n				}</script><noscript>This page has moved, <A HREF=''http://ww6.mihalism.net?noscript=1''>Click here</A> to go there.</noscript></body></html>'),
('user_file_extensions', 'jpeg,jpg,gif,png,ico'),
('recaptcha_public', ''),
('recaptcha_private', ''),
('email_in', 'chuong_vu@student.uml.edu'),
('email_out', 'chuong_vu@student.uml.edu');

-- --------------------------------------------------------

--
-- Table structure for table `vdc_user_info`
--

CREATE TABLE IF NOT EXISTS `vdc_user_info` (
  `user_id` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email_address` varchar(255) NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `private_gallery` tinyint(1) NOT NULL DEFAULT '0',
  `time_joined` int(10) NOT NULL DEFAULT '0',
  `user_group` varchar(20) NOT NULL DEFAULT 'normal_user',
  `upload_type` varchar(8) NOT NULL DEFAULT 'standard',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `vdc_user_info`
--

INSERT INTO `vdc_user_info` (`user_id`, `username`, `password`, `email_address`, `ip_address`, `private_gallery`, `time_joined`, `user_group`, `upload_type`) VALUES
(1, 'vdc', '098f6bcd4621d373cade4e832627b4f6', 'chuong_vu@student.uml.edu', '96.237.195.130', 0, 1540831431, 'root_admin', 'boxed');

-- --------------------------------------------------------

--
-- Table structure for table `vdc_user_passwords`
--

CREATE TABLE IF NOT EXISTS `vdc_user_passwords` (
  `password_id` int(25) NOT NULL AUTO_INCREMENT,
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `new_password` varchar(32) NOT NULL DEFAULT '',
  `time_requested` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`password_id`),
  UNIQUE KEY `password` (`new_password`),
  UNIQUE KEY `auth_key` (`auth_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `vdc_user_sessions`
--

CREATE TABLE IF NOT EXISTS `vdc_user_sessions` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `session_start` int(10) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vdc_user_sessions`
--

INSERT INTO `vdc_user_sessions` (`session_id`, `session_start`, `user_id`, `ip_address`, `user_agent`) VALUES
('113696495a42b0281bfb02fdd7386d28', 1540832089, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'),
('bead10d8820b51c91f020d9dd3a5a553', 1540834322, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'),
('98411abb6c8820e38145e59ad76c1eb6', 1540903392, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'),
('f31c6cdbd7ee5fe0d3540b550f1623bf', 1540904227, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'),
('c9c6772485cd32df6ff08d3f585a303a', 1540925056, 1, '96.237.195.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
