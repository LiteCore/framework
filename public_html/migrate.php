<?php

	header('Content-Type: text/plain');

	require_once __DIR__.'/includes/app_header.inc.php';

	set_time_limit(0);
	ini_set('display_errors', 'on');

	// Get all existing tables
	$existing_tables = database::query(
		"select table_name from information_schema.tables
		where table_schema = '". DB_DATABASE ."';"
	)->fetch_all('table_name');

	// Lock all existing tables for write access during migration
	database::query("LOCK TABLES ". implode(', ', array_map(function($table) {
		return '`'. database::input($table) .'` WRITE';
	}, $existing_tables)) .";");

	register_shutdown_function(function() {
		// Always release locks on shutdown, even on error
		database::query('UNLOCK TABLES;');
	});

	try {

		// Apply migration patches

	$migrated = preg_split('#\R+#', @file_get_contents(__DIR__.'/../migrations/.migrated'));

	$files = glob(__DIR__.'/../migrations/*.{php,sql}', GLOB_BRACE);
	sort($files);

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
					$sql = preg_replace('#``(lchq|lc)_#', '`' . DB_TABLE_PREFIX, $sql);
					$sql = preg_split('#(^|\R)-- -----+(\R|$)#', $sql, -1, PREG_SPLIT_NO_EMPTY);

					foreach ($sql as $query) {

						// Remove comment lines (lines starting with --)
						$query = preg_replace('#^--.*$#m', '', $query);

						// Trim whitespace
						$query = trim($query);

						// Execute query
						if ($query != '') {
							echo 'Executing query...' . PHP_EOL;
						database::query($query);
					}
				}

				break;
		}

			// Mark migration patch as applied
			file_put_contents('../migrations/.migrated', $filename . PHP_EOL, FILE_APPEND);
		}

		## Table Structures #####

		echo 'Update table structures...' . PHP_EOL;

		$default_collation = database::query(
			"SELECT DEFAULT_COLLATION_NAME
			FROM INFORMATION_SCHEMA.SCHEMATA
			WHERE SCHEMA_NAME = '". database::input(DB_DATABASE) ."';"
		)->fetch('DEFAULT_COLLATION_NAME');

		// Fetch MySQL table structures from structure.json
		$structure = json_decode(file_get_contents(__DIR__.'/../structure.json'), true);


		if ($structure === null) {
			throw new Exception('structure.json could not be decoded: ' . json_last_error_msg());
		}

		if (empty($structure['tables'])) {
			throw new Exception('structure.json does not contain any tables.');
		}

		// Assign table name with table prefix
		foreach ($structure['tables'] as $key => $table) {
			$structure['tables'][$key]['name'] = DB_TABLE_PREFIX . $key;

			// Assign table prefix to foreign key references
			if (!empty($table['foreign_keys'])) {
				foreach ($table['foreign_keys'] as $fk_key => $fk) {
					$structure['tables'][$key]['foreign_keys'][$fk_key]['references']['table'] = DB_TABLE_PREFIX . $fk['references']['table'];
				}
			}
		}

		## Table Structures #####

		// Temporarily disable foreign key checks for table conversions
		database::query("SET FOREIGN_KEY_CHECKS=0;");

		// Iterate through each table (this is to ensure specific table properties)
		foreach ($structure['tables'] as $table) {

			// If table exists
			if (in_array($table['name'], $existing_tables)) {

				// Get existing table properties
				$existing_table = database::query(
					"SHOW TABLE STATUS LIKE '". database::input($table['name']) ."';"
				)->fetch();

				// Convert engine and row format if needed
				if ($existing_table['Engine'] != 'InnoDB' || $existing_table['Row_format'] != 'Dynamic') {
					database::query(
						"ALTER TABLE `". database::input($table['name']) ."`
						ENGINE='InnoDB' ROW_FORMAT=DYNAMIC;"
					);
				}

				// Convert charset and collation if needed
				if ($existing_table['Create_options'] != 'CHARSET=utf8mb4' || $existing_table['Collation'] != $default_collation) {
					database::query(
						"ALTER TABLE `". database::input($table['name']) ."`
						CONVERT TO CHARACTER SET utf8mb4 COLLATE ". database::input($default_collation) .";"
					);
				}
			}
		}

		// Re-enable foreign key checks
		database::query("SET FOREIGN_KEY_CHECKS=1;");

		## #####

		// Drop all foreign keys before making alterations
		echo 'Dropping foreign keys...' . PHP_EOL;

		$foreign_keys = [];

		database::query(
			"SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
			FROM information_schema.KEY_COLUMN_USAGE
			WHERE TABLE_SCHEMA = '". database::input(DB_DATABASE) ."'
			AND REFERENCED_TABLE_NAME IS NOT NULL
			ORDER BY TABLE_NAME, CONSTRAINT_NAME;"
		)->each(function($fk) use (&$foreign_keys) {

			// Store foreign key definition for later recreation
			if (!isset($foreign_keys[$fk['TABLE_NAME']][$fk['CONSTRAINT_NAME']])) {
				$foreign_keys[$fk['TABLE_NAME']][$fk['CONSTRAINT_NAME']] = [
					'columns' => [],
					'referenced_table' => $fk['REFERENCED_TABLE_NAME'],
					'referenced_columns' => []
				];
			}

			$foreign_keys[$fk['TABLE_NAME']][$fk['CONSTRAINT_NAME']]['columns'][] = $fk['COLUMN_NAME'];
			$foreign_keys[$fk['TABLE_NAME']][$fk['CONSTRAINT_NAME']]['referenced_columns'][] = $fk['REFERENCED_COLUMN_NAME'];

			// Drop the foreign key
			database::query(
				"ALTER TABLE `". database::input($fk['TABLE_NAME']) ."`
				DROP FOREIGN KEY `". database::input($fk['CONSTRAINT_NAME']) ."`;"
			);
		});

		## #####

		// Iterate through each table and add/change columns and keys
		foreach ($structure['tables'] as $table) {

			if (empty($table['columns'])) {
				throw new Exception('Table structure for '. $table['name'] .' in structure.json does not contain any columns');
			}

			if (in_array($table['name'], $existing_tables)) {
				$table_exists = true;
			} else {
				$table_exists = false;
			}

			if ($table_exists) {
				$sql = 'ALTER TABLE `'. $table['name'] .'`' . PHP_EOL;
			} else {
				$sql = 'CREATE TABLE `'. $table['name'] .'` (' . PHP_EOL;
			}

			$last_column = null;

			// Drop primary key
			if ($table_exists && !empty($table['primary_key'])) {
				if (database::query(
					"SHOW INDEX FROM `". $table['name'] ."`
					WHERE Key_name = 'PRIMARY'
					/*AND non_unique = 0*/;"
				)->num_rows) {
					$sql .= 'DROP PRIMARY KEY,' . PHP_EOL;
				}
			}

			// Drop unique keys
			if ($table_exists && !empty($table['unique_keys'])) {
				foreach (array_keys($table['unique_keys']) as $key_name) {
					if (database::query(
						"SHOW INDEX FROM `". $table['name'] ."`
						WHERE Key_name = '". database::input($key_name) ."'
						AND non_unique = 0;"
					)->num_rows) {
						$sql .= 'DROP INDEX `'. database::input($key_name) .'`,' . PHP_EOL;
					}
				}
			}

			// Drop keys
			if ($table_exists && !empty($table['keys'])) {
				foreach (array_keys($table['keys']) as $key_name) {
					if (database::query(
						"SHOW INDEX FROM `". database::input($table['name']) ."`
						WHERE Key_name = '". database::input($key_name) ."'
						AND non_unique = 1;"
					)->num_rows) {
						$sql .= 'DROP INDEX `'. database::input($key_name) .'`,' . PHP_EOL;
					}
				}
			}

			// Drop fulltext keys
			if ($table_exists && !empty($table['fulltext_keys'])) {
				foreach (array_keys($table['fulltext_keys']) as $key_name) {
					if (database::query(
						"SHOW INDEX FROM `". database::input($table['name']) ."`
						WHERE Key_name = '". database::input($key_name) ."'
						AND Index_type = 'FULLTEXT';"
					)->num_rows) {
						$sql .= 'DROP INDEX `'. database::input($key_name) .'`,' . PHP_EOL;
					}
				}
			}

			// Drop check constraints
			if ($table_exists && !empty($table['check_constraints'])) {
				foreach (array_keys($table['check_constraints']) as $name) {
					if (database::query(
						"SELECT CONSTRAINT_NAME
						FROM information_schema.table_constraints
						WHERE CONSTRAINT_SCHEMA = '". database::input(DB_DATABASE) ."'
						AND TABLE_NAME = '". database::input($table['name']) ."'
						AND CONSTRAINT_NAME = '". database::input($name) ."';"
					)->num_rows) {
						$sql .= 'DROP CONSTRAINT `'. database::input($name) .'`,' . PHP_EOL;
					}
				}
			}

			// Fix invalid JSON data before adding constraints
			if ($table_exists && !empty($table['check_constraints'])) {
				foreach ($table['check_constraints'] as $name => $expression) {

					// Extract column name from JSON_VALID(column_name) expression
					if (preg_match('/JSON_VALID\((\w+)\)/', $expression, $matches)) {

						$column_name = $matches[1];

						if (isset($table['columns'][$column_name])) {

							$column_definition = $table['columns'][$column_name];
							$default_literal = isset($column_definition['default']) ? trim($column_definition['default']) : null;
							$replacement_value = "'{}'";

							if ($default_literal !== null) {

								if (strcasecmp($default_literal, 'NULL') === 0) {
									$replacement_value = 'NULL';
								} elseif (strpos($default_literal, '[]') !== false) {
									$replacement_value = "'[]'";
								} elseif (strpos($default_literal, '{}') !== false) {
									$replacement_value = "'{}'";
								}

							} else if (!empty($column_definition['nullable'])) {
								$replacement_value = 'NULL';
							}

							$set_clause = $replacement_value === 'NULL'
								? "SET `". database::input($column_name) ."` = NULL"
								: "SET `". database::input($column_name) ."` = ". $replacement_value;

							$conditions = [
								"`". database::input($column_name) ."` IS NULL",
								"TRIM(`". database::input($column_name) ."`) = ''",
								"NOT JSON_VALID(`". database::input($column_name) ."`)"
							];

							database::query(
								"UPDATE `". database::input($table['name']) ."`
								". $set_clause ."
								WHERE (". implode(' OR ', $conditions) .")"
							);
						}
					}
				}
			}

			// Add/change columns
			foreach ($table['columns'] as $column_name => $column) {

				if ($table_exists && database::query(
					"SHOW COLUMNS FROM `". database::input($table['name']) ."`
					LIKE '". database::input($column_name) ."';"
				)->num_rows) {
					$column_exists = true;
				} else {
					$column_exists = false;
				}

				if ($table_exists) {

					if ($column_exists) {
						$sql .= 'CHANGE COLUMN `'. $column_name .'` `'. $column_name .'` '. $column['type'];
					} else {
						$sql .= 'ADD COLUMN `'. $column_name .'` '. $column['type'];
					}

				} else {
					$sql .= '  `'. $column_name .'` '. $column['type'];
				}

				if (isset($column['length'])) {
					$sql .= '('. $column['length'] .')';
				}

				if (isset($column['unsigned']) && $column['unsigned'] === true) {
					$sql .= ' UNSIGNED';
				}

				if (!empty($column['nullable'])) {
					$sql .= ' NULL';
				} else {
					$sql .= ' NOT NULL';
				}

				if (isset($column['auto_increment']) && $column['auto_increment'] === true) {
					$sql .= ' AUTO_INCREMENT';
				}

				if (isset($column['default'])) {
					$sql .= ' DEFAULT '. $column['default'] .'';
				}

				if (!empty($column['on_update'])) {
					$sql .= ' ON UPDATE '. $column['on_update'];
				}

				if (isset($column['comment'])) {
					$sql .= ' COMMENT ' . "'" . database::input($column['comment']) . "'";
				}

				if ($table_exists && !$column_exists && $last_column) {
					$sql .= ' AFTER `'. $last_column .'`';
				}

				$sql .= ',' . PHP_EOL;

				$last_column = $column_name;
			}

			// Create primary key
			if (!empty($table['primary_key'])) {
				if ($table_exists) {
					$sql .= 'ADD PRIMARY KEY (`'. implode('`, `', database::input($table['primary_key'])) .'`),' . PHP_EOL;
				} else {
					$sql .= '  PRIMARY KEY (`'. implode('`, `', database::input($table['primary_key'])) .'`),' . PHP_EOL;
				}
			}

			// Create unique keys
			if (!empty($table['unique_keys'])) {
				foreach ($table['unique_keys'] as $key_name => $key_columns) {
					if ($table_exists) {
						$sql .= 'ADD CONSTRAINT `'. database::input($key_name) .'` UNIQUE (`'. implode('`, `', database::input($key_columns)) .'`),' . PHP_EOL;
					} else {
						$sql .= '  CONSTRAINT `'. database::input($key_name) .'` UNIQUE (`'. implode('`, `', database::input($key_columns)) .'`),' . PHP_EOL;
					}
				}
			}

			// Create fulltext keys
			if (isset($table['fulltext_keys'])) {
				foreach ($table['fulltext_keys'] as $key_name => $key_columns) {
					if ($table_exists) {
						$sql .= 'ADD FULLTEXT KEY `'. database::input($key_name) .'` (`'. implode('`, `', database::input($key_columns)) .'`),' . PHP_EOL;
					} else {
						$sql .= '  FULLTEXT KEY `' . database::input($key_name) . '` (`' . implode('`, `', database::input($key_columns)) . '`),' . PHP_EOL;
					}
				}
			}

			// Create keys
			if (!empty($table['keys'])) {
				foreach ($table['keys'] as $key_name => $key_columns) {
					if ($table_exists) {
						$sql .= 'ADD KEY `'. database::input($key_name) .'` (`'. implode('`, `', database::input($key_columns)) .'`),' . PHP_EOL;
					} else {
						$sql .= '  KEY `'. database::input($key_name) .'` (`'. implode('`, `', database::input($key_columns)) .'`),' . PHP_EOL;
					}
				}
			}

			// Create check constraints
			if (!empty($table['check_constraints'])) {
				foreach ($table['check_constraints'] as $name => $expression) {
					if ($table_exists) {
						$sql .= 'ADD CONSTRAINT `'. database::input($name) .'` CHECK ('. database::input($expression) .'),' . PHP_EOL;
					} else {
						$sql .= '  CONSTRAINT `'. database::input($name) .'` CHECK ('. database::input($expression) .'),' . PHP_EOL;
					}
				}
			}

			if ($table_exists) {
				$sql = rtrim($sql, ", \r\n") . ';';
			} else {
				$sql = rtrim($sql, ", \r\n") . PHP_EOL . ") ENGINE='InnoDB' ROW_FORMAT=DYNAMIC DEFAULT CHARSET='utf8mb4' COLLATE='". database::input($default_collation) ."';";
			}

			database::query($sql);

			echo ' [OK]' . PHP_EOL . PHP_EOL;
		}

		echo 'Update table structures completed successfully.' . PHP_EOL;

		## Recreate foreign keys

		echo 'Recreating foreign keys...' . PHP_EOL;

		foreach ($foreign_keys as $table_name => $constraints) {
			foreach ($constraints as $constraint_name => $definition) {

				// Skip if table no longer exists
				if (!in_array($table_name, $existing_tables)) continue;

				// Skip if referenced table no longer exists
				if (!in_array($definition['referenced_table'], $existing_tables)) continue;

				try {

					database::query(
						"ALTER TABLE `". database::input($table_name) ."`
						ADD CONSTRAINT `". database::input($constraint_name) ."`
						FOREIGN KEY (`". implode('`, `', array_map('database::input', $definition['columns'])) ."`)
						REFERENCES `". database::input($definition['referenced_table']) ."` (`". implode('`, `', array_map('database::input', $definition['referenced_columns'])) ."`);"
					);

				} catch (Exception $e) {
					echo 'Warning: Could not recreate foreign key '. $constraint_name .' on '. $table_name .': '. $e->getMessage() . PHP_EOL;
				}
			}
		}

		echo 'Foreign keys recreated successfully.' . PHP_EOL;

		echo PHP_EOL
		. 'Migrations complete!' . PHP_EOL;

	} catch (Exception $e) {
		echo 'Error: '. $e->getMessage() . PHP_EOL;
	}
