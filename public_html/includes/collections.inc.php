<?php

/*
 * This file is a reference for the collection definitions used in the application.
 * It defines the collections, their names, entities, and how they are identified.
 *
 */

// Define collections
return [
	[
		'id' => 'administrators',
		'name' => t('title_administrators', 'Administrators'),
		'entity' => 'administrator',
		'identified_by' => ['id', 'username', 'email'],
		'translatable' => false,
	],
	[
		'id' => 'banners',
		'name' => t('title_banners', 'Banners'),
		'entity' => 'banner',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'countries',
		'name' => t('title_countries', 'Countries'),
		'entity' => 'country',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'currencies',
		'name' => t('title_currencies', 'Currencies'),
		'entity' => 'currency',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'emails',
		'name' => t('title_emails', 'Emails'),
		'entity' => 'email',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'languages',
		'name' => t('title_languages', 'Languages'),
		'entity' => 'language',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'modules',
		'name' => t('title_modules', 'Modules'),
		'entity' => 'translation',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'pages',
		'name' => t('title_pages', 'Pages'),
		'entity' => 'page',
		'identified_by' => ['id'],
		'translatable' => ['title', 'head_title', 'meta_description', 'content'],
	],
	[
		'id' => 'redirects',
		'name' => t('title_redirects', 'Redirects'),
		'entity' => 'redirect',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'site_tags',
		'name' => t('title_site_tags', 'Site Tags'),
		'entity' => 'site_tag',
		'identified_by' => ['id'],
		'translatable' => false,
	],
	[
		'id' => 'third_parties',
		'name' => t('title_third_parties', 'Third Parties'),
		'entity' => 'third_party',
		'identified_by' => ['id'],
		'translatable' => ['description', 'collected_data', 'purposes'],
	],
	[
		'id' => 'vmods',
		'name' => t('title_vmods', 'vMods'),
		'entity' => 'vmod',
		'identified_by' => ['id'],
		'translatable' => false,
	],
];
