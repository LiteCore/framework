<?php

	return [
		'name' => t('title_modules', 'Modules'),
		'group' => 'system',
		'default' => 'customer',
		'priority' => 0,

		'theme' => [
			'color' => '#c449c5',
			'icon' => 'icon-cube',
		],

		'menu' => [
			[
				'title' => t('title_job_modules', 'Job Modules'),
				'doc' => 'jobs',
			],
			[
				'title' => t('title_translation', 'Translation'),
				'doc' => 'translation',
			],
		],

		'docs' => [
			'jobs' => 'modules.inc.php',
			'translation' => 'translation.inc.php',
			'edit_job' => 'edit_module.inc.php',
			'run_job' => 'run_job.inc.php',
		],
	];
