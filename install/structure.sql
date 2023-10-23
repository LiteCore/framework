CREATE TABLE IF NOT EXISTS `lb_administrators` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `email` VARCHAR(128) NOT NULL DEFAULT '',
  `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
  `apps` VARCHAR(4096) NOT NULL DEFAULT '',
  `widgets` VARCHAR(512) NOT NULL DEFAULT '',
  `login_attempts` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `total_logins` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `last_ip_address` VARCHAR(39) NOT NULL DEFAULT '',
  `last_hostname` VARCHAR(128) NOT NULL DEFAULT '',
  `last_user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `date_valid_from` TIMESTAMP NULL DEFAULT NULL,
  `date_valid_to` TIMESTAMP NULL DEFAULT NULL,
  `date_active` TIMESTAMP NULL DEFAULT NULL,
  `date_login` TIMESTAMP NULL DEFAULT NULL,
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
INSERT INTO `lb_administrators` (`id`, `status`, `username`, `email`, `password_hash`, `apps`, `widgets`, `last_ip_address`, `last_hostname`, `login_attempts`, `total_logins`, `date_valid_from`, `date_valid_to`, `date_active`, `date_login`, `date_updated`, `date_created`)
VALUES (1, 1, 'admin', '', '$2y$10$iCjIIJh4rcNiOe2fRxE.Dej65HjwrTzRSe5YYaoibX.vIY/ngkUM.', '[]', '', '127.0.0.1', '', 0, 0, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
-- --------------------------------------------------------
CREATE TABLE `lb_emails` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` ENUM('draft','scheduled','sent','error') NOT NULL DEFAULT 'draft',
  `code` VARCHAR(255) NOT NULL DEFAULT '',
  `sender` VARCHAR(255) NOT NULL DEFAULT '',
  `recipients` TEXT NOT NULL DEFAULT '',
  `ccs` TEXT NOT NULL DEFAULT '',
  `bccs` TEXT NOT NULL DEFAULT '',
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `multiparts` MEDIUMTEXT NOT NULL DEFAULT '',
  `date_scheduled` TIMESTAMP NULL DEFAULT NULL,
  `date_sent` TIMESTAMP NULL DEFAULT NULL,
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `date_scheduled` (`date_scheduled`),
  KEY `code` (`code`),
  KEY `date_created` (`date_created`),
  KEY `sender_email` (`sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE `lb_languages` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `code` CHAR(2) NOT NULL DEFAULT '',
  `code2` CHAR(3) NOT NULL DEFAULT '',
  `name` VARCHAR(32) NOT NULL DEFAULT '',
  `direction` ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
  `locale` VARCHAR(64) NOT NULL DEFAULT '',
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
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY `id` (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lb_modules` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` VARCHAR(64) NOT NULL DEFAULT '',
  `type` VARCHAR(16) NOT NULL DEFAULT '',
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `priority` INT(11) NOT NULL DEFAULT '0',
  `settings` TEXT NOT NULL DEFAULT '',
  `last_log` TEXT NOT NULL DEFAULT '',
  `date_pushed` TIMESTAMP NULL DEFAULT NULL,
  `date_processed` TIMESTAMP NULL DEFAULT NULL,
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_id` (`module_id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE `lb_pages` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `dock` VARCHAR(64) NOT NULL DEFAULT '',
  `priority` INT(11) NOT NULL DEFAULT '0',
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parent_id` (`parent_id`),
  KEY `dock` (`dock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE `lb_pages_info` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
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
-- --------------------------------------------------------
CREATE TABLE `lb_settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_key` VARCHAR(64) NOT NULL DEFAULT '',
  `type` ENUM('global','local') NOT NULL DEFAULT 'local',
  `key` VARCHAR(64) NOT NULL DEFAULT '',
  `value` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(128) NOT NULL DEFAULT '',
  `description` VARCHAR(512) NOT NULL DEFAULT '',
  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `function` VARCHAR(128) NOT NULL DEFAULT '',
  `priority` INT(11) NOT NULL DEFAULT '0',
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `type` (`type`),
  KEY `group_key` (`group_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE `lb_settings_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(64) NOT NULL DEFAULT '',
  `name` VARCHAR(64) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `priority` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------
CREATE TABLE `lb_translations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(128) NOT NULL DEFAULT '',
  `text_en` TEXT NOT NULL DEFAULT '',
  `html` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `frontend` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `backend` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_accessed` TIMESTAMP NULL DEFAULT NULL,
  `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `frontend` (`frontend`),
  KEY `backend` (`backend`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
