<?php

	define('BACKEND_ALIAS', 'admin');

	######################################################################
	## Files and Directory  ##############################################
	######################################################################

	// File System
	define('DOCUMENT_ROOT',    str_replace('\\', '/', rtrim(realpath(!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : __DIR__.'/..'), '/')));

	define('FS_DIR_APP',       str_replace('\\', '/', rtrim(realpath(__DIR__.'/..'), '/')) . '/');
	define('FS_DIR_STORAGE',   FS_DIR_APP . 'storage/');

	// Web System
	define('WS_DIR_APP',       preg_replace('#^'. preg_quote(DOCUMENT_ROOT, '#') .'#', '', FS_DIR_APP));
	define('WS_DIR_ADMIN',     WS_DIR_APP . BACKEND_ALIAS . '/');
	define('WS_DIR_STORAGE',   WS_DIR_APP . 'storage/');

	######################################################################
	## Database ##########################################################
	######################################################################

	// Database
	define('DB_SERVER', '127.0.0.1');
	define('DB_USERNAME', '');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', '');
	define('DB_TABLE_PREFIX', 'lc_');

	######################################################################
	## System ############################################################
	######################################################################

	// Set Character Encoding
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');
	mb_http_output('UTF-8');

	// Errors
	error_reporting(version_compare(PHP_VERSION, '5.4.0', '>=') ? E_ALL & ~E_STRICT : E_ALL);
	ini_set('ignore_repeated_errors', 'On');
	ini_set('log_errors', 'On');
	ini_set('error_log', FS_DIR_STORAGE . 'logs/errors.log');
	ini_set('display_startup_errors', 'Off');
	ini_set('display_errors', 'Off');
	if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1'])) {
		error_reporting(E_ALL);
		ini_set('display_startup_errors', 'On');
		ini_set('display_errors', 'On');
	}

	// Default Script timeout
	set_time_limit(60);

	// Floating Point Precision
	ini_set('serialize_precision', 6);

	// Sessions
	ini_set('session.name', 'LCSESSID');
	ini_set('session.use_cookies', 1);
	ini_set('session.use_only_cookies', 1);
	ini_set('session.use_strict_mode', 1);
	ini_set('session.use_trans_sid', 0);
	ini_set('session.cookie_httponly', 1);
	ini_set('session.cookie_lifetime', 0);
	ini_set('session.cookie_path', WS_DIR_APP);
	ini_set('session.cookie_samesite', 'Lax');
	ini_set('session.gc_maxlifetime', 1440);

	// Timezone
	ini_set('date.timezone', 'Europe/Stockholm');

	// Output Compression
	ini_set('zlib.output_compression', 1);

	######################################################################
	## Application #######################################################
	######################################################################

	// Ability to disable vMods
	define('VMOD_DISABLED', false);

	// Session Platform ID
  define('SESSION_UNIQUE_ID', 'intranet');

	// Password Encryption Salt
  define('PASSWORD_SALT', '459u09jfgosdmvrointr90293gmvldsbnviodfnhwqpomfrhg03');

	// BitBucket
  define('BITBUCKET_REPOSITORY_UUID', '{9c4f5356-0980-43b4-bb50-4ada9bd8bd68}');
  define('BITBUCKET_REPOSITORY_BRANCH', 'dev');
