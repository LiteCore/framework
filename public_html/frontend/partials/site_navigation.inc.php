<?php

	$site_navigation = new ent_view('app://frontend/templates/'. settings::get('template') .'/partials/site_navigation.inc.php');

	$site_navigation_cache_token = cache::token('site_navigation', ['language']);
	if (!$site_navigation->snippets = cache::get($site_navigation_cache_token)) {

		$site_navigation->snippets = [
			'items' => [],
		];

		//$site_navigation->snippets['items'][] = [
		//	'id' => 'home',
		//	'name' => functions::draw_fonticon('fa-home'),
		//	'link' => document::ilink(''),
		//	'priority' => -1,
		//];

		//$site_navigation->snippets['items'][] = [
		//	'id' => '',
		//	'name' => 'Dropdown',
		//	'link' => '#',
		//	'priority' => -1,
		//	'subitems' => [],  <-- Put items in here
		//];

		$site_navigation->snippets['items'][] = [
			'id' => 'contact-us',
			'name' => language::translate('title_contact_us', 'Contact Us'),
			'link' => document::ilink('contact_us'),
			'priority' => 99,
		];

		// Sort menu items by priority
		uasort($site_navigation->snippets['items'], function($a, $b) {
			if (!isset($a['priority'])) $a['priority'] = 0;
			if (!isset($b['priority'])) $b['priority'] = 0;
			if ($a['priority'] == $b['priority']) return;
			return ($a['priority'] < $b['priority']) ? -1 : 1;
		});

		cache::set($site_navigation_cache_token, $site_navigation->snippets);
	}

	echo $site_navigation;
