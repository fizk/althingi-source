CREATE TABLE `althingi`.`Absence` (
  `assembly_id` int(11) NOT NULL,
  `plenary_id` int(11) NOT NULL,
  `congressman_abbreviation` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`assembly_id`, `plenary_id`, `congressman_abbreviation`),
  INDEX `plenary_id_idx` (`plenary_id` ASC),
  INDEX `abbreviation_idx` (`congressman_abbreviation` ASC),
  CONSTRAINT `assembly_id`
  FOREIGN KEY (`assembly_id`)
  REFERENCES `althingi`.`Assembly` (`assembly_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `plenary_id`
  FOREIGN KEY (`plenary_id`)
  REFERENCES `althingi`.`Plenary` (`plenary_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `abbreviation`
  FOREIGN KEY (`congressman_abbreviation`)
  REFERENCES `althingi`.`Congressman` (`abbreviation`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);
