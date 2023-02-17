<?php

  function draw_image($image, $width=null, $height=null, $clipping='fit', $parameters='') {

    if ($width && $height) {
      if (preg_match('#style="#', $parameters)) {
        $parameters = preg_replace('#style="(.*?)"#', 'style="$1 aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"', $parameters);
      } else {
        $parameters .= ' style="aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"';
      }
    }

    return '<img '. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="'. functions::escape_html($clipping) .'"' : '') .' src="'. document::href_link($image) .'" '. ($parameters ? ' '. $parameters : '') .' />';
  }

  function draw_thumbnail($source, $width, $height, $clipping='fit', $trim=false, $parameters='') {

    if (!is_file($source)) {
      $source = 'storage://images/no_image.png';
    }

    if (preg_match('#^'. preg_quote(FS_DIR_STORAGE, '#') .'#', $source)) {
      $storage = WS_DIR_STORAGE;
    } else {
      $storage = WS_DIR_APP;
    }

    $thumbnail = $storage . functions::image_thumbnail($source, $width, $height, settings::get('product_image_trim'));
    $thumbnail_2x = $storage . functions::image_thumbnail($source, $width*2, $height*2, settings::get('product_image_trim'));

    if ($width && $height) {
      if (preg_match('#style="#', $parameters)) {
        $parameters = preg_replace('#style="(.*?)"#', 'style="$1 aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"', $parameters);
      } else {
        $parameters .= ' style="aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"';
      }
    }

    return '<img '. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="'. functions::escape_html($clipping) .'"' : '') .' src="'. document::href_link($thumbnail) .'" srcset="1x '. document::href_link($thumbnail) .', 2x '. document::href_link($thumbnail_2x) .'"'. ($parameters ? ' '. $parameters : '') .' />';
  }

  function draw_fonticon($class, $parameters=null) {

    switch(true) {

    // Bootstrap Icons
      case (substr($class, 0, 3) == 'bi-'):
        document::$snippets['head_tags']['bootstrap-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />';
        return '<i class="bi '. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></i>';

    // Fontawesome 4
      case (substr($class, 0, 3) == 'fa-'):
        //document::$snippets['head_tags']['fontawesome'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css" />'; // Uncomment if removed from lib_document
        return '<i class="fa '. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></i>';

    // Foundation
      case (substr($class, 0, 3) == 'fi-'):
        document::$snippets['head_tags']['foundation-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/foundation-icons/latest/foundation-icons.min.css" />';
        return '<i class="'. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></i>';

    // Glyphicon
      case (substr($class, 0, 10) == 'glyphicon-'):
        //document::$snippets['head_tags']['glyphicon'] = '<link rel="stylesheet" href="'/path/to/glyphicon.min.css" />'; // Not embedded in release
        return '<span class="glyphicon '. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></span>';

    // Ion Icons
      case (substr($class, 0, 4) == 'ion-'):
        document::$snippets['head_tags']['ionicons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/ionicons/latest/css/ionicons.min.css" />';
        return '<i class="'. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></i>';

    // Material Design Icons
      case (substr($class, 0, 4) == 'mdi-'):
        document::$snippets['head_tags']['material-design-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" />';
        return '<i class="mdi '. $class .'"'. (!empty($parameters) ? ' ' . $parameters : null) .'></i>';
    }

    switch ($class) {
      case 'add':         return draw_fonticon('fa-plus');
      case 'cancel':      return draw_fonticon('fa-times');
      case 'edit':        return draw_fonticon('fa-pencil');
      case 'fail':        return draw_fonticon('fa-times', 'color: #c00;"');
      case 'folder':      return draw_fonticon('fa-folder', 'style="color: #cc6;"');
      case 'folder-open': return draw_fonticon('fa-folder-open', 'style="color: #cc6;"');
      case 'remove':      return draw_fonticon('fa-times', 'style="color: #c33;"');
      case 'delete':      return draw_fonticon('fa-trash-o');
      case 'move-up':     return draw_fonticon('fa-arrow-up', 'style="color: #39c;"');
      case 'move-down':   return draw_fonticon('fa-arrow-down', 'style="color: #39c;"');
      case 'ok':          return draw_fonticon('fa-check', 'style="color: #8c4;"');
      case 'on':          return draw_fonticon('fa-circle', 'style="color: #8c4;"');
      case 'off':         return draw_fonticon('fa-circle', 'style="color: #f64;"');
      case 'semi-off':    return draw_fonticon('fa-circle', 'style="color: #ded90f;"');
      case 'save':        return draw_fonticon('fa-floppy-o');
      case 'send':        return draw_fonticon('fa-paper-plane');
      case 'warning':     return draw_fonticon('fa-exclamation-triangle', 'color: #c00;"');
      default: trigger_error('Unknown font icon ('. $class .')', E_USER_WARNING); return;
    }
  }

  function draw_pagination($pages) {

    $pages = ceil($pages);

    if ($pages < 2) return false;

    if (empty($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1) $_GET['page'] = 1;

    if ($_GET['page'] > 1) document::$snippets['head_tags']['prev'] = '<link rel="prev" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']-1]) .'" />';
    if ($_GET['page'] < $pages) document::$snippets['head_tags']['next'] = '<link rel="next" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]) .'" />';
    if ($_GET['page'] < $pages) document::$snippets['head_tags']['prerender'] = '<link rel="prerender" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]) .'" />';

    $pagination = new ent_view(FS_DIR_TEMPLATE . 'partials/pagination.inc.php');

    $pagination->snippets['items'][] = [
      'page' => $_GET['page']-1,
      'title' => language::translate('title_previous', 'Previous'),
      'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']-1]),
      'disabled' => ($_GET['page'] <= 1) ? true : false,
      'active' => false,
    ];

    for ($i=1; $i<=$pages; $i++) {

      if ($i < $pages-5) {
        if ($i > 1 && $i < $_GET['page'] - 1 && $_GET['page'] > 4) {
          $rewind = round(($_GET['page'] - 1) / 2);
          $pagination->snippets['items'][] = [
            'page' => $rewind,
            'title' => ($rewind == $_GET['page']-2) ? $rewind : '...',
            'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $rewind]),
            'disabled' => false,
            'active' => false,
          ];
          $i = $_GET['page'] - 1;
          if ($i > $pages-4) $i = $pages-4;
        }
      }

      if ($i > 5) {
        if ($i > $_GET['page'] + 1 && $i < $pages) {
          $forward = round(($_GET['page']+1+$pages)/2);
          $pagination->snippets['items'][] = [
            'page' => $forward,
            'title' => ($forward == $_GET['page']+2) ? $forward : '...',
            'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $forward]),
            'disabled' => false,
            'active' => false,
          ];
          $i = $pages;
        }
      }

      $pagination->snippets['items'][] = [
        'page' => $i,
        'title' => $i,
        'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $i]),
        'disabled' => false,
        'active' => ($i == $_GET['page']) ? true : false,
      ];
    }

    $pagination->snippets['items'][] = [
      'page' => $_GET['page']+1,
      'title' => language::translate('title_next', 'Next'),
      'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]),
      'disabled' => ($_GET['page'] >= $pages) ? true : false,
      'active' => false,
    ];

    return (string)$pagination;
  }
