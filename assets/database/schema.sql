-- MySQL dump 10.13  Distrib 5.7.16, for osx10.11 (x86_64)
--
-- Host: localhost    Database: althingi
-- ------------------------------------------------------
-- Server version	5.7.16

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
-- Table structure for table `Assembly`
--

DROP TABLE IF EXISTS `Assembly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Assembly` (
  `assembly_id` int(11) NOT NULL AUTO_INCREMENT,
  `from` date NOT NULL,
  `to` date DEFAULT NULL,
  PRIMARY KEY (`assembly_id`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Assembly_has_Party`
--

DROP TABLE IF EXISTS `Assembly_has_Party`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Assembly_has_Party` (
  `assembly_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `is_government` tinyint(4) NOT NULL DEFAULT '0',
  `rate` decimal(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`assembly_id`,`party_id`),
  KEY `fk_Assembly_has_Party_Party1_idx` (`party_id`),
  KEY `fk_Assembly_has_Party_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_Assembly_has_Party_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Assembly_has_Party_Party1` FOREIGN KEY (`party_id`) REFERENCES `Party` (`party_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cabinet`
--

DROP TABLE IF EXISTS `Cabinet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cabinet` (
  `cabinet_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cabinet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cabinet_has_Assembly`
--

DROP TABLE IF EXISTS `Cabinet_has_Assembly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cabinet_has_Assembly` (
  `cabinet_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  PRIMARY KEY (`cabinet_id`,`assembly_id`),
  KEY `fk_Cabinet_has_Assembly_Assembly1_idx` (`assembly_id`),
  KEY `fk_Cabinet_has_Assembly_Cabinet1_idx` (`cabinet_id`),
  CONSTRAINT `fk_Cabinet_has_Assembly_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Cabinet_has_Assembly_Cabinet1` FOREIGN KEY (`cabinet_id`) REFERENCES `Cabinet` (`cabinet_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cabinet_has_Congressman`
--

DROP TABLE IF EXISTS `Cabinet_has_Congressman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cabinet_has_Congressman` (
  `cabinet_id` int(11) NOT NULL,
  `congressman_id` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  PRIMARY KEY (`cabinet_id`,`congressman_id`,`title`),
  KEY `fk_Cabinet_has_Congressman_Congressman1_idx` (`congressman_id`),
  KEY `fk_Cabinet_has_Congressman_Cabinet1_idx` (`cabinet_id`),
  CONSTRAINT `fk_Cabinet_has_Congressman_Cabinet1` FOREIGN KEY (`cabinet_id`) REFERENCES `Cabinet` (`cabinet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Cabinet_has_Congressman_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `super_category_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`category_id`,`super_category_id`),
  KEY `fk_Category_SuperCategory1_idx` (`super_category_id`),
  CONSTRAINT `fk_Category_SuperCategory1` FOREIGN KEY (`super_category_id`) REFERENCES `SuperCategory` (`super_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Category_has_Issue`
--

DROP TABLE IF EXISTS `Category_has_Issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category_has_Issue` (
  `category_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`issue_id`,`assembly_id`),
  KEY `fk_Category_has_Issue_Issue1_idx` (`issue_id`,`assembly_id`),
  KEY `fk_Category_has_Issue_Category1_idx` (`category_id`),
  CONSTRAINT `fk_Category_has_Issue_Category1` FOREIGN KEY (`category_id`) REFERENCES `Category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Category_has_Issue_Issue1` FOREIGN KEY (`issue_id`, `assembly_id`) REFERENCES `Issue` (`issue_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Committee`
--

DROP TABLE IF EXISTS `Committee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Committee` (
  `committee_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `first_assembly_id` int(11) NOT NULL,
  `last_assembly_id` int(11) DEFAULT NULL,
  `abbr_long` varchar(45) DEFAULT NULL,
  `abbr_short` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`committee_id`),
  KEY `fk_Committee_Assembly1_idx` (`first_assembly_id`),
  KEY `fk_Committee_Assembly2_idx` (`last_assembly_id`),
  CONSTRAINT `fk_Committee_Assembly1` FOREIGN KEY (`first_assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Committee_Assembly2` FOREIGN KEY (`last_assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CommitteeMeeting`
--

DROP TABLE IF EXISTS `CommitteeMeeting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CommitteeMeeting` (
  `committee_meeting_id` int(11) NOT NULL AUTO_INCREMENT,
  `committee_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`committee_meeting_id`),
  KEY `fk_CommitteeMeeting_Committee1_idx` (`committee_id`),
  KEY `fk_CommitteeMeeting_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_CommitteeMeeting_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_CommitteeMeeting_Committee1` FOREIGN KEY (`committee_id`) REFERENCES `Committee` (`committee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18334 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CommitteeMeetingAgenda`
--

DROP TABLE IF EXISTS `CommitteeMeetingAgenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CommitteeMeetingAgenda` (
  `committee_meeting_agenda_id` int(11) NOT NULL AUTO_INCREMENT,
  `committee_meeting_id` int(11) NOT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `assembly_id` int(11) NOT NULL,
  `title` text,
  PRIMARY KEY (`committee_meeting_agenda_id`,`committee_meeting_id`),
  KEY `fk_CommitteeMeetingAgenda_CommitteeMeeting1_idx` (`committee_meeting_id`),
  KEY `fk_CommitteeMeetingAgenda_Issue1_idx` (`issue_id`,`assembly_id`),
  CONSTRAINT `fk_CommitteeMeetingAgenda_CommitteeMeeting1` FOREIGN KEY (`committee_meeting_id`) REFERENCES `CommitteeMeeting` (`committee_meeting_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_CommitteeMeetingAgenda_Issue1` FOREIGN KEY (`issue_id`, `assembly_id`) REFERENCES `Issue` (`issue_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Congressman`
--

DROP TABLE IF EXISTS `Congressman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Congressman` (
  `congressman_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `birth` date NOT NULL,
  `death` date DEFAULT NULL,
  PRIMARY KEY (`congressman_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1335 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Congressman_has_Issue`
--

DROP TABLE IF EXISTS `Congressman_has_Issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Congressman_has_Issue` (
  `congressman_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`congressman_id`,`issue_id`,`assembly_id`),
  KEY `fk_Congressman_has_Issue_Issue1_idx` (`issue_id`,`assembly_id`),
  KEY `fk_Congressman_has_Issue_Congressman1_idx` (`congressman_id`),
  CONSTRAINT `fk_Congressman_has_Issue_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Congressman_has_Issue_Issue1` FOREIGN KEY (`issue_id`, `assembly_id`) REFERENCES `Issue` (`issue_id`, `assembly_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Constituency`
--

DROP TABLE IF EXISTS `Constituency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Constituency` (
  `constituency_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `abbr_short` varchar(10) DEFAULT NULL,
  `abbr_long` varchar(25) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`constituency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Document`
--

DROP TABLE IF EXISTS `Document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  PRIMARY KEY (`document_id`,`issue_id`,`assembly_id`),
  KEY `fk_Document_Issue1_idx` (`issue_id`,`assembly_id`),
  CONSTRAINT `fk_Document_Issue1` FOREIGN KEY (`issue_id`, `assembly_id`) REFERENCES `Issue` (`issue_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1835 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Document_has_Congressman`
--

DROP TABLE IF EXISTS `Document_has_Congressman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Document_has_Congressman` (
  `document_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `congressman_id` int(11) NOT NULL,
  `minister` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`document_id`,`issue_id`,`assembly_id`,`congressman_id`),
  KEY `fk_Document_has_Congressman_Congressman1_idx` (`congressman_id`),
  KEY `fk_Document_has_Congressman_Document1_idx` (`document_id`,`issue_id`,`assembly_id`),
  CONSTRAINT `fk_Document_has_Congressman_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Document_has_Congressman_Document1` FOREIGN KEY (`document_id`, `issue_id`, `assembly_id`) REFERENCES `Document` (`document_id`, `issue_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Election`
--

DROP TABLE IF EXISTS `Election`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Election` (
  `election_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`election_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ElectionResult`
--

DROP TABLE IF EXISTS `ElectionResult`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ElectionResult` (
  `election_result_id` int(11) NOT NULL AUTO_INCREMENT,
  `election_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `seat` int(11) DEFAULT NULL,
  `result` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`election_result_id`),
  KEY `fk_ElectionResult_Election1_idx` (`election_id`),
  KEY `fk_ElectionResult_Party1_idx` (`party_id`),
  CONSTRAINT `fk_ElectionResult_Election1` FOREIGN KEY (`election_id`) REFERENCES `Election` (`election_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ElectionResult_Party1` FOREIGN KEY (`party_id`) REFERENCES `Party` (`party_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Election_has_Assembly`
--

DROP TABLE IF EXISTS `Election_has_Assembly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Election_has_Assembly` (
  `election_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  PRIMARY KEY (`election_id`,`assembly_id`),
  KEY `fk_Election_has_Election_Election1_idx` (`election_id`),
  KEY `fk_Election_has_Election_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_Election_has_Election_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Election_has_Election_Election1` FOREIGN KEY (`election_id`) REFERENCES `Election` (`election_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Issue`
--

DROP TABLE IF EXISTS `Issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Issue` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `assembly_id` int(11) NOT NULL,
  `congressman_id` int(11) DEFAULT NULL,
  `category` char(2) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sub_name` varchar(255) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `type_subname` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `question` varchar(255) DEFAULT NULL,
  `goal` text,
  `major_changes` text,
  `changes_in_law` text,
  `costs_and_revenues` text,
  `deliveries` text,
  `additional_information` text,
  PRIMARY KEY (`issue_id`,`assembly_id`),
  KEY `fk_Issue_Assembly1_idx` (`assembly_id`),
  KEY `fk_Issue_Congressman1_idx` (`congressman_id`),
  CONSTRAINT `fk_Issue_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Issue_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=901 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Party`
--

DROP TABLE IF EXISTS `Party`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Party` (
  `party_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abbr_short` varchar(5) DEFAULT NULL,
  `abbr_long` varchar(20) DEFAULT NULL,
  `color` char(6) DEFAULT NULL,
  PRIMARY KEY (`party_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Plenary`
--

DROP TABLE IF EXISTS `Plenary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Plenary` (
  `plenary_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`plenary_id`,`assembly_id`),
  KEY `fk_Plenary_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_Plenary_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Plenary_New`
--

DROP TABLE IF EXISTS `Plenary_New`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Plenary_New` (
  `plenary_id` int(11) NOT NULL AUTO_INCREMENT,
  `assembly_id` int(11) NOT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`plenary_id`,`assembly_id`),
  KEY `fk_Plenary_Assembly1_idx` (`assembly_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `President`
--

DROP TABLE IF EXISTS `President`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `President` (
  `president_id` int(11) NOT NULL AUTO_INCREMENT,
  `congressman_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `from` date NOT NULL COMMENT '	',
  `to` date DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `abbr` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`president_id`,`title`),
  UNIQUE KEY `unique_president_values` (`congressman_id`,`assembly_id`,`from`,`title`),
  KEY `fk_president_Congressman1_idx` (`congressman_id`),
  KEY `fk_president_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_president_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_president_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6034 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `congressman_id` int(11) NOT NULL,
  `constituency_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `party_id` int(11) DEFAULT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `abbr` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `uq_Session_Congressman_from` (`congressman_id`,`from`,`type`),
  KEY `fk_Session_Congressman_idx` (`congressman_id`),
  KEY `fk_Session_Party1_idx` (`party_id`),
  KEY `fk_Session_Constituency1_idx` (`constituency_id`),
  KEY `fk_Session_Assembly1_idx` (`assembly_id`),
  CONSTRAINT `fk_Session_Assembly1` FOREIGN KEY (`assembly_id`) REFERENCES `Assembly` (`assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Session_Congressman` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Session_Constituency1` FOREIGN KEY (`constituency_id`) REFERENCES `Constituency` (`constituency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Session_Party1` FOREIGN KEY (`party_id`) REFERENCES `Party` (`party_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106103 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Speech`
--

DROP TABLE IF EXISTS `Speech`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Speech` (
  `speech_id` varchar(45) NOT NULL,
  `plenary_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `congressman_id` int(11) NOT NULL,
  `congressman_type` varchar(255) DEFAULT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `text` longtext,
  `type` varchar(45) DEFAULT NULL,
  `iteration` varchar(5) DEFAULT NULL,
  `word_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`speech_id`),
  KEY `fk_Speach_Congressman1_idx` (`congressman_id`),
  KEY `fk_Speach_Plenary1_idx` (`plenary_id`,`assembly_id`),
  KEY `fk_Speech_Issue1_idx` (`issue_id`),
  CONSTRAINT `fk_Speach_Congressman1` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Speach_Plenary1` FOREIGN KEY (`plenary_id`, `assembly_id`) REFERENCES `Plenary` (`plenary_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Speech_Issue1` FOREIGN KEY (`issue_id`) REFERENCES `Issue` (`issue_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Speech_New`
--

DROP TABLE IF EXISTS `Speech_New`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Speech_New` (
  `speech_id` varchar(45) NOT NULL,
  `plenary_id` int(11) NOT NULL AUTO_INCREMENT,
  `assembly_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `congressman_id` int(11) NOT NULL,
  `congressman_type` varchar(255) DEFAULT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `text` longtext,
  `type` varchar(45) DEFAULT NULL,
  `iteration` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`speech_id`),
  KEY `fk_Speach_Congressman1_idx` (`congressman_id`),
  KEY `fk_Speach_Plenary1_idx` (`plenary_id`,`assembly_id`),
  KEY `fk_Speech_Issue1_idx` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SuperCategory`
--

DROP TABLE IF EXISTS `SuperCategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SuperCategory` (
  `super_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`super_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Vote`
--

DROP TABLE IF EXISTS `Vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Vote` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `committee_to` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `type` varchar(120) DEFAULT NULL,
  `outcome` varchar(45) DEFAULT NULL,
  `method` varchar(45) DEFAULT NULL,
  `yes` int(11) NOT NULL DEFAULT '0',
  `no` int(11) NOT NULL DEFAULT '0',
  `inaction` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `fk_Vote_Document1_idx` (`document_id`),
  KEY `fk_Vote_Issue2` (`issue_id`,`assembly_id`),
  CONSTRAINT `fk_Vote_Document1` FOREIGN KEY (`document_id`) REFERENCES `Document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Vote_Issue2` FOREIGN KEY (`issue_id`, `assembly_id`) REFERENCES `Issue` (`issue_id`, `assembly_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54805 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VoteItem`
--

DROP TABLE IF EXISTS `VoteItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VoteItem` (
  `vote_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_id` int(11) NOT NULL,
  `congressman_id` int(11) NOT NULL,
  `vote` varchar(45) NOT NULL,
  PRIMARY KEY (`vote_item_id`),
  UNIQUE KEY `vote_id_UNIQUE` (`vote_id`,`congressman_id`),
  KEY `fk_VoteItem_Congressman2_idx` (`congressman_id`),
  CONSTRAINT `fk_VoteItem_Congressman2` FOREIGN KEY (`congressman_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_VoteItem_Vote3` FOREIGN KEY (`vote_id`) REFERENCES `Vote` (`vote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=309922 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-25 12:58:44
