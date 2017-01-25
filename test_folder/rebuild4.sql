CREATE DATABASE  IF NOT EXISTS `PRINTING` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `PRINTING`;
-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: localhost    Database: PRINTING
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
INSERT INTO `allowance_refresh_stamp` VALUES (1,'2015-06-15 04:32:33');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES ('2015-01-25 05:46:40',1,'007978256','Duy','Nguyen',1,'asdasd'),('2015-01-26 00:38:53',2,'007978256','Duy','Nguyen',2,'helloweqwe'),('2015-01-28 00:26:17',3,'007050030','Joseph','Miclat Bourne',2,'Dropping a file on the button does not add th'),('2015-01-30 19:54:56',4,'009008480','Erick','Mena',2,'not printing'),('2015-02-02 21:13:54',5,'007050030','Joseph','Miclat Bourne',2,'After dropping a file and then clicking to pr'),('2015-02-03 21:13:23',6,'006622954','Wilson','Luc',1,'Allow us to select whether or not to do doubl'),('2015-02-04 22:19:24',7,'006044363','Tsui Yu','Wong',2,'Drag-and-drop PDF files to print causes the f'),('2015-02-19 21:34:33',8,'008912904','Hung','Tran',2,'Your feaking thing does not accept my .Pdf extension file!!'),('2015-03-18 18:09:44',9,'008715252','Kyaw','Win',2,'duy, we get an error. don\'t know what happen tho. Kyaw'),('2015-03-20 22:44:45',10,'006044363','Tsui Yu','Wong',2,'Two-sided printing does not reduce the number of prints by half. -Nelson'),('2015-04-22 17:04:32',11,'007810023','Ronald','Cheng',2,'ERROR NAME: undefined, COMMAND: Q, OPERAND STACK'),('2015-04-22 17:04:46',12,'007810023','Ronald','Cheng',2,'ERROR NAME: typecheck, COMMAND: image, OPERAND STACK'),('2015-05-24 22:22:31',13,'007978256','Duy','Nguyen',2,'asdasda');
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
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_allowance`
--

LOCK TABLES `print_allowance` WRITE;
/*!40000 ALTER TABLE `print_allowance` DISABLE KEYS */;
INSERT INTO `print_allowance` VALUES (1,39,'Charles','MacDonald',30),(2,2,'Duy','Nguyen',1000),(3,1,'Khalil','Estell',1000),(4,135,'Angel','Hernandez-Perez',30),(5,54,'Dustin','Phou',30),(6,60,'Kyaw','Win',30),(7,12,'Albert','Oum',1000),(8,138,'Miguel','Patiño',30),(9,49,'Keven','Gallegos',1000),(10,31,'Gurleen','Dhillon',1000),(11,34,'Edwin','Garcia',30),(12,27,'Andrew','Pang',30),(13,22,'Erick','Mena',30),(14,46,'Rajat','Bansal',30),(15,38,'Zachary','Baumgartner',30),(16,43,'Wilson','Luc',1000),(17,10,'Camille','Long',1000),(18,14,'Joseph','Miclat Bourne',1000),(19,112,'Divya','Kamath',30),(20,142,'Matthew','Boyd',30),(21,19,'Alex','Reyna',30),(22,11,'Devin','Villarosa',30),(23,65,'Saar','Sagir',30),(24,62,'Hung','Tran',1000),(25,105,'Jonathan','Chen',30),(26,87,'Samuel','Palomino',30),(27,141,'Ho Yeung','Lai',30),(28,140,'Henry','Tran',30),(29,59,'Anthony','Vo',30),(30,156,'Fayek','Wahhab',30),(31,96,'Roya','Del parastaran',30),(32,37,'Joshua','Ambion',30),(33,159,'Stefan','Francisco',30),(34,68,'Marvin','Flores',30),(35,84,'Jeanette ','Uddenfeldt',30),(36,47,'Kai','Wetlesen',30),(37,98,'Yvonne','Jacinto',30),(38,91,'Adrian','Marroquin',30),(39,151,'George','Sebastian',30),(40,24,'Tsui Yu','Wong',1000),(41,88,'Ronald','Cheng',30),(42,122,'Katie','Burrows',30),(43,20,'Rajwinder','Ruprai',30),(44,163,'Derek','Tran',30),(45,32,'Duo','Yao',30),(46,72,'Yvonne','Pon',30),(47,164,'y','nguyen',30),(48,109,'Melissa ','Lauzon',30),(49,25,'Almon Gem','Otanes',1000),(50,95,'eya','badal abdisho',30),(51,7,'Helen','Tsui',1000),(52,158,'Vikas','Pandey',30),(53,161,'Jason','Tran',30),(54,160,'Omar','Mousa',30),(55,21,'Wilson','Ng Tse',30),(56,143,'Rocely','Mati',30),(57,177,'Arnab','Kar',30),(58,162,'Steven Nam','Le',30),(59,137,'Yohan','Bouvron',30),(60,173,'Sricharan','Bonda',30),(61,64,'steven','hwu',30),(62,26,'Samira','Oliva',30),(63,111,'Viraj','Kulkarni',30),(64,171,'Kimberly Megan','Cheng',30),(65,176,'Alexander','Koumis',30),(66,3,'Kevin','Manan',1000),(67,187,'Yaoyu','Tan',30),(68,195,'Johnny','Nguyen',30),(69,80,'Yuyu','Chen',30),(70,188,'Sanjay','Maharaj',30),(71,175,'Nicholas','Carter',30),(72,185,'moises','quintero',30),(73,139,'Sara','Sepasian',30),(74,190,'Kevin','Do',30),(75,157,'Kristen','Kan',30),(76,169,'Alexander','Zavala',30),(77,48,'Gurnit','Ghardhora',30),(78,81,'Alejandro ','Puente',30),(79,63,'Michael Bricker','Bricker',30),(80,184,'Alexander','Gilham',30),(81,167,'Duy','Nguyen',30),(82,192,'Prasamsha','Pradhan',30),(83,132,'Derik','Vega',30),(84,198,'Vince','Ly',30),(85,33,'Eduardo','Espericueta',1000),(86,183,'Erik','Sanchez',30),(87,94,'Eric','Eddins',30),(88,170,'Joshua','Cruz',30),(89,6,'Albert','Chen',30),(90,53,'Tri','Han',30),(91,45,'Anton','Pedruco',30),(92,194,'Brandon','Nguyen',30),(93,179,'Rolando','Javier',30),(94,153,'Ankit','Sharma',30),(95,186,'Tianran','Chen',30),(96,197,'Megumi','Page',30),(97,191,'Jan Mikhael','Bayabo',30),(98,148,'Colin','Chen',30);
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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_error`
--

LOCK TABLES `print_error` WRITE;
/*!40000 ALTER TABLE `print_error` DISABLE KEYS */;
INSERT INTO `print_error` VALUES ('2015-01-25 05:56:05',20,'asd',0,'asdasd','asdasd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),('2015-01-25 23:53:17',21,NULL,2,NULL,'<h4>Cannot retrieve a Job ID. Will you allow us to store your file to prevent this from happening again?<h4>','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /tmp/phpamBxgx','1','1','false','portrait','1','1000','ghost.pdf'),('2015-05-24 21:09:40',22,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpE421FJ','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:09:40',23,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:10:45',24,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpbhPix1','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:10:45',25,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:11:32',26,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpXL8615','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:11:32',27,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:49:02',28,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpACv08M','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:49:02',29,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:55:39',30,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpGZ7RIy','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:55:39',31,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:56:48',32,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpIdoOJK','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:56:48',33,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:57:36',34,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpGHMYdG','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 21:57:36',35,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:02:12',36,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpsPUx0h','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:02:12',37,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:02:52',38,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpvNXGPS','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:02:52',39,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:32:25',40,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpAEzZzT','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:32:25',41,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:34:44',42,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/php8nVSxB','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:34:44',43,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:38:06',44,NULL,2,NULL,'','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpH1ej1N','1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-05-24 22:38:06',45,NULL,2,NULL,' Unable to save file.',NULL,'1-61','1','true','portrait','61','573','4Lecture_session_Interaction_2014_ISE_217_AMO'),('2015-06-07 20:42:01',46,NULL,2,NULL,'<h4>Cannot retrieve a Job ID. Will you allow us to store your file to prevent this from happening again?<h4>','lp -n  -o sides=one-sided -o portrait -o page-ranges= /Applications/MAMP/tmp/php/phpH8TNdk','1-9','1','true','portrait','9','991','demo.pdf');
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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_log`
--

LOCK TABLES `print_log` WRITE;
/*!40000 ALTER TABLE `print_log` DISABLE KEYS */;
INSERT INTO `print_log` VALUES ('2015-05-24 08:37:16',1,'007978256','Duy','Nguyen','26',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpUW33y5','2',NULL),('2015-05-24 08:38:34',2,'007978256','Duy','Nguyen','27',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/php2QHwyV','2',NULL),('2015-05-24 08:39:38',3,'007978256','Duy','Nguyen','28',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpLBVwg5','2',NULL),('2015-05-24 08:40:19',4,'007978256','Duy','Nguyen','29',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpZkG993','2',NULL),('2015-05-24 08:41:33',5,'007978256','Duy','Nguyen','30',2,754,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-2 /Applications/MAMP/tmp/php/phpAmj5yo','2',NULL),('2015-05-24 08:42:46',6,'007978256','Duy','Nguyen','31',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phph65PIc','2',NULL),('2015-05-24 08:43:07',7,'007978256','Duy','Nguyen','32',2,754,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-2 /Applications/MAMP/tmp/php/phpRlRDoj','2',NULL),('2015-05-24 08:44:37',8,'007978256','Duy','Nguyen','33',61,695,'756','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpkzUYmo','2',NULL),('2015-05-24 08:45:30',9,'007978256','Duy','Nguyen','34',61,634,'695','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpapASUo','2',NULL),('2015-05-24 08:46:45',10,'007978256','Duy','Nguyen','35',61,634,'695','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpY85OFx','2',NULL),('2015-05-24 21:09:21',11,'007978256','Duy','Nguyen','36',61,573,'634','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpMZDGcX','2',NULL),('2015-05-24 22:07:15',12,'007978256','Duy','Nguyen','46',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpDqHjCi','2','Canceled'),('2015-05-24 22:18:54',13,'007978256','Duy','Nguyen','47',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpVGbzRL','2','Canceled'),('2015-05-24 22:27:29',14,'007978256','Duy','Nguyen','48',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpLc3F9U','2','Canceled'),('2015-05-24 22:31:54',15,'007978256','Duy','Nguyen','49',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpWL1tCE','2','Canceled'),('2015-05-24 22:35:38',16,'007978256','Duy','Nguyen','52',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpGfkLcu','2','Canceled'),('2015-05-24 22:41:33',17,'007978256','Duy','Nguyen','54',61,512,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-61 /Applications/MAMP/tmp/php/phpBc9LHJ','2','Canceled'),('2015-05-25 16:51:43',18,'007978256','Duy','Nguyen','55',7,566,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-7 /Applications/MAMP/tmp/php/phpNTyG8z','2','Canceled'),('2015-05-25 18:54:50',19,'007978256','Duy','Nguyen','56',7,566,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-7 /Applications/MAMP/tmp/php/phpfRKUQq','2','Canceled'),('2015-05-25 18:57:38',20,'007978256','Duy','Nguyen','57',7,566,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-7 /Applications/MAMP/tmp/php/phpxIh5L1','2','Canceled'),('2015-05-25 21:39:04',21,'007978256','Duy','Nguyen','58',2,571,'573','lp -n 1 -o sides=two-sided-short-edge -o landscape -o page-ranges=5-6 /Applications/MAMP/tmp/php/php0qoIiI','2','Canceled'),('2015-05-26 01:14:59',22,'007978256','Duy','Nguyen','59',7,566,'573','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-7 /Applications/MAMP/tmp/php/phpE9sOVm','2','Canceled'),('2015-06-06 02:43:56',23,'007978256','Duy','Nguyen','67',9,991,'1000','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/tmp/php/phpRVbIoi','2','Canceled'),('2015-06-07 02:49:49',24,'007978256','Duy','Nguyen','68',9,991,'1000','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/tmp/php/phpJgCJMX','2',NULL),('2015-06-07 03:06:07',25,'007978256','Duy','Nguyen','69',1,990,'991','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/tmp/php/php1Lbls7','2',NULL),('2015-06-07 20:15:51',26,'007978256','Duy','Nguyen','70',9,991,'1000','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/tmp/php/phpXCK9eY','2',NULL),('2015-06-10 06:05:46',27,'007978256','Duy','Nguyen','108',9,982,'991','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433916344.46.pdf','3',NULL),('2015-06-10 06:15:51',28,'007978256','Duy','Nguyen','109',1,990,'991','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/86fcc4db23ef002198e766d9b4df1b1717c8dad5.1433916946.16.pdf','3','processing'),('2015-06-10 06:16:30',29,'007978256','Duy','Nguyen','110',5,986,'991','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-5 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/5af7d11859e81605db826d3dc851dcae76e83193.1433916977.37.pdf','3','pending'),('2015-06-10 06:17:02',30,'007978256','Duy','Nguyen','111',9,982,'991','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433917020.41.pdf','3','pending'),('2015-06-10 06:20:29',31,'007978256','Duy','Nguyen','112',9,973,'982','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433917227.41.pdf','3','pending'),('2015-06-10 22:08:16',32,'007978256','Duy','Nguyen','113',1,972,'973','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/8de5dd6f062e79b8e6a8ca6a4290f56528357700.1433974084.06.jpg','3','processing'),('2015-06-10 22:23:21',33,'007978256','Duy','Nguyen','114',9,963,'972','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433974998.86.pdf','3','processing'),('2015-06-10 22:24:00',34,'007978256','Duy','Nguyen','115',9,954,'963','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433975037.86.pdf','3','pending'),('2015-06-10 22:24:50',35,'007978256','Duy','Nguyen','116',9,945,'954','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433975088.5.pdf','3','pending'),('2015-06-10 22:26:38',36,'007978256','Duy','Nguyen','117',9,936,'945','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433975196.16.pdf','3','processing'),('2015-06-10 22:50:43',37,'007978256','Duy','Nguyen','118',1,935,'936','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/86fcc4db23ef002198e766d9b4df1b1717c8dad5.1433976639.65.pdf','3','pending'),('2015-06-10 22:54:24',38,'007978256','Duy','Nguyen','120',9,926,'935','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433976861.67.pdf','3','1'),('2015-06-10 22:55:45',39,'007978256','Duy','Nguyen','121',9,917,'926','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433976942.98.pdf','3','1'),('2015-06-10 22:57:45',40,'007978256','Duy','Nguyen','122',9,908,'917','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433977063.24.pdf','3','3'),('2015-06-10 22:58:19',41,'007978256','Duy','Nguyen','123',32,876,'908','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-32 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/0385ab24d2e26143085e462bc6e97554b9d42374.1433977088.16.pdf','3','1'),('2015-06-11 00:14:35',42,'007978256','Duy','Nguyen','124',1,875,'876','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/2d350341a645ad33ab5604aca16c05f22a83ff51.1433981665.66.png','3','3'),('2015-06-11 00:15:38',43,'007978256','Duy','Nguyen','125',9,866,'875','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433981733.51.pdf','3','1'),('2015-06-11 00:42:14',44,'007978256','Duy','Nguyen','129',9,857,'866','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433983330.68.pdf','3','3'),('2015-06-11 00:45:37',45,'007978256','Duy','Nguyen','130',9,848,'857','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433983534.76.pdf','3','3'),('2015-06-11 00:46:08',46,'007978256','Duy','Nguyen','131',9,839,'848','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433983566.17.pdf','3','1'),('2015-06-11 00:47:23',47,'007978256','Duy','Nguyen','132',9,830,'839','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433983638.57.pdf','3','1'),('2015-06-11 00:47:38',48,'007978256','Duy','Nguyen','133',9,821,'830','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433983656.24.pdf','3','1'),('2015-06-11 00:56:19',49,'007978256','Duy','Nguyen','135',9,812,'821','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433984176.94.pdf','3','pending'),('2015-06-11 00:57:32',50,'007978256','Duy','Nguyen','136',9,803,'812','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433984249.9.pdf','3','pending'),('2015-06-11 01:00:22',51,'007978256','Duy','Nguyen','137',9,794,'803','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433984420.43.pdf','3','pending'),('2015-06-11 01:00:37',52,'007978256','Duy','Nguyen','138',9,785,'794','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433984436.28.pdf','3','pending'),('2015-06-11 01:01:17',53,'007978256','Duy','Nguyen','139',9,776,'785','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433984473.51.pdf','3','pending'),('2015-06-11 01:20:31',54,'007978256','Duy','Nguyen','140',9,767,'776','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433985628.77.pdf','3','pending'),('2015-06-11 01:27:37',55,'007978256','Duy','Nguyen','141',9,758,'767','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986055.05.pdf','3','pending'),('2015-06-11 01:28:40',56,'007978256','Duy','Nguyen','143',9,758,'767','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986055.05.pdf','3','pending'),('2015-06-11 01:28:41',57,'007978256','Duy','Nguyen','144',9,758,'767','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986055.05.pdf','3','pending'),('2015-06-11 01:28:41',58,'007978256','Duy','Nguyen','142',9,758,'767','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986055.05.pdf','3','pending'),('2015-06-11 01:40:27',59,'007978256','Duy','Nguyen','145',9,749,'758','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986825.01.pdf','3','pending'),('2015-06-11 01:41:26',60,'007978256','Duy','Nguyen','146',9,740,'749','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986883.81.pdf','3','pending'),('2015-06-11 01:41:52',61,'007978256','Duy','Nguyen','147',9,731,'740','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433986906.55.pdf','3','processing'),('2015-06-11 01:51:32',62,'007978256','Duy','Nguyen','149',9,722,'731','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433987490.31.pdf','3','1'),('2015-06-11 01:55:01',63,'007978256','Duy','Nguyen','153',9,713,'722','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1433987698.93.pdf','3','1'),('2015-06-12 23:41:22',64,'007978256','Duy','Nguyen','154',1,712,'713','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/86fcc4db23ef002198e766d9b4df1b1717c8dad5.1434152475.28.pdf','3','3'),('2015-06-13 21:29:51',65,'007978256','Duy','Nguyen','159',9,703,'712','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-9 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/70600e44c35e478ace6041f3064692448ad9b0e2.1434230625.98.pdf','3','1'),('2015-06-13 21:30:39',66,'007978256','Duy','Nguyen','160',2,701,'703','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-2 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/86fcc4db23ef002198e766d9b4df1b1717c8dad5.1434231029.6.pdf','3','3'),('2015-06-13 21:32:55',67,'007978256','Duy','Nguyen','161',32,669,'701','lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-32 /Applications/MAMP/htdocs/sceprinterv3/php/temporary_file_storage/49ee6c5d9472950cafe9772f32d673915575bc00.1434231082.29.pdf','3','1');
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

-- Dump completed on 2015-06-17 22:19:41
