<?php

	ignore_user_abort(true);
	@set_time_limit(300);

	header('X-Robots-Tag: noindex');
	header('Content-type: text/plain; charset='. mb_http_output());

	if ($last_run = settings::get('jobs_last_run')) {
		$last_run = strtotime($last_run);
		if (date('Ymdh', $last_run) == date('Ymdh') && floor(date('i', $last_run)/5) == floor(date('i')/5)) {
			die('Zzz...');
		}
	}

	session::close();

	database::query(
		"update ". DB_TABLE_PREFIX ."settings
		set value = '". date('Y-m-d H:i:s') ."'
		where `key` = 'jobs_last_run'
		limit 1;"
	);

	$jobs = new mod_jobs();

	if (!empty($_GET['module_id'])) {
		$jobs->process($_GET['module_id']);
	} else {
		$jobs->process();
	}

	echo 'OK';
	exit;
