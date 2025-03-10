<?php

	require_once __DIR__.'/includes/app_header.inc.php';

	ob_start();

	try {

		// Validate request
		if (!$input = json_decode(file_get_contents('php://input'), true)) {
			throw new Exception('Not a valid webhook request');
		}
	
		file_put_contents(FS_DIR_STORAGE . 'logs/last_deploy_request.log', file_get_contents('php://input'));

		switch (true) {
			
			// Handle an expected Bitbucket webhook request
			case (defined('BITBUCKET_REPOSITORY_UUID')):
			
				if (empty($input['push']['changes'][0]['new']['name'])) {
					throw new Exception('Not a valid resource');
				}
		
				if ($input['repository']['uuid'] != BITBUCKET_REPOSITORY_UUID) {
					throw new Exception('Not a recognized repository ('. $input['repository']['uuid'] .')');
				}
		
				$is_monitored_branch = false;
				foreach (array_keys($input['push']['changes']) as $key) {
					if ($input['push']['changes'][0]['new']['name'] == BITBUCKET_REPOSITORY_BRANCH) {
						$is_monitored_branch = true;
						break;
					}
				}
		
				if (!$is_monitored_branch) {
					throw new Exception('Not monitoring other branches than '. BITBUCKET_REPOSITORY_BRANCH);
				}
			
				break;
		
			// Handle an expected Github webhook request
			case (defined('GITHUB_REPOSITORY_NAME')):
			
				if (!isset($input['repository']['full_name']) || $input['repository']['full_name'] != GITHUB_REPOSITORY_NAME) {
					throw new Exception('Not a recognized repository ('. $input['repository']['full_name'] .')');
				}
		
				if (!isset($input['repository']['default_branch']) || $input['repository']['default_branch'] != GITHUB_REPOSITORY_BRANCH) {
					throw new Exception('Not monitoring other branches than '. GITHUB_REPOSITORY_BRANCH);
				}
			
				break;
				
			default:
				throw new Exception('No repository sources configured');
		}

		// Pull git commits
		echo 'Pulling changes from repository...' . PHP_EOL;

		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			echo shell_exec('C:\\Program\\Git\\cmd\\git.exe pull 2>&1') . PHP_EOL;
		} else {
			echo shell_exec('git pull 2>&1') . PHP_EOL;
		}

		// Run migrations
		include 'migrate.php';

	} catch (Exception $e) {
		echo $e->getMessage();
	}

	$output = ob_get_clean();

	file_put_contents(FS_DIR_STORAGE . 'logs/last_deploy.log', $output);

	header('Content-Type: text/plain');
	echo $output;
