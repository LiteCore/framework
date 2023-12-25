<?php
	$site_footer_cache_token = cache::token('site_footer', ['language', 'login', 'region']);
	if (cache::capture($site_footer_cache_token)) {

		$site_footer = new ent_view('app://frontend/templates/'.settings::get('template').'/partials/site_footer.inc.php');

		$site_footer->snippets = [
			'pages' => [],
			'modules' => [],
			'social' => [],
		];

		$site_footer->snippets['social'] = [
			[
				'type' => 'facebook',
				'title' => 'Facebook',
				'icon' => 'fa-facebook',
				'link' => 'https://www.facebook.com/',
			],
			[
				'type' => 'twitter',
				'title' => 'Twitter',
				'icon' => 'fa-twitter',
				'link' => 'https://www.twitter.com/',
			],
			[
				'type' => 'linkedin',
				'title' => 'LinkedIn',
				'icon' => 'fa-linkedin',
				'link' => 'https://www.linkedin.com/',
			]
		];

		echo $site_footer;

		cache::end_capture($site_footer_cache_token);
	}
