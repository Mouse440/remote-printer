CREATE DATABASE  IF NOT EXISTS `PRINTING` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `PRINTING`;
-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: 127.0.0.1    Database: PRINTING
-- ------------------------------------------------------
-- Server version	5.5.47-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `allowance_refresh_stamp`
--

DROP TABLE IF EXISTS `allowance_refresh_stamp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allowance_refresh_stamp` (
  `UID` int(11) unsigned NOT NULL DEFAULT '1',
  `Stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `Stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SJSUID` varchar(11) NOT NULL,
  `FirstName` varchar(45) NOT NULL,
  `LastName` varchar(45) NOT NULL,
  `QuestionID` int(11) NOT NULL,
  `Answer` varchar(256) NOT NULL,
  PRIMARY KEY (`UID`),
  UNIQUE KEY `UID_UNIQUE` (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback_questions`
--

DROP TABLE IF EXISTS `feedback_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback_questions` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Questions` varchar(60) NOT NULL,
  PRIMARY KEY (`UID`),
  UNIQUE KEY `UID_UNIQUE` (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `print_allowance`
--

DROP TABLE IF EXISTS `print_allowance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_allowance` (
  `UID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MemID` int(11) unsigned NOT NULL,
  `FirstName` varchar(45) DEFAULT NULL,
  `LastName` varchar(45) DEFAULT NULL,
  `Allowance` smallint(11) unsigned NOT NULL,
  PRIMARY KEY (`UID`),
  UNIQUE KEY `UID_UNIQUE` (`UID`),
  UNIQUE KEY `MemID_UNIQUE` (`MemID`),
  KEY `MemberID_idx` (`MemID`),
  CONSTRAINT `MemberID` FOREIGN KEY (`MemID`) REFERENCES `SCE-CORE`.`Members` (`MemberID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `print_error`
--

DROP TABLE IF EXISTS `print_error`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_error` (
  `Stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SJSUID` varchar(11) DEFAULT NULL,
  `MemID` int(11) DEFAULT NULL,
  `Message` varchar(255) DEFAULT NULL,
  `ErrorMessage` varchar(225) DEFAULT NULL,
  `Command` varchar(225) DEFAULT NULL,
  `Range` varchar(45) DEFAULT NULL,
  `Copies` varchar(45) DEFAULT NULL,
  `TwoSided` varchar(45) DEFAULT NULL,
  `Layout` varchar(45) DEFAULT NULL,
  `Total` varchar(45) DEFAULT NULL,
  `Allowance` varchar(45) DEFAULT NULL,
  `Title` varchar(45) DEFAULT NULL COMMENT 'PDF title',
  PRIMARY KEY (`UID`),
  UNIQUE KEY `UID_UNIQUE` (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `print_log`
--

DROP TABLE IF EXISTS `print_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_log` (
  `Stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SJSUID` varchar(11) NOT NULL,
  `FirstName` varchar(45) DEFAULT NULL,
  `LastName` varchar(45) DEFAULT NULL,
  `JobID` varchar(11) NOT NULL,
  `PagesUsed` smallint(11) unsigned NOT NULL,
  `PagesLeft` smallint(11) unsigned NOT NULL,
  `PagesAllowed` varchar(225) NOT NULL,
  `PrintCommand` varchar(255) NOT NULL,
  `Version` varchar(1) DEFAULT NULL COMMENT 'Printer version printed from',
  `Status` varchar(45) DEFAULT NULL COMMENT 'Status of this print record. e.g. canceled',
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=5449 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-02-04 16:48:06
