<?php

	spl_autoload_register(function($class) {

		switch (true) {

			// Abstract classes
			case (preg_match('#^abs_#', $class)):

				require 'app://includes/abstracts/' . $class . '.inc.php';
				break;

			// Clients and wrappers
			case (preg_match('#_client$#', $class)):

				require 'app://includes/clients/' . $class . '.inc.php';
				break;

			// Entities
			case (preg_match('#^ent_#', $class)):

				require 'app://includes/entities/' . $class . '.inc.php';
				break;

			// Modules
			case (preg_match('#^mod_#', $class)):

				require 'app://includes/modules/' . $class . '.inc.php';
				break;

			// Submodules
			case (preg_match('#^job_#', $class)):

				// Patch modules for PHP 8.2 Compatibility
				if (version_compare(PHP_VERSION, 8.2, '>=')) {

					$search_replace = [
						'#^job_.*$#' => 'app://includes/modules/jobs/$1.inc.php',
					];

					$file = preg_replace(array_keys($search_replace), array_values($search_replace), $class);

					if (is_file($file)) {
						$source = file_get_contents($file);

						if (!preg_match('#class [a-zA-Z0-9_-]+ extends abs_module#', $source)) {
							$source = preg_replace('#(class [a-zA-Z0-9_-]+) *\{#', '$1 extends abs_module {', $source);
							file_put_contents($file, $source);
						}
					}
				}

				switch (true) {

					case (preg_match('#^job_#', $class)):
						require 'app://includes/modules/jobs/' . $class . '.inc.php';
						break;
				}

				break;

			// References
			case (preg_match('#^ref_#', $class)):

				require 'app://includes/references/' . $class . '.inc.php';
				break;

			// Routing modules
			case (preg_match('#^url_#', $class)):

				if (is_file($file = 'app://backend/routes/' . $class . '.inc.php')) require $file;
				if (is_file($file = 'app://frontend/routes/' . $class . '.inc.php')) require $file;
				break;

			// Stream wrappers
			case (preg_match('#^stream_#', $class)):

				require 'app://includes/streams/' . $class . '.inc.php';
				break;

			// System nodes
			default:

				if (is_file($file = 'app://includes/nodes/nod_' . $class . '.inc.php')) {
					require $file;

					if (method_exists($class, 'init')) {
						call_user_func([$class, 'init']); // As static classes do not have a __construct() (PHP #62860)
					}
				}

				break;
		}
	}, true, false);
