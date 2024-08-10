<?php

	class vmod {
		public static  $enabled = true;                // Bool whether or not to enable this feature
		private static $aliases = [];                  // Array of path aliases ['pattern' => 'replace']
		private static $_checked = [];                 // Array of files that have already passed check() and
		private static $_checksums = [];               // Array of checksums for time comparison
		private static $_files_to_modifications = [];  // Array of references to modifications
		private static $_modifications = [];           // Array of modifications to apply
		private static $_installed = [];               // Array of installed modifications
		private static $_settings = [];                // Array of modification settings
		public static  $time_elapsed = 0;              // Integer of time elapsed during operations

		public static function init() {

			// Check if enabled
			if (defined('VMOD_DISABLED') && filter_var(VMOD_DISABLED, FILTER_VALIDATE_BOOL)) {
				self::$enabled = false;
				return;
			}

			$timestamp = microtime(true);

			if (!is_dir(FS_DIR_STORAGE . 'addons/.cache/')) {
				mkdir(FS_DIR_STORAGE . 'addons/.cache/');
			}

			if (!is_file($installed_file = FS_DIR_STORAGE .'addons/.installed')) {
				file_put_contents($installed_file, '', LOCK_EX);
			}

			if (!is_file($checked_file = FS_DIR_STORAGE . 'addons/.cache/.checked')) {
				file_put_contents($checked_file, '', LOCK_EX);
			}

			if (!is_file($cache_file = FS_DIR_STORAGE . 'addons/.cache/.modifications')) {
				file_put_contents($cache_file, '{}');
			}

			if (!is_file($settings_file = FS_DIR_STORAGE .'addons/.settings')) {
				file_put_contents($settings_file, '{}');
			}

			// Determine last modified date
			$last_modified = null;

			if (($folder_last_modified = filemtime(FS_DIR_STORAGE .'addons/')) > $last_modified) {
				$last_modified = $folder_last_modified;
			}

			foreach (scandir(FS_DIR_STORAGE .'addons/') as $folder) {
				if (in_array($folder, ['.', '..', '.cache'])) continue;
				if (!is_dir(FS_DIR_STORAGE .'addons/'.$folder)) continue;
				if (preg_match('#\.disabled$#', $folder)) continue;

				$vmod = FS_DIR_STORAGE .'addons/'.$folder.'/vmod.xml';

				if (filemtime($vmod) > $last_modified) {
					$last_modified = filemtime($vmod);
				}
			}

			if (($installed_last_modified = filemtime($installed_file)) > $last_modified) {
				$last_modified = $installed_last_modified;
			}

			if (($settings_last_modified = filemtime($settings_file)) > $last_modified) {
				$last_modified = $settings_last_modified;
			}

			// If no cache is requested by browser
				//if (isset($_SERVER['HTTP_CACHE_CONTROL']) && preg_match('#no-cache#i', $_SERVER['HTTP_CACHE_CONTROL'])) {
				//  $last_modified = time();
				//}

			// Load installed
			foreach (file($installed_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $installed) {
				list($id, $version) = preg_split('#;#', $installed);
				self::$_installed[$id] = $version;
			}

			// Load settings
			if (!self::$_settings = json_decode(file_get_contents($settings_file), true)) {
				self::$_settings = [];
			}

			// Get modifications from cache
			if (filemtime($cache_file) > $last_modified) {
				if ($cache = json_decode(file_get_contents($cache_file), true)) {
					self::$_modifications = $cache['modifications'];
					self::$_files_to_modifications = $cache['index'];
				}
			}

			// Create a list of checked files
			if (filemtime($checked_file) > $last_modified) {
				foreach (file($checked_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
					list($original_file, $modified_file, $checksum) = preg_split('#;#', $line);
					if (is_file(FS_DIR_APP . $original_file) && is_file(FS_DIR_STORAGE . $modified_file) && filemtime(FS_DIR_STORAGE . $modified_file) > filemtime(FS_DIR_APP . $original_file)) {
						self::$_checked[$original_file] = $modified_file;
						self::$_checksums[$original_file] = $checksum;
					}
				}
			}

			// Load modifications from disk
			if (!self::$_modifications) {
				foreach (scandir(FS_DIR_STORAGE .'addons/') as $folder) {
					if (in_array($folder, ['.', '..', '.cache'])) continue;
					if (!is_dir(FS_DIR_STORAGE .'addons/'.$folder)) continue;
					if (preg_match('#\.disabled$#', $folder)) continue;
					self::load(FS_DIR_STORAGE .'addons/'. $folder .'/vmod.xml');
				}

				// Store modifications to cache
				$serialized = json_encode([
					'modifications' => self::$_modifications,
					'index' => self::$_files_to_modifications,
					//), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
				], JSON_UNESCAPED_SLASHES);

				file_put_contents($cache_file, $serialized, LOCK_EX);
			}

			self::$time_elapsed += microtime(true) - $timestamp;
		}

		// Return a modified file
		public static function check($file) {

			// Halt if there is nothing to modify
			if (!self::$enabled || !$file || !self::$_files_to_modifications) {
				return $file;
			}

			$timestamp = microtime(true);

			if (!is_file($file)) {
					// check here if there is a modification creating the file
				self::$time_elapsed += microtime(true) - $timestamp;
				return $file;
			} else {
				$file = str_replace('\\', '/', realpath($file));
			}

			$original_file = preg_replace('#^(app://|' . preg_quote(FS_DIR_APP, '#') .')#', '', $file);
			$modified_file = 'addons/.cache/' . preg_replace('#[/\\\\]+#', '-', $original_file);

			// Return original file if there are no modifications
			if (empty(self::$_files_to_modifications[$original_file])) {

				if (isset(self::$_checked[$original_file])) {
					unset(self::$_checked[$original_file]);
				}

				if (isset(self::$_checksums[$original_file])) {
					unset(self::$_checksums[$original_file]);
				}

				self::$time_elapsed += microtime(true) - $timestamp;
				return $file;
			}

			// Add modifications to queue and calculate checksum
			$queue = [];
			$digest = [filemtime($file)];

			foreach (self::$_files_to_modifications[$original_file] as $modification) {
				$digest[] = strtotime(self::$_modifications[$modification['id']]['date_modified']);
				$queue[] = $modification;
			}

			$checksum = crc32(implode($digest));

			// Return original file if nothing to modify
			if (empty($queue)) {

				if (is_file(FS_DIR_STORAGE . $modified_file)) {
					unlink(FS_DIR_STORAGE . $modified_file);
				}

				self::$time_elapsed += microtime(true) - $timestamp;
				return FS_DIR_STORAGE . (self::$_checked[$original_file] = $modified_file);
			}

			// Return modified file if checksum matches
			if (!empty(self::$_checksums[$original_file]) && self::$_checksums[$original_file] == $checksum) {
				if (!empty(self::$_checked[$original_file]) && file_exists(FS_DIR_STORAGE . self::$_checked[$original_file])) {
					self::$time_elapsed += microtime(true) - $timestamp;
					return FS_DIR_STORAGE . (self::$_checked[$original_file] = $modified_file);
				}
			}

			// Modify file
			if (is_file($file)) {
				$original = $buffer = preg_replace('#\r\n?|\n#', PHP_EOL, file_get_contents($file));
			} else {
				$original = $buffer = '';
			}

			foreach ($queue as $modification) {

				if (!$vmod = self::$_modifications[$modification['id']]) continue;
				if (!$operations = self::$_modifications[$modification['id']]['files'][$modification['key']]['operations']) continue;

				$tmp = $buffer; $i = 0;
				foreach ($operations as $operation) {
					$i++;

					$found = preg_match_all($operation['find']['pattern'], $tmp, $matches, PREG_OFFSET_CAPTURE);

					if (!$found) {
						switch ($operation['onerror']) {
							case 'abort':
								trigger_error("Modification \"$vmod[name]\" failed during operation #$i in $original_file: Search not found [ABORTED]", E_USER_WARNING);
								continue 3;
							case 'ignore':
								continue 2;
							case 'warning':
							default:
								trigger_error("Modification \"$vmod[name]\" failed during operation #$i in $original_file: Search not found", E_USER_WARNING);
								continue 2;
						}
					}

					if (!empty($operation['find']['indexes'])) {
						rsort($operation['find']['indexes']);

						foreach ($operation['find']['indexes'] as $index) {
							$index = $index - 1; // [0] is the 1st in computer language

							if ($found > $index) {
								$tmp = substr_replace($tmp, preg_replace($operation['find']['pattern'], $operation['insert'], $matches[0][$index][0]), $matches[0][$index][1], strlen($matches[0][$index][0]));
							}
						}

					} else {
						$tmp = preg_replace($operation['find']['pattern'], $operation['insert'], $tmp, -1, $count);

						if (!$count && $operation['onerror'] != 'skip') {
							trigger_error("Vmod failed to perform insert", E_USER_ERROR);
							continue 2;
						}
					}
				}

				$buffer = $tmp;
			}

			// Create cache folder for modified files if missing
			if (!is_dir(FS_DIR_STORAGE . 'addons/.cache/')) {
				if (!mkdir(FS_DIR_STORAGE . 'addons/.cache/', 0777)) {
					throw new \Exception('The modifications cache directory could not be created', E_USER_ERROR);
				}
			}

			if (!is_writable(FS_DIR_STORAGE . 'addons/.cache/')) {
				throw new \Exception('The modifications cache directory is not writable', E_USER_ERROR);
			}

			// Return original if nothing was modified
			if ($buffer == $original) {
				self::$time_elapsed += microtime(true) - $timestamp;
				return FS_DIR_STORAGE . (self::$_checked[$original_file] = $original_file);
			}

			// Write modified file
			file_put_contents(FS_DIR_STORAGE . $modified_file, $buffer, LOCK_EX);

			// Update checked cache
			if (!isset(self::$_checked[$original_file]) || self::$_checksums[$original_file] != $checksum) {
				self::$_checked[$original_file] = $modified_file;
				self::$_checksums[$original_file] = $checksum;
				$serialized_checked = implode('', array_map(function($original_file){
					if (!isset(self::$_checksums[$original_file])) return;
					return $original_file .';'. self::$_checked[$original_file] .';'. self::$_checksums[$original_file] . PHP_EOL;
				}, array_keys(self::$_checked)));

				file_put_contents(FS_DIR_STORAGE . 'addons/.cache/.checked', $serialized_checked, LOCK_EX);
			}

			self::$time_elapsed += microtime(true) - $timestamp;
			return FS_DIR_STORAGE . $modified_file;
		}

		public static function load($file) {

			try {

				// Get XML file contents
				if (!$xml = file_get_contents($file)) {
					throw new \Exception('Could not read file', E_USER_ERROR);
				}

				// Normalize line endings
				$xml = preg_replace('#\r\n?|\n#', PHP_EOL, $xml);

				// Initiate a Document Object
				$dom = new \DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhiteSpace = false;

				if (!$dom->loadXml($xml)) {
					throw new \Exception(libxml_get_last_error()->message);
				}

				$vmod = self::parse_xml($dom, $file);

				// Load modification if it is installed
				if (in_array($vmod['id'], array_keys(self::$_installed))) {

					self::$_modifications[$vmod['id']] = $vmod;

					// Create cross reference for file patterns
					foreach (array_keys($vmod['files']) as $key) {

						$glob_pattern = $vmod['files'][$key]['name'];

						// Apply path aliases
						if (!empty(self::$aliases)) {
							$glob_pattern = preg_replace(array_keys(self::$aliases), array_values(self::$aliases), $glob_pattern);
						}

						foreach (glob(FS_DIR_APP . $glob_pattern, GLOB_BRACE) as $file_to_modify) {
							$relative_path = preg_replace('#^'. preg_quote(FS_DIR_APP, '#') .'#', '', $file_to_modify);

							self::$_files_to_modifications[$relative_path][] = [
								'id' => $vmod['id'],
								'key' => $key,
							];
						}
					}

					// Run upgrades if a previous version is installed
					if (self::$_installed[$vmod['id']] < $vmod['version']) {

						if (!empty($dom->getElementsByTagName('upgrade')->length)) {

							// Gather upgrade scripts
							$upgrades = [];

							foreach ($dom->getElementsByTagName('upgrade') as $upgrade_node) {

								$upgrade_version = $upgrade_node->getAttribute('version');

								if (version_compare($vmod['version'], $upgrade_version, '<=')) {
									$upgrades[] = [
										'version' => $upgrade_version,
										'script' => $upgrade_node->textContent,
									];
								}
							}

							uasort($upgrades, function($a, $b) {
								return version_compare($a['version'], $b['version']);
							});

							require_once vmod::check(FS_DIR_APP . 'includes/compatibility.inc.php');
							require_once vmod::check(FS_DIR_APP . 'includes/autoloader.inc.php');

							// Execute upgrade scripts
							foreach ($upgrades as $upgrade) {

								if (version_compare($upgrade['version'], self::$_installed[$vmod['id']], '<=')) continue;

								// Exceute upgrade in an isolated scope
								$tmp_file = stream_get_meta_data(tmpfile())['uri'];
								file_put_contents($tmp_file, "<?php" . PHP_EOL . $upgrade['script']);

								(function() {
									include func_get_arg(0);
								})($tmp_file);

								foreach (self::$_installed as $id => $version) {
									if ($id == $vmod['id']) {
										self::$_installed[$id] = $upgrade['version'];
										break;
									}
								}
							}

							$new_contents = implode(PHP_EOL, array_map(function($id, $version) {
								return $id .';'. $version;
							}, array_keys(self::$_installed), self::$_installed));

							file_put_contents(FS_DIR_STORAGE . 'addons/.installed', $new_contents . PHP_EOL, LOCK_EX);

							if (isset($_SERVER['REQUEST_URI'])) {
								header('Location: '. $_SERVER['REQUEST_URI']);
								exit;
							}
						}
					}

				// Install if this is a new modification
				} else {

					file_put_contents(FS_DIR_STORAGE . 'addons/.installed', $vmod['id'] .';'. $vmod['version'] . PHP_EOL, FILE_APPEND | LOCK_EX);

					// Exceute install script
					if ($dom->getElementsByTagName('install')->length) {

						require_once vmod::check(FS_DIR_APP . 'includes/compatibility.inc.php');
						require_once vmod::check(FS_DIR_APP . 'includes/autoloader.inc.php');

						$tmp_file = stream_get_meta_data(tmpfile())['uri'];
						file_put_contents($tmp_file, "<?php\r\n" . $dom->getElementsByTagName('install')->item(0)->textContent);

						(function() {
							include func_get_arg(0);
						})($tmp_file);

						header('Location: '. $_SERVER['REQUEST_URI']);
						exit;
					}

					self::$_installed[$vmod['id']] = $vmod['version'];
				}

			} catch (\Exception $e) {
				trigger_error("Could not load vMod ($file): " . $e->getMessage(), E_USER_WARNING);
			}
		}

		public static function parse_xml($dom, $file) {

			if ($dom->documentElement->tagName != 'vmod') {
				throw new \Exception("File is not a valid vMod ($file)");
			}

			if (empty($dom->getElementsByTagName('name')->item(0))) {
				throw new \Exception('File is missing the name element');
			}

			$id = preg_replace('#\.disabled$#', '', basename(dirname($file)));
			if ($id == 'vmods') $id = preg_replace('#\.(disabled|xml)$#', '', basename($file));

			$vmod = [
				'type' => 'vmod',
				'id' => $id,
				'name' => $dom->getElementsByTagName('name')->item(0)->textContent,
				'version' => $dom->getElementsByTagName('version')->item(0)->textContent,
				'author' => !empty($dom->getElementsByTagName('author')) ? $dom->getElementsByTagName('author')->item(0)->textContent : '',
				'date_modified' => date('Y-m-d H:i:s', filemtime($file)),
				'files' => [],
				'install' => null,
				'upgrades' => [],
			];

			if (empty($vmod['version'])) {
				$vmod['version'] = date('Y-m-d', filemtime($file));
			}

			if (!isset(self::$_installed[$vmod['id']])) {

				if ($dom->getElementsByTagName('install')->length > 0) {
					$vmod['install'] = $dom->getElementsByTagName('install')->item(0)->textContent;
				}

			} else {

				if (!empty($dom->getElementsByTagName('upgrade'))) {
					foreach ($dom->getElementsByTagName('upgrade') as $upgrade_node) {

						$upgrade_version = $upgrade_node->getAttribute('version');

						if (version_compare($vmod['version'], $upgrade_version, '<=')) {
							$vmod['upgrades'][] = [
								'version' => $upgrade_version,
								'script' => $upgrade_node->textContent,
							];
						}
					}
				}

				uasort($vmod['upgrades'], function($a, $b){
					return version_compare($a['version'], $b['version']);
				});
			}

			$aliases = [];
			foreach ($dom->getElementsByTagName('alias') as $alias_node) {
				$aliases[$alias_node->getAttribute('key')] = $alias_node->getAttribute('value');
			}

			$settings = [];
			foreach ($dom->getElementsByTagName('setting') as $setting_node) {
				$key = $setting_node->getElementsByTagName('key')->item(0)->textContent;
				$default_value = $setting_node->getElementsByTagName('default_value')->item(0)->textContent;
				$settings[$key] = isset(self::$_settings[$vmod['id']][$key]) ? self::$_settings[$vmod['id']][$key] : $default_value;
			}

			if (empty($dom->getElementsByTagName('file'))) {
				throw new \Exception('File has no defined files to modify');
			}

			foreach ($dom->getElementsByTagName('file') as $file_node) {

				$vmod_file = [
					'name' => $file_node->getAttribute('name'),
					'operations' => [],
				];

				foreach ($file_node->getElementsByTagName('operation') as $operation_node) {

					// On Error
					$onerror = $operation_node->getAttribute('onerror');

					// Find
					if (in_array($operation_node->getAttribute('method'), ['top', 'bottom', 'all'])) {

						$find = '';
						$indexes = '';

					} else {

						$find_node = $operation_node->getElementsByTagName('find')->item(0);
						$find = $find_node->textContent;

						if ($aliases) {
							foreach ($aliases as $key => $value) {
								$find = str_replace('{alias:'. $key .'}', $value, $insert);
							}
						}

						if ($settings) {
							foreach ($settings as $key => $value) {
								$find = str_replace('{setting:'. $key .'}', $value, $find);
							}
						}

						// Trim
						if (in_array($operation_node->getAttribute('type'), ['inline', 'regex'])) {
							$find = trim($find);

						} else if (in_array($operation_node->getAttribute('type'), ['multiline', ''])) {
							$find = preg_replace('#^[ \\t]*(\r\n?|\n)?#s', '', $find); // Trim beginning of CDATA
							$find = preg_replace('#(\r\n?|\n)?[ \\t]*$#s', '$1', $find); // Trim end of CDATA
						}

						// Cook the regex pattern
						if ($operation_node->getAttribute('type') != 'regex') {

							if ($operation_node->getAttribute('type') == 'inline') {
								$find = preg_quote($find, '#');

							} else {

								// Whitespace
								$find = preg_split('#(\r\n?|\n)#', $find);

								for ($i=0; $i<count($find); $i++) {
									if ($find[$i] = trim($find[$i])) {
										$find[$i] = '[ \\t]*' . preg_quote($find[$i], '#') . '[ \\t]*(?:\r\n?|\n|$)';
									} else if ($i != count($find)-1) {
										$find[$i] = '[ \\t]*(?:\r\n?|\n)';
									}
								}

								$find = implode($find);

								// Offset
								if ($find_node->getAttribute('offset-before') != '') {
									$find = '(?:.*?(?:\r\n?|\n)){'. (int)$find_node->getAttribute('offset-before') .'}' . $find;
								}

								if ($find_node->getAttribute('offset-after') != '') {
									$find = $find . '(?:.*?(?:\r\n?|\n|$)){0,'. (int)$find_node->getAttribute('offset-after') .'}';
								}
							}

							// Encapsulate regex
							$find = '#'. $find .'#';
						}

						// Indexes
						if ($indexes = $find_node->getAttribute('index')) {
							$indexes = preg_split('#\s*,\s*#', $indexes, -1, PREG_SPLIT_NO_EMPTY);
						}
					}

					// Insert
					$insert_node = $operation_node->getElementsByTagName('insert')->item(0);
					$insert = $insert_node->textContent;

					if ($aliases) {
						foreach ($aliases as $key => $value) {
							$insert = str_replace('{alias:'. $key .'}', $value, $insert);
						}
					}

					if ($settings) {
						foreach ($settings as $key => $value) {
							$insert = str_replace('{setting:'. $key .'}', $value, $insert);
						}
					}

					if ($operation_node->getAttribute('type') != 'regex') {

						if (in_array($operation_node->getAttribute('type'), ['multiline', ''])) {
							$insert = preg_replace('#^[ \\t]*(\r\n?|\n)?#s', '', $insert); // Trim beginning of CDATA
							$insert = preg_replace('#(\r\n?|\n)?[ \\t]*$#s', '$1', $insert); // Trim end of CDATA
						}

						switch ($method = $operation_node->getAttribute('method')) {

							case 'before':
								$insert = addcslashes($insert, '\\$').'$0';
								break;

							case 'after':
								$insert = '$0'. addcslashes($insert, '\\$');
								break;

							case 'top':
								$find = '#^#s';
								$indexes = '';
								$insert = addcslashes($insert, '\\$').'$0';
								break;

							case 'bottom':
								$find = '#$#s';
								$indexes = '';
								$insert = '$0'.addcslashes($insert, '\\$');
								break;

							case 'replace':
								$insert = addcslashes($insert, '\\$');
								break;

							case 'all':
								$find = '#^.*$#s';
								$add = addcslashes($insert, '\\$');
								break;

							default:
								throw new \Exception("Unknown value \"$method\" for operation method (before|after|replace|bottom|top|all)");
								continue 2;
						}
					}

					// Gather
					$vmod_file['operations'][] = [
						'onerror' => $onerror,
						'find' => [
							'pattern' => $find,
							'indexes' => $indexes,
						],
						'insert' => $insert,
					];
				}

				$vmod['files'][$vmod_file['name']] = $vmod_file;
			}

			return $vmod;
		}
	}
