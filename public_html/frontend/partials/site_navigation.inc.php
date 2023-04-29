<?php

	$site_navigation = new ent_view(FS_DIR_TEMPLATE . 'partials/site_navigation.inc.php');

	$site_navigation_cache_token = cache::token('site_navigation', ['language']);
	if (!$site_navigation->snippets = cache::get($site_navigation_cache_token)) {

		$site_navigation->snippets = [
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
			$site_navigation->snippets['pages'][$page['id']] = [
				'type' => 'page',
				'id' => $page['id'],
				'title' => $page['title'],
				'link' => document::ilink('information', ['page_id' => $page['id']]),
				'priority' => $page['priority'],
			];
		}

		cache::set($site_navigation_cache_token, $site_navigation->snippets);
	}

	echo $site_navigation;
