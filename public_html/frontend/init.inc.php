<?php

	// Set Default OpenGraph Content
	document::$opengraph = [
		'title' => settings::get('site_name'),
		'type' => 'website',
		'url' => document::href_ilink(''),
		'image' => document::href_rlink('storage://images/logotype.svg'),
	];

	// Set Default Schema Data
	document::$schema['website'] = [
		'@context' => 'https://schema.org/',
		'@type' => 'Website',
		'name' => settings::get('site_name'),
		'url' => document::ilink(''),
		'countryOfOrigin' => settings::get('site_country_code'),
	];

	// Set Default Organization Schema Data
	document::$schema['organization'] = [
		'@context' => 'https://schema.org/',
		'@type' => 'Organization',
		'name' => settings::get('site_name'),
		'url' => document::ilink(''),
		'logo' => document::rlink(FS_DIR_STORAGE . 'images/logotype.svg'),
		'email' => settings::get('site_email'),
		'availableLanguage' => array_column(language::$languages, 'name'),
	];

	// Favicons
	document::$head_tags['favicon'] = implode(PHP_EOL, [
		'<link rel="icon" href="'. document::href_rlink('storage://images/favicons/favicon.ico') .'" type="image/x-icon" sizes="32x32 48x48 64x64 96x96">',
		'<link rel="icon" href="'. document::href_rlink('storage://images/favicons/favicon-128x128.png') .'" type="image/png" sizes="128x128">',
		'<link rel="icon" href="'. document::href_rlink('storage://images/favicons/favicon-192x192.png') .'" type="image/png" sizes="192x192">',
		'<link rel="icon" href="'. document::href_rlink('storage://images/favicons/favicon-256x256.png') .'" type="image/png" sizes="256x256">',
	]);
	// Maintenance Mode
	if (settings::get('maintenance_mode')) {
		
		if (!in_array(route::$selected['resource'], [
			'f:invoice/edit',
		])) {
			
			if (administrator::check_login()) {
				
				notices::add('notices', strtr('%message [<a href="%link">%preview</a>]', [
					'%message' => language::translate('reminder_site_in_maintenance_mode', 'The site is in maintenance mode.'),
					'%preview' => language::translate('title_preview', 'Preview'),
					'%link' => document::href_ilink('maintenance_mode'),
				]), 'maintenance_mode');
				
			} else {
				http_response_code(503);
				include 'app://frontend/pages/maintenance_mode.inc.php';
				require_once 'app://includes/app_footer.inc.php';
				exit;
			}
		}
	}

	document::$head_tags['manifest'] = '<link rel="manifest" href="'. document::href_ilink('webmanifest.json') .'">';
