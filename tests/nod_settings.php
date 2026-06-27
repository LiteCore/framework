<?php

  include_once __DIR__.'/../public_html/includes/app_header.inc.php';

  try {

    ########################################################################
    ## settings::get — known setting
    ########################################################################

    $site_name = settings::get('site_name');

    if (empty($site_name)) {
      throw new Exception('settings::get failed to retrieve site_name');
    }

    ########################################################################
    ## settings::get — default language code
    ########################################################################

    $language_code = settings::get('default_language_code');

    if (empty($language_code)) {
      throw new Exception('settings::get returned empty default_language_code');
    }

    ########################################################################
    ## settings::get — nonexistent key with fallback
    ########################################################################

    $result = settings::get('nonexistent_setting_key_12345', 'default_value');

    if ($result !== 'default_value') {
      throw new Exception('settings::get fallback failed: got '. var_export($result, true));
    }

    ########################################################################
    ## settings::set — temporary override
    ########################################################################

    $original = settings::get('site_name');
    settings::set('site_name', 'Test Site Override');

    if (settings::get('site_name') !== 'Test Site Override') {
      throw new Exception('settings::set failed to override value');
    }

    // Restore
    settings::set('site_name', $original);

    if (settings::get('site_name') !== $original) {
      throw new Exception('settings::set failed to restore original value');
    }

    return true;

  } catch (Exception $e) {

    echo ' [Failed]'. PHP_EOL . 'Error: '. $e->getMessage();
    return false;
  }
