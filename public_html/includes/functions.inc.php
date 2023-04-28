<?php

/*
 * Output any variable to the browser console
 * Only works in a HTML environment
 * @author T. Almroth
*/
	function console_dump($var) {
		echo '<script>console.log("'. addcslashes(var_export($var, true), "\"\r\n") .'");</script>';
	}

	/*
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
	*/

	// Checks if variable is not set, null, (bool)false, (int)0, (float)0.00, (string)"", (string)"0", (string)"0.00", (array)[], or array with nil nodes
	function nil(&$var) {
		if (is_array($var)) {
			foreach ($var as $node) {
				if (!nil($node)) return !1;
			}
		}
		return (empty($var) || (is_numeric($var) && (float)$var == 0));
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
	// Checks if variable is equal(ish) to brother. Interprets null, (string)"", (array)[] and false as the same
	function equalish(mixed &$var1, mixed &$var2):bool {
		if (nil($var1) && nil($var2)) return true;
		if (is_string($var1) && is_string($var2) && strcasecmp($var1, $var2)) return true;
		if ($var1 == $var2) return true;
		return false;
	}
*/

	// Check if variable or string indicates true
	function is_true($string) {
			//return (!empty($string) && preg_match('#^(1|true|yes|on|active|enabled)$#i', $string));
		return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}

	// Check if variable or string indicates false
	function is_false($string) {
			//return (empty($string) || preg_match('#^(0|false|no|off|inactive|disabled)$#i', $string));
		return !filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}

	// Return a sane list of uploaded files
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
