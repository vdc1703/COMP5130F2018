-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2018 at 02:48 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `img`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_album`
--

CREATE TABLE `tbl_album` (
  `album_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `album_title` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_album`
--

INSERT INTO `tbl_album` (`album_id`, `user_id`, `album_title`) VALUES
(11, 1, 'Admin-Album'),
(10, 3, 'Picture'),
(12, 1, 'Admin-Album2');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_img`
--

CREATE TABLE `tbl_img` (
  `img_id` int(25) NOT NULL,
  `img_name` varchar(30) NOT NULL DEFAULT '',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `album_id` int(25) NOT NULL DEFAULT '0',
  `img_title` varchar(35) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_img`
--

INSERT INTO `tbl_img` (`img_id`, `img_name`, `user_id`, `album_id`, `img_title`) VALUES
(59, 'img_5c0532c2963cb.jpg', 1, 11, '_DSC0035'),
(58, 'img_5c0532b9a9d82.jpg', 0, 0, '_DSC0063'),
(57, 'img_5c0532acb5bab.jpg', 1, 11, '_DSC0063'),
(56, 'img_5c0532972e0d5.jpg', 1, 11, '_DSC0063'),
(55, 'img_5c05328fa0456.jpg', 1, 12, '_DSC0063'),
(54, 'img_5c05328cdfc0c.jpg', 1, 12, '_DSC0045'),
(53, 'img_5c052f456ec79.jpg', 3, 10, '112');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int(25) NOT NULL,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `user_group` varchar(20) NOT NULL DEFAULT 'member'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `user_group`) VALUES
(1, 'admin', '202cb962ac59075b964b07152d234b70', 'admin'),
(2, 'vdc', '585c84f141f1d73f16d39dfac7286b2f', 'member'),
(3, 'chanqua', 'f8fb6e888ceeed19e19526fd46753459', 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_album`
--
ALTER TABLE `tbl_album`
  ADD PRIMARY KEY (`album_id`);

--
-- Indexes for table `tbl_img`
--
ALTER TABLE `tbl_img`
  ADD PRIMARY KEY (`img_id`),
  ADD UNIQUE KEY `img_name` (`img_name`) USING BTREE;

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_album`
--
ALTER TABLE `tbl_album`
  MODIFY `album_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_img`
--
ALTER TABLE `tbl_img`
  MODIFY `img_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
