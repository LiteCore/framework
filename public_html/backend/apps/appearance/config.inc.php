<?php

	return [
		'name' => t('title_appearance', 'Appearance'),
		'default' => 'edit_styling',
		'priority' => 0,

		'theme' => [
			'color' => '#e54d80',
			'icon' => 'icon-adjust',
		],

		'menu' => [
			[
				'title' => t('title_edit_styling', 'Edit Styling'),
				'doc' => 'edit_styling',
				'params' => [],
			],
			[
				'title' => t('title_favicon', 'Favicon'),
				'doc' => 'favicon',
				'params' => [],
			],
			[
				'title' => t('title_logotype', 'Logotype'),
				'doc' => 'logotype',
				'params' => [],
			],
			[
				'title' => t('title_template_settings', 'Template Settings'),
				'doc' => 'template_settings',
				'params' => [],
			],
		],

		'docs' => [
			'edit_styling' => 'edit_styling.inc.php',
			'favicon' => 'favicon.inc.php',
			'logotype' => 'logotype.inc.php',
			'template_settings' => 'template_settings.inc.php',
		],
	];
