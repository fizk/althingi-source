
ALTER TABLE `CommitteeSitting` RENAME `CommitteeSession`;
ALTER TABLE `CommitteeSession` RENAME COLUMN `committee_sitting_id` TO `committee_session_id`;
