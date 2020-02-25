-- MySQL dump 10.13  Distrib 5.7.29, for Linux (x86_64)
--
-- Host: localhost    Database: tasks_db
-- ------------------------------------------------------
-- Server version	5.7.29-0ubuntu0.18.04.1

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
-- Table structure for table `tbl_sessions`
--

DROP TABLE IF EXISTS `tbl_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_sessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Session ID',
  `userid` bigint(20) NOT NULL COMMENT 'User ID',
  `accesstoken` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Access Token',
  `accesstokenexpiry` datetime NOT NULL COMMENT 'Access Token Expiry Date/Time',
  `refreshtoken` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Refresh Token',
  `refreshtokenexpiry` datetime NOT NULL COMMENT 'Refresh Token Expiry Date/Time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `accesstoken` (`accesstoken`),
  UNIQUE KEY `refreshtoken` (`refreshtoken`),
  KEY `sessionuserid_fk` (`userid`),
  CONSTRAINT `sessionuserid_fk` FOREIGN KEY (`userid`) REFERENCES `tbl_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_sessions`
--

LOCK TABLES `tbl_sessions` WRITE;
/*!40000 ALTER TABLE `tbl_sessions` DISABLE KEYS */;
INSERT INTO `tbl_sessions` VALUES (1,2,'YjhjZmQ4ZDAyYzkwZjdhZDY2M2M4YTU4ODZkYWQ0OWZkMjdlODdiMTNiNDBhMDY3MTU4MjMwNzA4Ng==','2020-02-21 18:04:46','MTgzNjE4MTY0NjhlZTk4ODZmMDlhOTFlN2RhMjUyZTEwMDhjMjgyYjZhMTI2ZjRhMTU4MjMwNzA4Ng==','2020-03-06 17:44:46'),(2,1,'MzliZmQyZjc2YWRjMDQ1ZWM1NTI3MGYyZWIzZTAwMjY0NzIxOTliNmM0NjNjOGExMTU4MjMwNzE1MA==','2020-02-21 18:05:50','NzNiMTUzNmI1MzVlMmQzZTU0N2M0YmE5MTJlYjY5NzFhODE3OWJiNDdkY2RiNjcyMTU4MjMwNzE1MA==','2020-03-06 17:45:50');
/*!40000 ALTER TABLE `tbl_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tasks`
--

DROP TABLE IF EXISTS `tbl_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tasks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'task id - primary key',
  `title` varchar(255) NOT NULL COMMENT 'task title',
  `description` mediumtext COMMENT 'task description',
  `deadline` datetime DEFAULT NULL COMMENT 'task deadline date',
  `completed` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'task completion status',
  `userid` bigint(20) NOT NULL COMMENT 'user id',
  PRIMARY KEY (`id`),
  KEY `taskuserid_fk` (`userid`),
  CONSTRAINT `taskuserid_fk` FOREIGN KEY (`userid`) REFERENCES `tbl_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='tasks table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tasks`
--

LOCK TABLES `tbl_tasks` WRITE;
/*!40000 ALTER TABLE `tbl_tasks` DISABLE KEYS */;
INSERT INTO `tbl_tasks` VALUES (1,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',1),(2,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',1),(3,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',1),(4,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',1),(5,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',2),(6,'New title from gopibabu','New task description','2020-09-03 13:00:00','N',2);
/*!40000 ALTER TABLE `tbl_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `fullname` varchar(255) NOT NULL COMMENT 'User Full Name',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Password',
  `useractive` enum('N','Y') NOT NULL DEFAULT 'Y' COMMENT 'Is User Active',
  `loginattempts` int(1) NOT NULL DEFAULT '0' COMMENT 'Attempts to Log in',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (1,'Gopibabu Srungavarapu','gopibabus','$2y$10$SjbCi5wh855MSc0uBmjvi.iw.zlopPHvHXMgzo77RUZwzQ61BPiAS','Y',0),(2,'Harika Kota','harikak','$2y$10$ULmsZKtztBz4T93zA4FTPOq9DB8hQUV0jR4Zj2nrDjLmb.zCnEF2m','Y',0);
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-23 15:31:26
