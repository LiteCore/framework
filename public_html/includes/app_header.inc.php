<?php
  define('PLATFORM_NAME', 'LiteBase');
  define('PLATFORM_VERSION', '1.0.0');
  define('SCRIPT_TIMESTAMP_START', microtime(true));

// Capture output buffer
  ob_start();

// Get config
  if (!defined('FS_DIR_APP')) {
    if (!file_exists(__DIR__ . '/../storage/config.inc.php')) {
      header('Location: ./install/');
      exit;
    }
    require_once __DIR__ . '/../storage/config.inc.php';
  }

// Virtual Modification System
  require_once FS_DIR_APP .'includes/wrappers/wrap_stream_app.inc.php';
  stream_wrapper_register('app', 'wrap_stream_app');

  require_once FS_DIR_APP .'includes/wrappers/wrap_stream_storage.inc.php';
  stream_wrapper_register('storage', 'wrap_stream_storage');

  require_once FS_DIR_APP .'includes/nodes/nod_vmod.inc.php';
  vmod::init();

// Compatibility and Polyfills
  require_once 'app://includes/compatibility.inc.php';

// 3rd party autoloader (If present)
  if (is_file('app://vendor/autoload.php')) {
    require_once 'app://vendor/autoload.php';
  }

// Autoloader
  require_once 'app://includes/autoloader.inc.php';

// Set error handler
  require_once 'app://includes/error_handler.inc.php';

  require_once 'app://includes/functions.inc.php';

// Jump-start some nodes
  class_exists('notices');
  class_exists('stats');
