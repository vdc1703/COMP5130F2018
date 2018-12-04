-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2018 at 03:29 AM
-- Server version: 5.5.52-MariaDB
-- PHP Version: 5.5.21

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
-- Table structure for table `tbl_album`
--

CREATE TABLE IF NOT EXISTS `tbl_album` (
  `album_id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `album_title` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `tbl_album`
--

INSERT INTO `tbl_album` (`album_id`, `user_id`, `album_title`) VALUES
(15, 2, 'test'),
(13, 1, 'Album1');

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
  UNIQUE KEY `img_name` (`img_name`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `tbl_img`
--

INSERT INTO `tbl_img` (`img_id`, `img_name`, `user_id`, `album_id`, `img_title`) VALUES
(1, 'img_5c05e255b68d2.jpg', 1, 13, '_DSC0045'),
(2, 'img_5c05e25612fa0.JPG', 1, 13, '_DSC0035'),
(3, 'img_5c05e25667f18.JPG', 1, 13, '_DSC0063'),
(4, 'img_5c05e25720a90.JPG', 1, 13, '_DSC2544'),
(5, 'img_5c05e25759455.JPG', 1, 13, '_DSC2545'),
(6, 'img_5c05e257a8162.JPG', 1, 13, '_DSC2555'),
(7, 'img_5c05e257eaf8c.JPG', 1, 13, '_DSC7351'),
(8, 'img_5c05e258946ab.JPG', 1, 13, '13'),
(9, 'img_5c05e2593fac1.JPG', 1, 13, '1,Before'),
(10, 'img_5c05e25a076c4.jpg', 1, 13, '111'),
(11, 'img_5c05e25a63d31.jpg', 1, 13, '112'),
(12, 'img_5c05e25ac2907.jpg', 1, 13, '116'),
(13, 'img_5c05e25b3c3d5.JPG', 1, 13, 'DSC_4560'),
(14, 'img_5c05e25b9e340.jpg', 1, 13, 'DSC_5110'),
(15, 'img_5c05e25c22dc3.jpg', 1, 13, 'DSC_5112'),
(16, 'img_5c05e29107c12.jpg', 1, 0, '01'),
(17, 'img_5c05e2915ee44.jpg', 1, 0, '02'),
(18, 'img_5c05e291b261d.jpg', 1, 0, '03'),
(19, 'img_5c05e292a7ec4.jpg', 1, 0, '04'),
(20, 'img_5c05e293ae27a.jpg', 1, 0, '05'),
(48, 'img_5c05ea6ccada2.jpg', 1, 0, '07'),
(23, 'img_5c05e3030bcb0.JPG', 2, 0, 'DSC_0001'),
(24, 'img_5c05e303b3c54.JPG', 2, 0, 'DSC_0004'),
(25, 'img_5c05e3048cf5e.JPG', 2, 0, 'DSC_0005'),
(26, 'img_5c05e304e87b4.JPG', 2, 0, 'DSC_0006'),
(27, 'img_5c05e30604953.JPG', 2, 0, 'DSC_0007'),
(28, 'img_5c05e306cca03.JPG', 2, 0, 'DSC_0008'),
(29, 'img_5c05e30782283.JPG', 2, 0, 'DSC_0009'),
(30, 'img_5c05e30843b86.JPG', 2, 0, 'DSC_0010'),
(31, 'img_5c05e30920e26.JPG', 2, 0, 'DSC_0011'),
(32, 'img_5c05e309da0b9.JPG', 2, 0, 'DSC_0012'),
(33, 'img_5c05e30aad452.JPG', 2, 0, 'DSC_0013'),
(34, 'img_5c05e30c67fac.JPG', 2, 0, 'DSC_0015'),
(35, 'img_5c05e30c9e9bf.JPG', 2, 0, 'DSC_0016'),
(36, 'img_5c05e417e4ea7.jpg', 3, 0, 'DSC_0051'),
(37, 'img_5c05e41853f21.jpg', 3, 0, 'DSC_0063'),
(38, 'img_5c05e418d1715.jpg', 3, 0, 'DSC_0066'),
(39, 'img_5c05e41955d65.jpg', 3, 0, 'DSC_0071'),
(40, 'img_5c05e419b53e8.jpg', 3, 0, 'DSC_0085'),
(41, 'img_5c05e41a1968d.jpg', 3, 0, 'DSC_0086'),
(42, 'img_5c05e41a78f32.jpg', 3, 0, 'DSC_0088'),
(43, 'img_5c05e41ab650a.jpg', 3, 0, 'DSC_0089'),
(44, 'img_5c05e41b43e15.jpg', 3, 0, 'DSC_0096'),
(45, 'img_5c05e41bdf060.jpg', 3, 0, 'DSC_0097'),
(46, 'img_5c05e41c4b3db.jpg', 3, 0, 'DSC_0098'),
(47, 'img_5c05e41cccfa1.jpg', 3, 0, 'DSC_0100'),
(49, 'img_5c05ea6d535ea.jpg', 1, 0, '06'),
(50, 'img_5c05f36bb084d.jpg', 4, 0, 'DSC_5558_00545'),
(51, 'img_5c05f36ca88ac.jpg', 4, 0, 'DSC_5555_00542'),
(52, 'img_5c05f36e2ac3a.jpg', 4, 0, 'DSC_5221_00220'),
(53, 'img_5c05f36f0491e.jpg', 4, 0, 'DSC_5230_00229'),
(54, 'img_5c05f37094e37.jpg', 4, 0, 'DSC_5253_00249'),
(55, 'img_5c05f37169af4.jpg', 4, 0, 'DSC_5256_00252'),
(56, 'img_5c05f372ec809.jpg', 4, 0, 'DSC_5258_00253'),
(57, 'img_5c05f3745dbbd.jpg', 4, 0, 'DSC_5265_00259'),
(58, 'img_5c05f3754c1bd.jpg', 4, 0, 'DSC_5268_00261'),
(59, 'img_5c05f376acf74.jpg', 4, 0, 'DSC_5269_00262');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `user_id` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `user_group` varchar(20) NOT NULL DEFAULT 'member',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `user_group`) VALUES
(1, 'admin', '202cb962ac59075b964b07152d234b70', 'admin'),
(2, 'chuong', '202cb962ac59075b964b07152d234b70', 'member'),
(3, 'vdc', '202cb962ac59075b964b07152d234b70', 'member'),
(4, 'test', '202cb962ac59075b964b07152d234b70', 'member');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
