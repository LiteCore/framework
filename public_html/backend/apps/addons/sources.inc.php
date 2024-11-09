<?php

	try {

		if (empty($_GET['pattern'])) {
			throw new Exception('Missing file');
		}

		$results = [];

		$skip_list = [
			'#.*(?<!\.inc\.php)$#',
			'#^assets/#',
			'#^index.php$#',
			'#^includes/app_header.inc.php$#',
			'#^includes/nodes/nod_vmod.inc.php$#',
			'#^includes/wrappers/wrap_app.inc.php$#',
			'#^includes/wrappers/wrap_storage.inc.php$#',
			'#^install/#',
			'#^storage/#',
		];

		$files = functions::file_search('app://' . $_GET['pattern'] .'*', GLOB_BRACE);

		foreach ($files as $file) {

			$relative_path = preg_replace('#^app://#', '', $file);

			foreach ($skip_list as $pattern) {
				if (preg_match($pattern, $relative_path)) continue 2;
			}

			$results[$relative_path] = file_get_contents($file);
		}

	} catch (Exception $e) {
		http_response_code(500);
		$results = [];
	}

	header('Content-Type: application/json');
	echo json_encode($results, JSON_UNESCAPED_SLASHES);
	exit;
