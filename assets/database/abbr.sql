ALTER TABLE `Congressman`
  ADD COLUMN `abbreviation` VARCHAR(15) AFTER `name`;

ALTER TABLE `Congressman`
  ADD INDEX `abbreviation_idx` (`abbreviation`);
