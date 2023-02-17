<?php

  define('BACKEND_ALIAS', 'admin');

######################################################################
## Files and Directory  ##############################################
######################################################################

// File System
  define('DOCUMENT_ROOT',    str_replace('\\', '/', rtrim(realpath(!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : __DIR__.'/..'), '/')));

  define('FS_DIR_APP',       str_replace('\\', '/', rtrim(realpath(__DIR__.'/..'), '/')) . '/');
  define('FS_DIR_ASSETS',    FS_DIR_APP . 'assets/');
  define('FS_DIR_STORAGE',   FS_DIR_APP . 'storage/');
  define('FS_DIR_VENDOR',    FS_DIR_APP . 'vendor/');

// Web System
  define('WS_DIR_APP',       preg_replace('#^'. preg_quote(DOCUMENT_ROOT, '#') .'#', '', FS_DIR_APP));
  define('WS_DIR_ADMIN',     WS_DIR_APP . BACKEND_ALIAS . '/');
  define('WS_DIR_ASSETS',    WS_DIR_APP . 'assets/');
  define('WS_DIR_STORAGE',   WS_DIR_APP . 'storage/');
  define('WS_DIR_VENDOR',    WS_DIR_APP . 'vendor/');

######################################################################
## Database ##########################################################
######################################################################

// Database
  define('DB_TYPE', 'mysql');
  define('DB_SERVER', '127.0.0.1');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');
  define('DB_DATABASE', 'litebase');
  define('DB_TABLE_PREFIX', 'lb_');
  define('DB_CONNECTION_CHARSET', 'utf8mb4');

######################################################################
## Application #######################################################
######################################################################

// Errors
  error_reporting(version_compare(PHP_VERSION, '5.4.0', '>=') ? E_ALL & ~E_STRICT : E_ALL);
  ini_set('ignore_repeated_errors', 'On');
  ini_set('log_errors', 'On');
  ini_set('error_log', FS_DIR_STORAGE . 'logs/errors.log');
  ini_set('display_startup_errors', 'Off');
  ini_set('display_errors', 'Off');
  if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1'])) {
    error_reporting(E_ALL);
    ini_set('display_startup_errors', 'On');
    ini_set('display_errors', 'On');
  }
