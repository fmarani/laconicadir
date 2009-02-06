-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 02 Feb, 2009 at 05:43 PM
-- Versione MySQL: 5.0.67
-- Versione PHP: 5.2.6-2ubuntu4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `laconicadir`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `providers`
--

CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(6) NOT NULL auto_increment,
  `nickname` varchar(64) NOT NULL,
  `minilogo` varchar(128) NOT NULL,
  `streamlogo` varchar(128) NOT NULL,
  `profilelogo` varchar(128) NOT NULL,
  `rooturl` varchar(128) NOT NULL,
  `registrationurl` varchar(128) NOT NULL,
  `apirooturl` varchar(128) NOT NULL,
  `license` varchar(64) NOT NULL,
  `lastmodified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `providers`
--

INSERT INTO `providers` (`id`, `nickname`, `minilogo`, `streamlogo`, `profilelogo`, `rooturl`, `registrationurl`, `apirooturl`, `license`, `lastmodified`) VALUES
(1, 'identica', 'http://identi.ca/minilogo.gif', 'http://identi.ca/streamlogo.gif', 'http://identi.ca/profilelogo.gif', 'http://identi.ca/', 'http://identi.ca/new_user', 'http://identi.ca/api/', 'CC-By-License', '2009-02-02 00:00:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `providers_langspecific`
--

CREATE TABLE IF NOT EXISTS `providers_langspecific` (
  `id_provider` int(6) NOT NULL,
  `language` char(2) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `categories` varchar(1024) NOT NULL,
  KEY `id_provider` (`id_provider`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `providers_langspecific`
--

INSERT INTO `providers_langspecific` (`id_provider`, `language`, `fullname`, `description`, `categories`) VALUES
(1, 'en', 'Identi.ca microblogging service', 'Start your own microblog on identi.ca!', 'all'),
(1, 'it', 'Identi.ca servizio di microblogging', 'Crea il tuo microblog su identi.ca!', 'tutto');

-- --------------------------------------------------------

--
-- Struttura della tabella `ui_users`
--

CREATE TABLE IF NOT EXISTS `ui_users` (
  `id_provider` int(6) NOT NULL,
  `sha1_password` binary(40) NOT NULL,
  KEY `id_provider` (`id_provider`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ui_users`
--

