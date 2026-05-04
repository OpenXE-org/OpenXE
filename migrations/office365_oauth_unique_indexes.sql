-- Office365 OAuth2 — Add UNIQUE constraints for token cache and account properties
-- Run this AFTER office365_oauth_tables.sql on existing installations.
-- This dedupes any leftover duplicate rows (one per account / one per varname)
-- and then adds UNIQUE indexes that make INSERT ... ON DUPLICATE KEY UPDATE
-- behave correctly going forward.

-- 1) Deduplicate office365_access_token: keep the most recent row per account
DELETE t1 FROM `office365_access_token` t1
INNER JOIN `office365_access_token` t2
  ON t1.office365_account_id = t2.office365_account_id
  AND t1.id < t2.id;

-- 2) Add UNIQUE index (drop the non-unique one if it exists)
ALTER TABLE `office365_access_token`
  DROP INDEX `office365_account_id`;
ALTER TABLE `office365_access_token`
  ADD UNIQUE KEY `office365_account_id` (`office365_account_id`);

-- 3) Deduplicate office365_account_property: keep the most recent row per (account, varname)
DELETE p1 FROM `office365_account_property` p1
INNER JOIN `office365_account_property` p2
  ON p1.office365_account_id = p2.office365_account_id
  AND p1.varname = p2.varname
  AND p1.id < p2.id;

-- 4) Add composite UNIQUE index for property upserts
ALTER TABLE `office365_account_property`
  DROP INDEX `office365_account_id`;
ALTER TABLE `office365_account_property`
  ADD UNIQUE KEY `account_varname` (`office365_account_id`, `varname`);
