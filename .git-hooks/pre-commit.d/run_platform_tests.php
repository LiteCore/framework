<?php

	define('VMOD_DISABLED', true);

	include_once __DIR__.'/../../public_html/includes/app_header.inc.php';

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$directory = functions::file_resolve_path(__DIR__.'/../../tests/');

	$files = functions::file_search($directory . '/*.php');

	echo 'Found '. count($files) . ' test files' . PHP_EOL;
	echo implode(PHP_EOL, array_map(function($file) {
		return ' - '. basename($file);
	}, $files)) . PHP_EOL;

	$failed = 0;

	foreach ($files as $file) {

		echo 'Running tests from '. basename($file) .'...';

		try {

			$result = require $file;

			if ($result === true) {
				echo ' [OK]' . PHP_EOL;
			} else {
				echo ' [FAIL]' . PHP_EOL;
				$failed++;
			}

		} catch (Error $e) {
			echo ' [ERROR] ' . $e->getMessage() .' in '. $e->getFile() .' on line '. $e->getLine() . PHP_EOL;
			$failed++;

		} catch (Exception $e) {
			echo ' [EXCEPTION] ' . $e->getMessage() . PHP_EOL;
			$failed++;
		}
	}

	if ($failed > 0) {
		echo PHP_EOL . $failed . ' test(s) failed' . PHP_EOL;
		exit(1);
	}

	echo PHP_EOL . 'All tests passed' . PHP_EOL;
