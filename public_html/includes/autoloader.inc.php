<?php

	spl_autoload_register(function($class) {

		switch (true) {

			case (substr($class, 0, 4) == 'abs_'):

				require 'app://includes/abstracts/' . $class . '.inc.php';
				break;

			case (substr($class, -7) == '_client'):

				require 'app://includes/clients/' . $class . '.inc.php';
				break;

			case (preg_match('#^(job)_#', $class)):

				// Patch modules for PHP 8.2 Compatibility
				if (version_compare(PHP_VERSION, 8.2, '>=')) {

					$search_replace = [
						'#^(job_.*)#' => 'app://includes/modules/jobs/$1.inc.php',
					];

					$file = preg_replace(array_keys($search_replace), array_values($search_replace), $class);

					if (is_file($file)) {
						$source = file_get_contents($file);

						if (!preg_match('#\#\[AllowDynamicProperties\]#', $source)) {
							$source = preg_replace('#([ \t]*)class [a-zA-Z0-9_-]+ *\{(\n|\r\n?)#', '$1#[AllowDynamicProperties]$2$0', $source);
							file_put_contents($file, $source);
						}

						if (!preg_match('#class [a-zA-Z0-9_-]+ extends abs_module#', $source)) {
							$source = preg_replace('#(class [a-zA-Z0-9_-]+) *\{#', '$1 extends abs_module {', $source);
							file_put_contents($file, $source);
						}
					}
				}

				switch ($class) {

					case (substr($class, 0, 4) == 'job_'):
						require vmod::check('app://includes/modules/jobs/' . $class . '.inc.php');
						break;
				}

				break;

			case (substr($class, 0, 4) == 'ent_'):

				require 'app://includes/entities/' . $class . '.inc.php';
				break;

			case (substr($class, 0, 4) == 'mod_'):

				require 'app://includes/modules/' . $class . '.inc.php';
				break;

			case (substr($class, 0, 4) == 'ref_'):

				require 'app://includes/references/' . $class . '.inc.php';
				break;

			case (substr($class, 0, 4) == 'url_'):

				if (is_file($file = 'app://backend/routes/' . $class . '.inc.php')) require $file;
				if (is_file($file = 'app://frontend/routes/' . $class . '.inc.php')) require $file;
				break;

			case (substr($class, 0, 5) == 'wrap_'):

				require 'app://includes/wrappers/' . $class . '.inc.php';
				break;

			default:

				if (is_file($file = 'app://includes/nodes/nod_' . $class . '.inc.php')) {
					require $file;
				}

				if (method_exists($class, 'init')) {
					call_user_func([$class, 'init']); // As static classes do not have a __construct() (PHP #62860)
				}

				if (is_file($file = 'app://includes/nodes/' . $class . '.inc.php')) {
					require $file;
				}

				break;
		}
	}, true, false);
