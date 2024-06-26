/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

ALTER TABLE `Category_has_Issue` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `CommitteeMeetingAgenda` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Congressman_has_Issue` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Document` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Document_has_Congressman` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Document_has_Committee` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Issue` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `PlenaryAgenda` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Speech` RENAME COLUMN `category` TO `kind`;
ALTER TABLE `Vote` RENAME COLUMN `category` TO `kind`;



ALTER TABLE `Category_has_Issue` MODIFY `kind` char(1);
ALTER TABLE `CommitteeMeetingAgenda` MODIFY `kind` char(1);
ALTER TABLE `Congressman_has_Issue` MODIFY `kind` char(1);
ALTER TABLE `Document` MODIFY `kind` char(1);
ALTER TABLE `Document_has_Congressman` MODIFY `kind` char(1);
ALTER TABLE `Document_has_Committee` MODIFY `kind` char(1);
ALTER TABLE `Issue` MODIFY `kind` char(1);
ALTER TABLE `PlenaryAgenda` MODIFY `kind` char(1);
ALTER TABLE `Speech` MODIFY `kind` char(1);
ALTER TABLE `Vote` MODIFY `kind` char(1);

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
