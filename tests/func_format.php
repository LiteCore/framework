<?php

  include_once __DIR__.'/../public_html/includes/app_header.inc.php';

  try {

    ########################################################################
    ## format_json — pretty print
    ########################################################################

    $data = ['name' => 'Test', 'value' => 42];
    $json = f::format_json($data);

    if (strpos($json, "\t") === false) {
      throw new Exception('format_json with indent should contain tabs');
    }

    $decoded = json_decode($json, true);
    if ($decoded['name'] !== 'Test' || $decoded['value'] !== 42) {
      throw new Exception('format_json output not valid JSON after decode');
    }

    ########################################################################
    ## format_json — compact (no indent)
    ########################################################################

    $json_compact = f::format_json($data, false);

    if (strpos($json_compact, "\t") !== false || strpos($json_compact, "\n") !== false) {
      throw new Exception('format_json without indent should not contain tabs or newlines');
    }

    ########################################################################
    ## format_path_friendly — basic slug
    ########################################################################

    $slug = f::format_path_friendly('Hello World 123');

    if ($slug !== 'hello-world-123') {
      throw new Exception('format_path_friendly basic failed: got "'. $slug .'"');
    }

    ########################################################################
    ## format_path_friendly — special characters
    ########################################################################

    $slug = f::format_path_friendly('Über Straße & Café');

    if (empty($slug) || strpos($slug, '&') !== false) {
      throw new Exception('format_path_friendly should strip special chars: got "'. $slug .'"');
    }

    ########################################################################
    ## format_path_friendly — German umlauts
    ########################################################################

    $slug = f::format_path_friendly('Ärger mit Öl', 'de');

    if (strpos($slug, 'aerger') === false) {
      throw new Exception('format_path_friendly German should convert Ä to Ae: got "'. $slug .'"');
    }

    return true;

  } catch (Exception $e) {

    echo ' [Failed]'. PHP_EOL . 'Error: '. $e->getMessage();
    return false;
  }
