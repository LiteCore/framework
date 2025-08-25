<?php

	// Short hand command for dumping variables to output and exiting
	function x(&...$arrays) {

		foreach ($arrays as $array) {
			var_dump($array);
		}

		exit(1);
	}

	function backtrace() {

		$trace = debug_backtrace();
		$caller = array_shift($trace);

		echo $caller['file'] .' on line '. $caller['line'];

		foreach ($trace as $caller) {
			echo "\n". $caller['file'] .' on line '. $caller['line'];
		}

		exit(1);
	}

	// Output any variable to the browser console
	function console_dump(...$vars) { // ... as of PHP 5.6

		ob_start();
		var_dump($vars);
		$output = ob_get_clean();

		echo '<script>console.log("'. addcslashes($output, "\"\r\n") .'");</script>';
	}

	// Include, but in an isolated scope
	function embed() {
		foreach (func_get_args() as $file) {
			(function(){
				include func_get_arg(0);
			})($file);
		}
	}

	// Return the first non-nil variable
	function coalesce(&...$args) { // ... as of PHP 5.6
		foreach ($args as $arg) {
			if (!nil($arg)) return $arg;
		}
	}

/*
	// Checks if two variables are equal(ish). Case insensitive. Interprets null, (string)"", (array)[] and false as the same
	function equalish(mixed &$var1, mixed &$var2):bool {
		if (nil($var1) && nil($var2)) return true;
		if (is_string($var1) && is_string($var2) && strcasecmp($var1, $var2)) return true;
		if ($var1 == $var2) return true;
		return false;
	}
*/

	// Return a sane list of uploaded files $name[subnode][subnode][tmp_name] rather than $name[tmp_name][subnode][subnode]
	function get_uploaded_files() {

		$result = [];
		foreach (explode('&', http_build_query($_FILES, '&')) as $pair) {
			list($key, $value) = explode('=', $pair);
			$key = urlencode(preg_replace('#^([^\[]+)\[(name|tmp_name|type|size|error)\](.*)$#', '$1$3[$2]', urldecode($key)));
			$result[] = $key .'='. $value;
		}

		parse_str(implode('&', $result), $result);

		return $result;
	}
