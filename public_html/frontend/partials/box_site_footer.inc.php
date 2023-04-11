<?php
  $box_site_footer_cache_token = cache::token('box_site_footer', ['language', 'login', 'region']);
  if (cache::capture($box_site_footer_cache_token)) {

    $box_site_footer = new ent_view(FS_DIR_TEMPLATE . 'partials/box_site_footer.inc.php');

    $box_site_footer->snippets = [
      'pages' => [],
      'modules' => [],
      'social' => [],
    ];

    $pages_query = database::query(
      "select p.id, pi.title from ". DB_TABLE_PREFIX ."pages p
      left join ". DB_TABLE_PREFIX ."pages_info pi on (p.id = pi.page_id and pi.language_code = '". database::input(language::$selected['code']) ."')
      where status
      order by p.priority, pi.title;"
    );

    while ($page = database::fetch($pages_query)) {
      $box_site_footer->snippets['pages'][$page['id']] = [
        'id' => $page['id'],
        'title' => $page['title'],
        'link' => document::href_ilink('information', ['page_id' => $page['id']]),
      ];
    }

    $box_site_footer->snippets['social']['facebook'] = [
      'type' => 'facebook',
      'title' => 'Facebook',
      'icon' => 'fa-facebook',
      'link' => 'https://www.facebook.com/',
    ];

    $box_site_footer->snippets['social']['twitter'] = [
      'type' => 'twitter',
      'title' => 'Twitter',
      'icon' => 'fa-twitter',
      'link' => 'https://www.twitter.com/',
    ];

    $box_site_footer->snippets['social']['linkedin'] = [
      'type' => 'linkedin',
      'title' => 'LinkedIn',
      'icon' => 'fa-linkedin',
      'link' => 'https://www.linkedin.com/',
    ];

    echo $box_site_footer;

    cache::end_capture($box_site_footer_cache_token);
  }
