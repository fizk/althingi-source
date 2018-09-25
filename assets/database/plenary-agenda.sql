DROP TABLE IF EXISTS `PlenaryAgenda`;
CREATE TABLE `PlenaryAgenda` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `plenary_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `assembly_id` int(11) NOT NULL,
  `category` char(2) NOT NULL,
  `iteration_type` varchar(2) DEFAULT NULL,
  `iteration_continue` varchar(2) DEFAULT NULL,
  `iteration_comment` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `comment_type` varchar(2) DEFAULT NULL,
  `posed_id` int(11) default null,
  `posed` varchar(255) DEFAULT NULL,
  `answerer_id` int(11) default null,
  `answerer` varchar(255) DEFAULT NULL,
  `counter_answerer_id` int(11) default null,
  `counter_answerer` varchar(255) DEFAULT NULL,
  `instigator_id` int(11) default null,
  `instigator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`,`plenary_id`,`issue_id`,`assembly_id`,`category`),
  KEY `fk_PlenaryAgenda_Issue1_idx` (`issue_id`,`assembly_id`,`category`),
  KEY `fk_PlenaryAgenda_Congressman1_idx` (`posed_id`),
  KEY `fk_PlenaryAgenda_Congressman2_idx` (`answerer_id`),
  KEY `fk_PlenaryAgenda_Congressman3_idx` (`counter_answerer_id`),
  KEY `fk_PlenaryAgenda_Congressman4_idx` (`instigator_id`),
  CONSTRAINT `fk_PlenaryAgenda_Issue1` FOREIGN KEY (`issue_id`, `assembly_id`, `category`) REFERENCES `Issue` (`issue_id`, `assembly_id`, `category`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PlenaryAgenda_Congressman1` FOREIGN KEY (`posed_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PlenaryAgenda_Congressman2` FOREIGN KEY (`answerer_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PlenaryAgenda_Congressman3` FOREIGN KEY (`counter_answerer_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PlenaryAgenda_Congressman4` FOREIGN KEY (`instigator_id`) REFERENCES `Congressman` (`congressman_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;







