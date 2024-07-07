/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

ALTER TABLE `Absence` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `Plenary` RENAME `ParliamentarySession`;
ALTER TABLE `ParliamentarySession` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `PlenaryAgenda` RENAME `ParliamentarySessionAgenda`;
ALTER TABLE `ParliamentarySessionAgenda` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `Speech` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
