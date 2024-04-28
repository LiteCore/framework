<?php

	return [
		'name' => language::translate('title_addons', 'Add-ons'),
		'default' => 'addons',
		'priority' => 0,

		'theme' => [
			'color' => '#4dcac3',
			'icon' => 'fa-plug',
		],

		'menu' => [],

		'docs' => [
			'addons' => 'addons.inc.php',
			'download' => 'download.inc.php',
			'edit_addon' => 'edit_addon.inc.php',
			'installed' => 'installed.inc.php',
			'sources' => 'sources.inc.php',
		],
	];
