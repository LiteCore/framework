<?php

	// CI helper: Create tables from structure.json and import seed data

	$link = new mysqli(
		getenv('DB_SERVER') ?: '127.0.0.1',
		getenv('DB_USERNAME') ?: 'litecore',
		getenv('DB_PASSWORD') ?: 'litecore',
		getenv('DB_DATABASE') ?: 'litecore'
	);

	$link->set_charset('utf8mb4');

	$prefix = getenv('DB_TABLE_PREFIX') ?: 'lc_';

	// Create tables from structure.json
	$structure = json_decode(file_get_contents(__DIR__ . '/../../install/structure.json'), true);

	if (empty($structure['tables'])) {
		echo 'ERROR: No tables found in structure.json' . PHP_EOL;
		exit(1);
	}

	foreach ($structure['tables'] as $key => $table) {

		$name = $prefix . $key;
		$cols = [];

		foreach ($table['columns'] as $col => $def) {
			$type = $def['type'];
			if (stripos($type, 'ENUM') === 0 || stripos($type, 'SET') === 0) {
				// ENUM/SET already includes values in type string
			} elseif (!empty($def['length'])) {
				$type .= '(' . $def['length'] . ')';
			}
			if (!empty($def['unsigned'])) $type .= ' UNSIGNED';

			$null = (!isset($def['null']) || $def['null']) ? '' : ' NOT NULL';

			$default = '';
			if (array_key_exists('default', $def)) {
				$d = $def['default'];
				if (is_null($d)) {
					$default = ' DEFAULT NULL';
				} elseif (is_numeric($d)) {
					$default = ' DEFAULT ' . $d;
				} elseif (preg_match('/^(CURRENT_TIMESTAMP|current_timestamp\(\)|NOW\(\))$/i', $d)) {
					$default = ' DEFAULT ' . $d;
				} elseif (preg_match("/^'.*'$/", $d)) {
					$default = ' DEFAULT ' . $d; // Already quoted (e.g. ENUM defaults)
				} else {
					$default = " DEFAULT '" . $link->real_escape_string($d) . "'";
				}
			}

			$auto = !empty($def['auto_increment']) ? ' AUTO_INCREMENT' : '';
			$cols[] = "`$col` $type$null$default$auto";
		}

		if (!empty($table['primary_key'])) {
			$pk = is_array($table['primary_key']) ? $table['primary_key'] : [$table['primary_key']];
			$cols[] = 'PRIMARY KEY (`' . implode('`,`', $pk) . '`)';
		}

		$sql = "CREATE TABLE IF NOT EXISTS `$name` (" . implode(', ', $cols) . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

		if (!$link->query($sql)) {
			echo "ERROR creating $name: " . $link->error . PHP_EOL;
			echo "SQL: $sql" . PHP_EOL;
			exit(1);
		}
	}

	echo count($structure['tables']) . ' tables created' . PHP_EOL;

	// Import seed data
	$data = file_get_contents(__DIR__ . '/../../install/data.sql');
	$data = str_replace('`lc_', '`' . $prefix, $data);
	$link->multi_query($data);
	while ($link->next_result()) {}
	echo 'Seed data imported' . PHP_EOL;

	// Create admin user
	$hash = password_hash('admin123456', PASSWORD_DEFAULT);
	$link->query("INSERT INTO {$prefix}administrators (status, username, password_hash) VALUES (1, 'admin', '$hash')");
	echo 'Admin user created' . PHP_EOL;
