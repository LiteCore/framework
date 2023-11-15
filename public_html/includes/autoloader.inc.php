<?php

	spl_autoload_register(function($class) {

		switch (true) {

			case (substr($class, 0, 4) == 'abs_'):

				require 'app://includes/abstracts/' . $class . '.inc.php';
				break;

			case (substr($class, -7) == '_client'):

				require 'app://includes/clients/' . $class . '.inc.php';
				break;

			case (substr($class, 0, 4) == 'job_'):

				// Patch modules for PHP 8.2 Compatibility
				if (version_compare(PHP_VERSION, 8.2, '>=')) {

					$file = preg_replace('#^(job_.*)#', 'app://includes/modules/jobs/$1.inc.php', $class);

					if (is_file($file)) {
						$source = file_get_contents($file);
						if (!preg_match('#\#\[AllowDynamicProperties\]#', $source)) {
							$source = preg_replace('#([ \t]*)class [a-zA-Z0-9_-]+ *\{(\n|\r\n?)#', '$1#[AllowDynamicProperties]$2$0', $source);
							file_put_contents($file, $source);
						}
					}
				}

				require 'app://includes/modules/jobs/' . $class . '.inc.php';
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

				require 'app://includes/nodes/nod_' . $class . '.inc.php';

				if (method_exists($class, 'init')) {
					call_user_func([$class, 'init']); // As static classes do not have a __construct() (PHP #62860)
				}

				break;
		}
	}, true, false);
