CREATE TABLE IF NOT EXISTS `#__jc_exApps` (
  `secretKey` varchar(64) NOT NULL,
  `host` varchar(50) NOT NULL,
  `path` varchar(50) NOT NULL,
  `port` varchar(50) NOT NULL,
  `appID` int(11) NOT NULL AUTO_INCREMENT,
  `appName` varchar(50) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`appID`),
  UNIQUE KEY `appName` (`appName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;



CREATE TABLE IF NOT EXISTS `#__jc_externalUsers` (
  `JID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ownerAppID` int(11) NOT NULL,
  `needSync` int(11) NOT NULL,
  PRIMARY KEY (`JID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `#__jc_meta` (
  `appID` int(11) NOT NULL,
  `metakey` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`appID`,`metakey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__jc_syncUsers` (
  `JID` int(3) NOT NULL DEFAULT '0',
  `appID` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`JID`,`appID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__jc_assoc_table` (
  `server_url` varchar(700) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `secret` blob NOT NULL,
  `issued` int(11) NOT NULL,
  `lifetime` int(11) NOT NULL,
  `assoc_type` varchar(64) NOT NULL,
  PRIMARY KEY (`server_url`,`handle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `#__jc_nonce` (
  `server_url` varchar(1000) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `salt` varchar(40) NOT NULL,
  PRIMARY KEY (`server_url`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `#__jc_groups_in` (
  `appID` int(11) NOT NULL,
  `exAppGroup` varchar(100) NOT NULL,
  `joomlaGroup` varchar(100) NOT NULL,
  PRIMARY KEY (`appID`,`exAppGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `#__jc_groups_out` (
  `appID` int(11) NOT NULL,
  `exAppGroup` varchar(100) NOT NULL,
  `joomlaGroup` varchar(100) NOT NULL,
  PRIMARY KEY (`appID`,`joomlaGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `#__jc_auth_key` (
  `userID` int(11) NOT NULL,
  `privateKey` varchar(200) NOT NULL, 
  `used` tinyint(4) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  `session_id` text NOT NULL,
  `appID` int(11) NOT NULL,
  PRIMARY KEY (`privateKey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `#__jc_tokens` (
  `appID` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `timestamp` bigint(11) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;