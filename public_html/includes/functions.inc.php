<?php

	// Output any variable to the browser console
	function console_dump(...$vars) { // ... as of PHP 5.6

		ob_start();
		var_dump($vars);
		$output = addcslashes(ob_get_clean(), "\"\r\n");

		echo '<script>console.log("'. addcslashes($output, '"') .'");</script>';
	}

	// Checks if variables are not set, null, (bool)false, (int)0, (float)0.00, (string)"", (string)"0", (string)"0.00", (array)[], or array with nil nodes
	function nil(&...$args) { // ... as of PHP 5.6
		foreach ($args as $arg) {
			if (is_array($arg)) {
				foreach ($arg as $node) {
					if (!nil($node)) return !1;
				}
			}
			if (!empty($arg) || (is_numeric($arg) && (float)$arg != 0)) return !1;
		}
		return !0;
	}

	// Returns value for variable or falls back to a substituting value on nil(). Similar to $var ?? $fallback
	function fallback(&$var, $fallback=null) {
		if (!nil($var)) return $var;
		return $fallback;
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

	// Check if variable indicates a truthy value
	function is_true($string) {
		//return (!empty($string) && preg_match('#^(1|true|yes|on|active|enabled)$#i', $string));
		return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}

	// Check if variable indicates a falsy value
	function is_false($string) {
		//return (empty($string) || preg_match('#^(0|false|no|off|inactive|disabled)$#i', $string));
		return !filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}

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

	// Slice a string into two arrays at the nth position
	function str_slice($string, $position) {
		return [substr($string, 0, $position), substr($string, $position)];
	}
