<?php

  $box_site_menu = new ent_view(FS_DIR_TEMPLATE . 'partials/box_site_menu.inc.php');

  $box_site_menu_cache_token = cache::token('box_site_menu', ['language']);
  if (!$box_site_menu->snippets = cache::get($box_site_menu_cache_token)) {

    $box_site_menu->snippets = [
      'categories' => [],
      'brands' => [],
      'pages' => [],
    ];

  // Information pages

    $pages_query = database::query(
      "select p.id, p.priority, pi.title from ". DB_TABLE_PREFIX ."pages p
      left join ". DB_TABLE_PREFIX ."pages_info pi on (p.id = pi.page_id and pi.language_code = '". language::$selected['code'] ."')
      where status
      order by p.priority, pi.title;"
    );

    while ($page = database::fetch($pages_query)) {
      $box_site_menu->snippets['pages'][$page['id']] = [
        'type' => 'page',
        'id' => $page['id'],
        'title' => $page['title'],
        'link' => document::ilink('information', ['page_id' => $page['id']]),
        'priority' => $page['priority'],
      ];
    }

    cache::set($box_site_menu_cache_token, $box_site_menu->snippets);
  }

  echo $box_site_menu;
