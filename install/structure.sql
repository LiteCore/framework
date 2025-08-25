CREATE TABLE IF NOT EXISTS `lc_administrators` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`username` VARCHAR(32) NOT NULL DEFAULT '',
	`firstname` VARCHAR(32) NOT NULL DEFAULT '',
	`lastname` VARCHAR(32) NOT NULL DEFAULT '',
	`email` VARCHAR(128) NOT NULL DEFAULT '',
	`password_hash` VARCHAR(255) NOT NULL DEFAULT '',
	`apps` VARCHAR(4096) NOT NULL DEFAULT '',
	`widgets` VARCHAR(512) NOT NULL DEFAULT '',
	`two_factor_auth` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`login_attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`total_logins` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`last_ip_address` VARCHAR(39) NOT NULL DEFAULT '',
	`last_hostname` VARCHAR(128) NOT NULL DEFAULT '',
	`last_user_agent` VARCHAR(255) NOT NULL DEFAULT '',
	`known_ips` VARCHAR(255) NOT NULL DEFAULT '',
	`valid_from` TIMESTAMP NULL DEFAULT NULL,
	`valid_to` TIMESTAMP NULL DEFAULT NULL,
	`last_active` TIMESTAMP NULL DEFAULT NULL,
	`last_login` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `status` (`status`),
	KEY `username` (`username`),
	KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_emails` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` ENUM('draft','scheduled','sent','error') NOT NULL DEFAULT 'draft',
	`code` VARCHAR(255) NOT NULL DEFAULT '',
	`sender` VARCHAR(255) NOT NULL DEFAULT '',
	`recipients` TEXT NOT NULL DEFAULT '',
	`ccs` TEXT NOT NULL DEFAULT '',
	`bccs` TEXT NOT NULL DEFAULT '',
	`subject` VARCHAR(255) NOT NULL DEFAULT '',
	`multiparts` MEDIUMTEXT NOT NULL DEFAULT '',
	`ip_address` VARCHAR(39) NOT NULL DEFAULT '',
	`hostname` VARCHAR(128) NOT NULL DEFAULT '',
	`user_agent` VARCHAR(256) NOT NULL DEFAULT '',
	`language_code` CHAR(2) NOT NULL DEFAULT '',
	`reference` VARCHAR(256) NOT NULL DEFAULT '',
	`scheduled_to` TIMESTAMP NULL DEFAULT NULL,
	`sent_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `scheduled_to` (`scheduled_to`),
	KEY `code` (`code`),
	KEY `created_at` (`created_at`),
	KEY `sender_email` (`sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_languages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`code` CHAR(2) NOT NULL DEFAULT '',
	`code2` CHAR(3) NOT NULL DEFAULT '',
	`name` VARCHAR(32) NOT NULL DEFAULT '',
	`direction` ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
	`locale` VARCHAR(32) NOT NULL DEFAULT '',
	`locale_intl` VARCHAR(24) NOT NULL DEFAULT '',
	`url_type` VARCHAR(16) NOT NULL DEFAULT '',
	`domain_name` VARCHAR(64) NOT NULL DEFAULT '',
	`raw_date` VARCHAR(32) NOT NULL DEFAULT '',
	`raw_time` VARCHAR(32) NOT NULL DEFAULT '',
	`raw_datetime` VARCHAR(32) NOT NULL DEFAULT '',
	`format_date` VARCHAR(32) NOT NULL DEFAULT '',
	`format_time` VARCHAR(32) NOT NULL DEFAULT '',
	`format_datetime` VARCHAR(32) NOT NULL DEFAULT '',
	`decimal_point` VARCHAR(1) NOT NULL DEFAULT '',
	`thousands_sep` VARCHAR(1) NOT NULL DEFAULT '',
	`currency_code` CHAR(3) NOT NULL DEFAULT '',
	`priority` INT(11) NOT NULL DEFAULT '0',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY `id` (`id`),
	KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE IF NOT EXISTS `lc_modules` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`module_id` VARCHAR(64) NOT NULL DEFAULT '',
	`type` VARCHAR(16) NOT NULL DEFAULT '',
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`priority` INT(11) NOT NULL DEFAULT '0',
	`settings` TEXT NOT NULL DEFAULT '',
	`last_log` TEXT NOT NULL DEFAULT '',
	`last_pushed` TIMESTAMP NULL DEFAULT NULL,
	`last_processed` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `module_id` (`module_id`),
	KEY `type` (`type`),
	KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_pages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`dock` VARCHAR(64) NOT NULL DEFAULT '',
	`priority` INT(11) NOT NULL DEFAULT '0',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `status` (`status`),
	KEY `parent_id` (`parent_id`),
	KEY `dock` (`dock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_pages_info` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`page_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`language_code` CHAR(2) NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`content` MEDIUMTEXT NOT NULL DEFAULT '',
	`head_title` VARCHAR(128) NOT NULL DEFAULT '',
	`meta_description` VARCHAR(512) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `page_info` (`page_id`, `language_code`),
	KEY `page_id` (`page_id`),
	KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_redirects` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`immediate` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`pattern` VARCHAR(256) NOT NULL DEFAULT '',
	`destination` VARCHAR(256) NOT NULL DEFAULT '',
	`http_response_code` ENUM('301','302') NOT NULL DEFAULT '301',
	`redirects` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`last_redirected` TIMESTAMP NULL DEFAULT NULL,
	`valid_from` TIMESTAMP NULL DEFAULT NULL,
	`valid_to` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `pattern` (`pattern`) USING BTREE,
	INDEX `status` (`status`) USING BTREE,
	INDEX `immediate` (`immediate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_settings` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`group_key` VARCHAR(64) NOT NULL DEFAULT '',
	`type` ENUM('global','local') NOT NULL DEFAULT 'local',
	`key` VARCHAR(64) NOT NULL DEFAULT '',
	`value` VARCHAR(255) NOT NULL DEFAULT '',
	`datatype` VARCHAR(16) NOT NULL DEFAULT '',
	`title` VARCHAR(128) NOT NULL DEFAULT '',
	`description` VARCHAR(512) NOT NULL DEFAULT '',
	`required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`function` VARCHAR(128) NOT NULL DEFAULT '',
	`priority` INT(11) NOT NULL DEFAULT '0',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `key` (`key`),
	KEY `type` (`type`),
	KEY `group_key` (`group_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_settings_groups` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`key` VARCHAR(64) NOT NULL DEFAULT '',
	`name` VARCHAR(64) NOT NULL DEFAULT '',
	`description` VARCHAR(255) NOT NULL DEFAULT '',
	`priority` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_site_tags` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`position` ENUM('head','body') NOT NULL DEFAULT 'head',
	`name` VARCHAR(128) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL DEFAULT '',
	`require_consent` VARCHAR(64) NULL DEFAULT NULL,
	`priority` TINYINT(4) NOT NULL DEFAULT '0',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `status` (`status`) USING BTREE,
	INDEX `position` (`position`) USING BTREE,
	INDEX `priority` (`priority`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_third_parties` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`privacy_classes` VARCHAR(64) NOT NULL DEFAULT '',
	`category` VARCHAR(64) NOT NULL DEFAULT '',
	`name` VARCHAR(64) NOT NULL DEFAULT '',
	`homepage` VARCHAR(256) NOT NULL DEFAULT '',
	`cookie_policy_url` VARCHAR(256) NOT NULL DEFAULT '',
	`privacy_policy_url` VARCHAR(256) NOT NULL DEFAULT '',
	`opt_out_url` VARCHAR(256) NOT NULL DEFAULT '',
	`zzz` INT(10) UNSIGNED ZEROFILL NOT NULL DEFAULT '0000000000',
	`do_not_sell_url` VARCHAR(256) NOT NULL DEFAULT '',
	`collected_data` VARCHAR(256) NOT NULL DEFAULT '',
	`country_code` CHAR(2) NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `status` (`status`) USING BTREE,
	INDEX `country_code` (`country_code`) USING BTREE,
	CONSTRAINT `third_party_to_country` FOREIGN KEY (`country_code`) REFERENCES `lc_countries` (`iso_code_2`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_third_parties_info` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`third_party_id` INT(10) UNSIGNED NOT NULL,
	`language_code` CHAR(2) NOT NULL DEFAULT '',
	`collected_data` VARCHAR(512) NOT NULL,
	`description` VARCHAR(4096) NOT NULL,
	`purposes` VARCHAR(4096) NOT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `third_party_id` (`third_party_id`) USING BTREE,
	INDEX `language_code` (`language_code`) USING BTREE,
	CONSTRAINT `third_party_info_to_language` FOREIGN KEY (`language_code`) REFERENCES `lc_languages` (`code`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `third_party_info_to_third_party` FOREIGN KEY (`third_party_id`) REFERENCES `lc_third_parties` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- -----
CREATE TABLE `lc_translations` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(128) NOT NULL DEFAULT '',
	`text_en` TEXT NOT NULL DEFAULT '',
	`html` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`frontend` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`backend` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`last_accessed` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `code` (`code`),
	KEY `frontend` (`frontend`),
	KEY `backend` (`backend`),
	KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;