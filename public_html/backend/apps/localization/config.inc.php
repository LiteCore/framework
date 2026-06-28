<?php

	$config = [
		'name' => t('title_localization', 'Localization'),
		'group' => 'system',
		'default' => 'countries/countries',
		'priority' => 0,

		'theme' => [
			'color' => '#21a9d2',
			'icon' => 'icon-globe',
		],

		'menu' => [
			[
				'title' => t('title_languages', 'Languages'),
				'doc' => 'languages/languages',
				'params' => [],
			],
			[
				'title' => t('title_storage_encoding', 'Storage Encoding'),
				'doc' => 'languages/storage_encoding',
				'params' => [],
			],
			[
				'title' => t('title_translations', 'Translations'),
				'doc' => 'translations/translations',
				'params' => [],
			],
			[
				'title' => t('title_auto_translate', 'Auto Translate'),
				'doc' => 'translations/auto_translate',
				'params' => [],
			],
			[
				'title' => t('title_scan_for_translations', 'Scan For Translations'),
				'doc' => 'translations/scan',
				'params' => [],
			],
			[
				'title' => t('title_csv_import_export_translations', 'CSV Import/Export Translations'),
				'doc' => 'translations/csv',
				'params' => [],
			],
		],

		'docs' => [
			'languages/languages' => 'languages/languages.inc.php',
			'languages/edit_language' => 'languages/edit_language.inc.php',
			'languages/storage_encoding' => 'languages/storage_encoding.inc.php',
			'translations/translations' => 'translations/translations.inc.php',
			'translations/auto_translate' => 'translations/auto_translate.inc.php',
			'translations/translate.json' => 'translations/translate.json.inc.php',
			'translations/scan' => 'translations/scan.inc.php',
			'translations/csv' => 'translations/csv.inc.php',
		],
	];

	return $config;
