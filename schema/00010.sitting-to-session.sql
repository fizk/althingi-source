
ALTER TABLE `CommitteeSitting` RENAME `CommitteeSession`;
ALTER TABLE `CommitteeSession` RENAME COLUMN `committee_sitting_id` TO `committee_session_id`;


ALTER TABLE `MinisterSitting` RENAME `MinisterSession`;
ALTER TABLE `MinisterSession` RENAME COLUMN `minister_sitting_id` TO `minister_session_id`;
