<?php

	return $app_config = [
		'name' => language::translate('title_modules', 'Modules'),
		'default' => 'jobs',
		'priority' => 0,

		'theme' => [
			'color' => '#c449c5',
			'icon' => 'fa-cube',
		],

		'menu' => [
			[
				'title' => language::translate('title_job_modules', 'Job Modules'),
				'doc' => 'jobs',
			],
		],

		'docs' => [
			'jobs' => 'modules.inc.php',
			'edit_job' => 'edit_module.inc.php',
			'run_job' => 'run_job.inc.php',
		],
	];
