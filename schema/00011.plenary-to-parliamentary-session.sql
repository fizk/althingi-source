ALTER TABLE `Absence` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `Plenary` RENAME `ParliamentarySession`;
ALTER TABLE `ParliamentarySession` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `PlenaryAgenda` RENAME `ParliamentarySessionAgenda`;
ALTER TABLE `ParliamentarySessionAgenda` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

ALTER TABLE `Speech` RENAME COLUMN `plenary_id` TO `parliamentary_session_id`;

