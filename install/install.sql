-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. Oktober 2010 um 11:13
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `phpdl`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `sessionId` varchar(32) NOT NULL COMMENT 'md5 hex',
  `ctime` int(11) NOT NULL COMMENT 'Create time',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `sessionId`, `ctime`) VALUES
(1, 'root', 'x', '', 1287219591);

