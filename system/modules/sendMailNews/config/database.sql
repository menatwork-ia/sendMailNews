-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_send_mail_news`
--

CREATE TABLE `tl_send_mail_news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `mail_server_name` varchar(255) NOT NULL default '',
  `mail_server_port` int(6) unsigned NOT NULL default '0',
  `mail_server_type` varchar(16) NOT NULL default '',
  `mail_server_security` varchar(512) NOT NULL default '',
  `mail_server_user` varchar(512) NOT NULL default '',
  `mail_server_password` varchar(255) NOT NULL default '',
  `mail_server_mailbox` varchar(255) NOT NULL default '',
  `news_archive` int(10) unsigned NOT NULL default '0',
  `inline_image` char(1) NOT NULL default '',
  `inline_image_dir` varchar(255) NOT NULL default '',
  `size` varchar(64) NOT NULL default '',
  `imagemargin` varchar(128) NOT NULL default '',
  `fullsize` char(1) NOT NULL default '',
  `floating` varchar(32) NOT NULL default '',
  `enclosure` char(1) NOT NULL default '',
  `enclosure_dir` varchar(255) NOT NULL default '',
  `time_check` varchar(16) NOT NULL default '',  
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;