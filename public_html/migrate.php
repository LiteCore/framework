<?php

	require_once __DIR__.'/includes/app_header.inc.php';

	ini_set('display_errors', 'on');
	header('Content-Type: text/plain');

	$files = glob(__DIR__.'/../migrations/*.{php,sql}', GLOB_BRACE);
	sort($files);

	$migrated = preg_split('#\R+#', @file_get_contents(__DIR__.'/../migrations/.migrated'));

	foreach ($files as $file) {

		$filename = pathinfo($file, PATHINFO_BASENAME);
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		if (in_array($filename, $migrated)) {
			echo 'Skipping '. $filename . PHP_EOL;
			continue;
		}

		echo 'Processing migration patch ' . $file . PHP_EOL;

		switch (strtolower($extension)) {

			case 'php':
				include $file;
				break;

			case 'sql':

				try {

					database::query("start transaction;");

					$sql = file_get_contents($file);
					$sql = str_replace('`lc_', '`'.DB_TABLE_PREFIX, $sql);

					foreach (preg_split('#^-- -----+$#m', $sql, -1, PREG_SPLIT_NO_EMPTY) as $query) {
						$query = preg_replace('#--.*\s#', '', $query);
						database::query($query);
					}

					database::query("commit;");

				} catch (Exception $e) {
					echo '  Error: '. $e->getMessage();
					echo 'Rolling back MySQL transaction' . PHP_EOL;
					database::query('rollback;');
					exit;
				}

				break;
		}

		file_put_contents(__DIR__.'/../migrations/.migrated', $filename . PHP_EOL, FILE_APPEND);
	}
