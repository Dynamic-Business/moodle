-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 15, 2011 at 03:01 PM
-- Server version: 5.1.50
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `moodle2demo`
--

-- Part 1 -------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `mdl_dynamic_courses_groups`
--

CREATE TABLE IF NOT EXISTS `mdl_dynamic_courses_groups` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_id` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_id_3` (`course_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mdl_dynamic_courses_groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `mdl_dynamic_group`
-- Updated 23-05-12 with grouptype for version 8.

CREATE TABLE IF NOT EXISTS `mdl_dynamic_group` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `grouptype` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'and' NOT NULL, 
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mdl_dynamic_group`
--


-- --------------------------------------------------------

--
-- Table structure for table `mdl_dynamic_managers_group`
--

CREATE TABLE IF NOT EXISTS `mdl_dynamic_managers_group` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` int(10) NOT NULL,
  `groupid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`,`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mdl_dynamic_managers_group`
--


-- --------------------------------------------------------

-- REMOVED FOR v8.0 onwards. Now reads mdl_dynamic_groupdata but can't be included here asphp is needed to read the fields in the config file

-- Table structure for table `mdl_dynamic_propertiesforgroup`
--

/*CREATE TABLE IF NOT EXISTS `mdl_dynamic_propertiesforgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(10) NOT NULL,
  `propertyname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `propertyvalue` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;*/

--
-- Dumping data for table `mdl_dynamic_propertiesforgroup`
--


-- --------------------------------------------------------

--
-- Table structure for table `mdl_dynamic_role_permissions`
--

CREATE TABLE IF NOT EXISTS `mdl_dynamic_role_permissions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `mdl_dynamic_role_permissions`
--

INSERT INTO `mdl_dynamic_role_permissions` (`id`, `role`, `permissions`) VALUES
(1, 'manager', 'addcourses|viewusers'),
(2, 'admin', 'editgroup|definegroup|addcourses|viewusers|assignmanager|deletegroup|creategroup|rebuildtables');

-- --------------------------------------------------------

-- Amend data field in mdl_user_info_data

ALTER TABLE `mdl_user_info_data` CHANGE COLUMN `data` `data` VARCHAR(150) NOT NULL  ;


-- Part 2 - run after rebuild tables ----------------------------------------------------------------------

-- Stand-in structure for view `mdl_dynamic_viewcombined`
--

CREATE TABLE IF NOT EXISTS 'mdl_dynamic_viewuserscourses' (
`user_id` varchar(255)
,`course_id` varchar(100)
);

-----------------------------------------------------------

DROP TABLE IF EXISTS `mdl_dynamic_viewuserscourses`;

CREATE ALGORITHM=TEMPTABLE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW mdl_dynamic_viewuserscourses AS SELECT u.idnumber AS 'user_id',cg.course_id FROM mdl_dynamic_usersgroups ug INNER JOIN mdl_dynamic_courses_groups cg ON cg.group_id = ug.groupid INNER JOIN mdl_user u ON u.id = ug.userid WHERE u.idnumber != '' AND u.deleted != 1;


