-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: localhost    Database: SCE-CORE
-- ------------------------------------------------------
-- Server version	5.5.34

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
-- Current Database: `SCE-CORE`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `SCE-CORE` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `SCE-CORE`;

--
-- Table structure for table `Administrators`
--

DROP TABLE IF EXISTS `Administrators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Administrators` (
  `AdminID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SJSUID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(5) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `Role` varchar(20) NOT NULL,
  `Active` tinyint(4) NOT NULL DEFAULT '1',
  `Password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`AdminID`),
  UNIQUE KEY `UID_UNIQUE` (`AdminID`),
  UNIQUE KEY `SJSUID_UNIQUE` (`SJSUID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`),
  UNIQUE KEY `Phone_UNIQUE` (`Phone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Administrators`
--

LOCK TABLES `Administrators` WRITE;
/*!40000 ALTER TABLE `Administrators` DISABLE KEYS */;
/*!40000 ALTER TABLE `Administrators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DoorCodes`
--

DROP TABLE IF EXISTS `DoorCodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DoorCodes` (
  `CodeID` int(11) NOT NULL AUTO_INCREMENT,
  `Code` varchar(10) NOT NULL,
  `Available` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`CodeID`),
  UNIQUE KEY `Code_UNIQUE` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DoorCodes`
--

LOCK TABLES `DoorCodes` WRITE;
/*!40000 ALTER TABLE `DoorCodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `DoorCodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DreamsparkRequests`
--

DROP TABLE IF EXISTS `DreamsparkRequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DreamsparkRequests` (
  `DreamProcID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SJSUID` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `Key` varchar(35) COLLATE utf8_bin DEFAULT NULL,
  `State` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'state 0 = inital submission\nstate 1 = user verified',
  `FirstName` varchar(50) COLLATE utf8_bin NOT NULL,
  `MiddleName` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8_bin NOT NULL,
  `Email` varchar(150) COLLATE utf8_bin NOT NULL,
  `Phone` varchar(45) COLLATE utf8_bin DEFAULT '---',
  `Active` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = not been verified\n0 = verified ',
  PRIMARY KEY (`DreamProcID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DreamsparkRequests`
--

LOCK TABLES `DreamsparkRequests` WRITE;
/*!40000 ALTER TABLE `DreamsparkRequests` DISABLE KEYS */;
/*!40000 ALTER TABLE `DreamsparkRequests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Logs`
--

DROP TABLE IF EXISTS `Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Logs` (
  `MemberID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` datetime NOT NULL,
  `SJSUID` varchar(10) NOT NULL,
  `Message` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`MemberID`),
  UNIQUE KEY `UID_UNIQUE` (`MemberID`),
  UNIQUE KEY `SJSUID_UNIQUE` (`SJSUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Logs`
--

LOCK TABLES `Logs` WRITE;
/*!40000 ALTER TABLE `Logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `Logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MemberRequests`
--

DROP TABLE IF EXISTS `MemberRequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MemberRequests` (
  `TempID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SJSUID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(5) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(45) DEFAULT '---',
  `Term` varchar(15) NOT NULL DEFAULT 'Annual',
  `Active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`TempID`),
  UNIQUE KEY `UID_UNIQUE` (`TempID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MemberRequests`
--

LOCK TABLES `MemberRequests` WRITE;
/*!40000 ALTER TABLE `MemberRequests` DISABLE KEYS */;
/*!40000 ALTER TABLE `MemberRequests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Members`
--

DROP TABLE IF EXISTS `Members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Members` (
  `MemberID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SJSUID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(5) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `Role` varchar(100) NOT NULL,
  `Active` tinyint(4) NOT NULL DEFAULT '1',
  `StartTerm` varchar(10) DEFAULT NULL,
  `EndTerm` varchar(10) DEFAULT NULL,
  `Benefits` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Membership Count is the amount of times someone as requested and become a memeber. \nNew members start with 1 for annual, or 0.5 for a semester.\n\nBronze (0-1)\nSilver (2-3)\nGold (4-5)\nPlatinum (6-7)\nSilicon (8-9)',
  `Password` varchar(255) DEFAULT NULL COMMENT 'This password is for the SCE Directory Services',
  `DoorCode` varchar(10) DEFAULT NULL,
  `EmailVerified` tinyint(4) DEFAULT '0' COMMENT 'If the member logs into the SCE Directory Service then this gets flipped \nfrom 0 to 1 showing that the user must have acquired his/her credientials from\nthe email sent to them. ',
  PRIMARY KEY (`MemberID`),
  UNIQUE KEY `UID_UNIQUE` (`MemberID`),
  UNIQUE KEY `SJSUID_UNIQUE` (`SJSUID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`),
  UNIQUE KEY `Phone_UNIQUE` (`Phone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Members`
--

LOCK TABLES `Members` WRITE;
/*!40000 ALTER TABLE `Members` DISABLE KEYS */;
INSERT INTO `Members` VALUES (2,'2015-06-28 20:59:58','111111111','Joe','','Blow','asdasdasd','asdasd','Officer',1,'2014-F','2015-S',0,'asdasdasdasdasd','asdasd',0);
/*!40000 ALTER TABLE `Members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `PRINTING`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `PRINTING` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `PRINTING`;

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
-- Dumping data for table `allowance_refresh_stamp`
--

LOCK TABLES `allowance_refresh_stamp` WRITE;
/*!40000 ALTER TABLE `allowance_refresh_stamp` DISABLE KEYS */;
INSERT INTO `allowance_refresh_stamp` VALUES (1,'2015-06-28 20:53:53');
/*!40000 ALTER TABLE `allowance_refresh_stamp` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `feedback_questions`
--

LOCK TABLES `feedback_questions` WRITE;
/*!40000 ALTER TABLE `feedback_questions` DISABLE KEYS */;
INSERT INTO `feedback_questions` VALUES (1,'What could we do to improve our application?'),(2,'What was the error? Decribe the steps that got you there:');
/*!40000 ALTER TABLE `feedback_questions` ENABLE KEYS */;
UNLOCK TABLES;

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
  CONSTRAINT `MemberID` FOREIGN KEY (`MemID`) REFERENCES `SCE-CORE`.`Members` (`MemberID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_allowance`
--

LOCK TABLES `print_allowance` WRITE;
/*!40000 ALTER TABLE `print_allowance` DISABLE KEYS */;
INSERT INTO `print_allowance` VALUES (1,2,'Joe','Blow',1000);
/*!40000 ALTER TABLE `print_allowance` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_error`
--

LOCK TABLES `print_error` WRITE;
/*!40000 ALTER TABLE `print_error` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_error` ENABLE KEYS */;
UNLOCK TABLES;

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
  `Status` varchar(45) DEFAULT NULL COMMENT 'Status of this record\n1: "Pending..."\n2: "Held."\n3: "Processing..."\n4: "Stopped."\n5: "Canceled."\n6: "Aborted."\n7: "Completed."',
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_log`
--

LOCK TABLES `print_log` WRITE;
/*!40000 ALTER TABLE `print_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-28 14:02:33
