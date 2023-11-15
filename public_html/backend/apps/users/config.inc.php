<?php

	return $app_config = [

		'name' => language::translate('title_users', 'Users'),
		'default' => 'users',
		'priority' => 0,

		'theme' => [
			'color' => '#21a261',
			'icon' => 'fa-user',
		],

		'menu' => [
			[
				'title' => language::translate('title_users', 'Users'),
				'doc' => 'users',
				'params' => [],
			],
			[
				'title' => language::translate('title_newsletter_recipients', 'Newsletter Recipients'),
				'doc' => 'newsletter_recipients',
				'params' => [],
			],
			[
				'title' => language::translate('title_csv_import_export', 'CSV Import/Export'),
				'doc' => 'csv',
				'params' => [],
			],
		],

		'docs' => [
			'user_picker' => 'user_picker.inc.php',
			'users' => 'users.inc.php',
			'users.json' => 'users.json.inc.php',
			'csv' => 'csv.inc.php',
			'edit_user' => 'edit_user.inc.php',
			'newsletter_recipients' => 'newsletter_recipients.inc.php',
		],

		'search_results' => 'search_results.inc.php',
	];
