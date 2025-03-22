INSERT INTO `lc_administrators` (`id`, `status`, `username`, `email`, `password_hash`, `apps`, `widgets`, `last_ip_address`, `last_hostname`, `login_attempts`, `total_logins`, `date_valid_from`, `date_valid_to`, `date_active`, `date_login`, `date_updated`, `date_created`)
VALUES (1, 1, 'admin', '', '$2y$10$iCjIIJh4rcNiOe2fRxE.Dej65HjwrTzRSe5YYaoibX.vIY/ngkUM.', '[]', '', '127.0.0.1', '', 0, 0, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
-- -----
INSERT INTO `lc_languages` (`status`, `code`, `code2`, `name`, `locale`, `url_type`, `raw_date`, `raw_time`, `raw_datetime`, `format_date`, `format_time`, `format_datetime`, `decimal_point`, `thousands_sep`, `priority`) VALUES
(1, 'en', 'eng', 'English', 'en_US.utf8,en_US.UTF-8,english', 'none', 'm/d/y', 'h:i:s A', 'm/d/y h:i:s A', '%b %e %Y', '%I:%M %p', '%b %e %Y %I:%M %p', '.', ',', 0);
-- -----
INSERT INTO `lc_modules` (`id`, `module_id`, `type`, `status`, `priority`, `settings`, `last_log`) VALUES
(4, 'job_error_reporter', 'job', 1, 0, '{"status":"1","report_frequency":"Weekly","email_receipient":"","priority":"0"}', ''),
(5, 'job_cache_cleaner', 'job', 1, 0, '{"status":"1","priority":"0"}', '');
-- -----
INSERT INTO `lc_settings_groups` (`key`, `name`, `description`, `priority`) VALUES
('site_info', 'Site Info', 'Site information', 10),
('defaults', 'Defaults', 'Default settings', 20),
('social_media', 'Social Media', 'Settings related to social media.', 30),
('email', 'Email', 'Email and SMTP', 40),
('listings', 'Listings', 'Settings for the catalog listing', 50),
('legal', 'Legal', 'Legal settings and information', 70),
('images', 'Images', 'Settings for graphical elements', 80),
('advanced', 'Advanced', 'Advanced settings', 100);
-- -----
INSERT INTO `lc_settings` (`group_key`, `type`, `title`, `description`, `key`, `value`, `datatype`, `function`, `required`, `priority`) VALUES
('', 'global', 'Platform Database Version', 'The platform version of the database', 'platform_database_version', '1.0.0', 'string', '', 0, 0),
('site_info', 'global', 'Site Name', 'The name of your site.', 'site_name', '', 'string', 'text()', 1, 10),
('site_info', 'global', 'Site Email', 'The site\'s email address.', 'site_email', '', 'string', 'email()', 1, 11),
('site_info', 'global', 'Site Phone', 'The site\'s phone number.', 'site_phone', '', 'string', 'phone()', 1, 11),
('site_info', 'global', 'Site Time Zone', 'The site\'s time zone.', 'site_timezone', '', 'string', 'timezone()', 1, 19),
('site_info', 'local', 'Site Language', 'The spoken language of your organization.', 'site_language_code', 'en', 'string', 'language()', 1, 20),
('defaults', 'global', 'Default Language', 'The default language, if not identified.', 'default_language_code', 'en', 'string', 'language()', 1, 10),
('email', 'local', 'Send Emails', 'Whether or not the platform should deliver outgoing emails.', 'email_status', '1', 'boolean', 'toggle("y/n")', 0, 1),
('email', 'local', 'SMTP Enabled', 'Whether or not to use an SMTP server for delivering email.', 'smtp_status', '0', 'boolean', 'toggle("e/d")', 0, 10),
('email', 'local', 'SMTP Host', 'SMTP hostname, e.g. smtp.myprovider.com.', 'smtp_host', 'localhost', 'string', 'text()', 0, 11),
('email', 'local', 'SMTP Port', 'SMTP port, e.g. 25, 465 (SSL/TLS), or 587 (STARTTLS).', 'smtp_port', '25', 'number', 'number()', 0, 12),
('email', 'local', 'SMTP Username', 'Username for SMTP authentication.', 'smtp_username', '', 'string', 'text()', 0, 13),
('email', 'local', 'SMTP Password', 'Password for SMTP authentication.', 'smtp_password', '', 'string', 'password()', 0, 14),
('listings', 'global', 'Maintenance Mode', 'Maintenance mode will enable a slash screen that the site is down for maintenance.', 'maintenance_mode', '0', 'boolean', 'toggle("e/d")', 0, 2),
('listings', 'global', 'Important Notice', 'An important notice to be displayed above your website.', 'important_notice', '', 'boolean', 'regional_text()', 1, 0),
('listings', 'local', 'Items Per Page', 'The number of items to be displayed per page.', 'items_per_page', '20', 'number', 'number()', 0, 10),
('listings', 'local', 'Data Table Rows', 'The number of data table rows to be displayed per page.', 'data_table_rows_per_page', '25', 'number', 'text()', 0, 11),
('legal', 'global', 'Cookie Policy', 'Select a page for the cookie policy or leave blank to disable.', 'cookie_policy', '', 'number', 'page()', 0, 10),
('legal', 'local', 'Privacy Policy', 'Select a page for the privacy policy consent or leave blank to disable.', 'privacy_policy', '', 'number', 'page()', 0, 11),
('images', 'global', 'Clear Thumbnails Cache', 'Remove all cached image thumbnails from disk.', 'cache_clear_thumbnails', '0', 'boolean', 'toggle()', 0, 1),
('images', 'local', 'Downsample', 'Downsample large uploaded images to best fit within the given dimensions of "width,height" or leave empty. Default: 2048,2048', 'image_downsample_size', '2048,2048', 'string', 'text()', 0, 34),
('images', 'local', 'Image Quality', 'The JPEG quality for uploaded images (0-100). Default: 90', 'image_quality', '90', 'number', 'number()', 0, 40),
('images', 'local', 'Thumbnail Quality', 'The JPEG quality for thumbnail images (0-100). Default: 65', 'image_thumbnail_quality', '65', 'number', 'number()', 0, 41),
('images', 'local', 'Interlaced Thumbnails', 'Generate interlaced thumbnail images for progressive loading. Increases the filesize by 10-20% but improves user experience.', 'image_thumbnail_interlaced', '0', 'boolean', 'toggle()', 0, 42),
('images', 'local', 'Whitespace Color', 'Set the color of any generated whitespace to the given RGB value. Default: 255,255,255', 'image_whitespace_color', '255,255,255', 'string', 'text()', 0, 43),
('images', 'local', 'AVIF Enabled', 'Use AVIF images if supported by the browser.', 'avif_enabled', '0', 'boolean', 'toggle("e/d")', 0, 44),
('images', 'local', 'WebP Enabled', 'Use WebP images if supported by the browser.', 'webp_enabled', '0', 'boolean', 'toggle("e/d")', 0, 45),
('advanced', 'global', 'GZIP Enabled', 'Compresses browser data. Increases the load on the server but decreases the bandwidth.', 'gzip_enabled', '1', 'boolean', 'toggle("e/d")', 5, 0),
('advanced', 'global', 'System Cache Enabled', 'Enables the system cache module which caches frequently used data.', 'cache_enabled', '1', 'boolean', 'toggle("e/d")', 0, 10),
('advanced', 'global', 'Clear System Cache', 'Remove all cached system information.', 'cache_clear', '0', 'boolean', 'toggle("y/n")', 0, 11),
('advanced', 'global', 'Static Content Domain Name', 'Use the given alias domain name for static content (images, stylesheets, javascripts) e.g. https://static.domain.tld/', 'static_domain', '', 'string', 'url()', 0, 12),
('advanced', 'local', 'Control Panel Link', 'The URL to your control panel, e.g. cPanel.', 'control_panel_link', 'https://', 'string', 'url()', 0, 18),
('advanced', 'local', 'Database Admin Link', 'The URL to your database manager, e.g. phpMyAdmin.', 'database_admin_link', 'https://', 'string', 'url()', 0, 19),
('advanced', 'local', 'Webmail Link', 'The URL to your webmail client.', 'webmail_link', 'https://', 'string', 'url()', 0, 20),
('security', 'local', 'CAPTCHA', 'Prevent robots from posting form data by enabling CAPTCHA security.', 'captcha_enabled', '1', 'boolean', 'toggle("e/d")', 0, 16),
('social_media', 'global', 'Facebook Link', 'The link to your Facebook page.', 'facebook_link', '#', 'string', 'url()', 0, 10),
('social_media', 'global', 'Instagram Link', 'The link to your Instagram page.', 'instagram_link', '#', 'string', 'url()', 0, 20),
('social_media', 'global', 'LinkedIn Link', 'The link to your LinkedIn page.', 'linkedin_link', '#', 'string', 'url()', 0, 30),
('social_media', 'global', 'Pinterest Link', 'The link to your Pinterest page.', 'pinterest_link', '#', 'string', 'url()', 0, 40),
('social_media', 'global', 'Twitter Link', 'The link to your Twitter page.', 'twitter_link', '#', 'string', 'url()', 0, 50),
('social_media', 'global', 'YouTube Link', 'The link to your YouTube channel.', 'youtube_link', '#', 'string', 'url()', 0, 60),
('', 'global', 'Template', '', 'template', 'default', 'string', 'template()', 1, 0),
('', 'global', 'Template Settings', '', 'template_settings', '{}', 'string', 'text()', 0, 0),
('', 'global', 'Jobs Last Run', 'Time when background jobs were last ran.', 'jobs_last_run', CURRENT_TIMESTAMP(), 'string', 'text()', 0, 0),
('', 'global', 'Jobs Last Push', 'Time when background jobs were last pushed for execution.', 'jobs_last_push', CURRENT_TIMESTAMP(), 'string', 'text()', 0, 0);
