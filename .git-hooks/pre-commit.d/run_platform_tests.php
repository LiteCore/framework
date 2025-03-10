<?php
	
	include_once __DIR__.'/../../public_html/includes/app_header.inc.php';
	
	ini_set('display_errors', 1);
	
	function check_if_similar($var1, $var2) {

		if (is_array($var1)) {

			foreach ($var1 as $key => $value) {
				if (is_array($value)) {
					if (!array_key_exists($key, $var2) || !is_array($var2[$key]) || !check_if_similar($value, $var2[$key])) {
						throw new Exception("1Arrays are not similar for key: " . $key);
					}
				}
			}

		} else {
			if ($var1 !== $var2) {
				throw new Exception('vars are not similar');
			}
		}

		return true;
	}

	$directory = functions::file_resolve_path(__DIR__.'/../../tests/');

	$files = functions::file_search($directory . '/*.php');

	echo 'Found '. count($files) . ' test files' . PHP_EOL;

	foreach ($files as $file) {

		echo 'Running tests from '. basename($file) .'...';

		$result = include $file;

		if ($result === true) {
			echo ' [OK]' . PHP_EOL;
		} else {
			echo ' [Failed]' . PHP_EOL;
			exit(1);
		}
	}
