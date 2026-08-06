-- Office365 OAuth2 Integration Tables
-- Created for Office365 email account management

-- Main account table
CREATE TABLE IF NOT EXISTS `office365_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `refresh_token` longtext DEFAULT NULL COMMENT 'Long-lived refresh token from Microsoft',
  `identifier` varchar(255) DEFAULT NULL,
  `tenant_id` varchar(255) DEFAULT NULL COMMENT 'Microsoft tenant ID (organization)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Access token cache table
CREATE TABLE IF NOT EXISTS `office365_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) NOT NULL,
  `token` longtext DEFAULT NULL COMMENT 'Current OAuth access token',
  `expires` datetime DEFAULT NULL COMMENT 'Token expiration timestamp',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `office365_account_id` (`office365_account_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- OAuth scopes granted by user
CREATE TABLE IF NOT EXISTS `office365_account_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL COMMENT 'OAuth scope granted by user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `office365_account_id` (`office365_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Account metadata (email, etc)
CREATE TABLE IF NOT EXISTS `office365_account_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) NOT NULL,
  `varname` varchar(64) DEFAULT NULL COMMENT 'Property name (e.g., email_address)',
  `value` varchar(255) DEFAULT NULL COMMENT 'Property value',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_varname` (`office365_account_id`,`varname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Add foreign key constraints after all tables are created
ALTER TABLE `office365_access_token` ADD CONSTRAINT `fk_office365_access_token_account`
  FOREIGN KEY (`office365_account_id`) REFERENCES `office365_account` (`id`) ON DELETE CASCADE;

ALTER TABLE `office365_account_scope` ADD CONSTRAINT `fk_office365_account_scope_account`
  FOREIGN KEY (`office365_account_id`) REFERENCES `office365_account` (`id`) ON DELETE CASCADE;

ALTER TABLE `office365_account_property` ADD CONSTRAINT `fk_office365_account_property_account`
  FOREIGN KEY (`office365_account_id`) REFERENCES `office365_account` (`id`) ON DELETE CASCADE;
