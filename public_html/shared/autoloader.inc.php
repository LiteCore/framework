<?php

	spl_autoload_register(function($class) {

		switch (true) {

			// Abstract classes
			case (preg_match('#^abs_#', $class)):

				require 'app://shared/abstracts/' . $class . '.inc.php';
				break;

			// Clients and wrappers
			case (preg_match('#_client$#', $class)):

				require 'app://shared/clients/' . $class . '.inc.php';
				break;

			// Entities
			case (preg_match('#^ent_#', $class)):

				require 'app://shared/entities/' . $class . '.inc.php';
				break;

			// Modules
			case (preg_match('#^mod_#', $class)):

				require 'app://shared/modules/' . $class . '.inc.php';
				break;

			// Submodules
			case (preg_match('#^job_#', $class)):
				$file = match(strtok($class, '_')) {
					'job' => FS_DIR_APP . 'shared/modules/jobs/' . $class . '.inc.php',
					'tm' => FS_DIR_APP . 'shared/modules/translation/' . $class . '.inc.php',
				};

				// Patch modules for PHP 8.2 Compatibility
				if (version_compare(PHP_VERSION, 8.2, '>=') && is_file($file)) {
					$source = file_get_contents($file);
					if (!preg_match('#\#\[AllowDynamicProperties\]#', $source)) {
						$source = preg_replace('#([ \t]*)class [a-zA-Z0-9_-]+\s*?\{(\n|\r\n?)#', '$1#[AllowDynamicProperties]$2$0', $source);
						file_put_contents($file, $source);
					}
				}

				require $file;
				break;

			// References
			case (preg_match('#^ref_#', $class)):

				require 'app://shared/references/' . $class . '.inc.php';
				break;

			// Routing modules
			case (preg_match('#^url_#', $class)):

				if (is_file($file = 'app://backend/routes/' . $class . '.inc.php')) require $file;
				if (is_file($file = 'app://frontend/routes/' . $class . '.inc.php')) require $file;
				break;

			// Stream wrappers
			case (preg_match('#^stream_#', $class)):

				require 'app://shared/streams/' . $class . '.inc.php';
				break;

			// Types
			case (preg_match('#^type_#', $class)):

				require 'app://shared/types/' . $class . '.inc.php';
				break;

			// System nodes
			default:

				if (is_file($file = 'app://shared/nodes/nod_' . $class . '.inc.php')) {
					require $file;

					if (method_exists($class, 'init')) {
						call_user_func([$class, 'init']); // As static classes do not have a __construct() (PHP #62860)
					}
				}

				break;
		}
	}, true, false);
