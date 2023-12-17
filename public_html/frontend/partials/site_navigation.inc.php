<?php

	$site_navigation = new ent_view(FS_DIR_TEMPLATE . 'partials/site_navigation.inc.php');

	$site_navigation_cache_token = cache::token('site_navigation', ['language']);
	if (!$site_navigation->snippets = cache::get($site_navigation_cache_token)) {

		$site_navigation->snippets = [
			'items' => [],
		];



		cache::set($site_navigation_cache_token, $site_navigation->snippets);
	}

	echo $site_navigation;
