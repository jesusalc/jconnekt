-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 17, 2009 at 07:57 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `gsoc_joomla`
--

-- --------------------------------------------------------

--
-- Table structure for table `jos_jc_exApps`
--

CREATE TABLE IF NOT EXISTS `jos_jc_exApps` (
  `secretKey` varchar(25) NOT NULL,
  `host` varchar(50) NOT NULL,
  `path` varchar(50) NOT NULL,
  `port` varchar(50) NOT NULL,
  `appID` int(11) NOT NULL AUTO_INCREMENT,
  `appName` varchar(50) NOT NULL,
  PRIMARY KEY (`appID`),
  UNIQUE KEY `appName` (`appName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `jos_jc_exApps`
--

INSERT INTO `jos_jc_exApps` (`secretKey`, `host`, `path`, `port`, `appID`, `appName`) VALUES
('KOeB%D4eOGJ4E9EGybD+O*)1N', 'localhost', '/jconnect/exApps/fake/server.php', '80', 1, 'fakeApp');

-- --------------------------------------------------------

--
-- Table structure for table `jos_jc_meta`
--

CREATE TABLE IF NOT EXISTS `jos_jc_meta` (
  `appID` int(11) NOT NULL,
  `metakey` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`appID`,`metakey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jos_jc_meta`
--

INSERT INTO `jos_jc_meta` (`appID`, `metakey`, `value`) VALUES
(1, 'username_conflict', 'update'),
(1, 'recursive_insert', 'allow'),
(1, 'recursive_delete', 'deny'),
(12, 'recursive_insert', 'deny');

-- --------------------------------------------------------

--
-- Table structure for table `jos_jc_syncUsers`
--

CREATE TABLE IF NOT EXISTS `jos_jc_syncUsers` (
  `needSync` int(3) NOT NULL DEFAULT '0',
  `appID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `needSyncWithExApp` int(3) NOT NULL,
  PRIMARY KEY (`appID`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jos_jc_syncUsers`
--

INSERT INTO `jos_jc_syncUsers` (`needSync`, `appID`, `username`, `needSyncWithExApp`) VALUES
(0, 1, 'aa', 0),
(0, 0, 'admin', 0),
(0, 1, 'bb', 0);

