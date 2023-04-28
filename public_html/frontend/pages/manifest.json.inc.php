<?php

  ob_clean();
  header('Content-Type: application/manifest+json; charset='. mb_http_output());

  $manifest = [
    'name' => settings::get('site_name'),
    'start_url' => document::ilink(''),
    'display' => 'standalone',
    'orientation' => 'landscape-primary',

    'icons' => [
      [
        'src' => document::rlink(FS_DIR_APP . 'images/favicons/favicon.ico'),
        'sizes' => '32x32 48x48 64x64 96x96',
        'type' => 'image/x-icon',
      ],
      [
        'src' => document::rlink(FS_DIR_APP . 'images/favicons/favicon-128x128.png'),
        'sizes' => '128x128',
        'type' => 'image/png',
      ],
      [
        'src' => document::rlink(FS_DIR_APP . 'images/favicons/favicon-192x192.png'),
        'sizes' => '192x192',
        'type' => 'image/png',
      ],
      [
        'src' => document::rlink(FS_DIR_APP . 'images/favicons/favicon-256x256.png'),
        'sizes' => '256x256',
        'type' => 'image/png',
      ],
    ],

    'shortcuts' => [
      //[
      //  'name' => 'Anypage',
      //  'url' => document::ilink('anypage'),
      //],
    ],
  ];

  echo json_encode($manifest,  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit; // As we don't need app_footer to process this with a template
