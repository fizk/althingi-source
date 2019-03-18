drop table `Cabinet_has_Congressman`;
drop table `Cabinet_has_Assembly`;
drop table `Cabinet`;

CREATE TABLE `Cabinet` (
  `cabinet_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `aka` varchar(255) DEFAULT NULL,
  `from` date default null,
  `to` date default null,
  `description` text default null,
  PRIMARY KEY (`cabinet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
