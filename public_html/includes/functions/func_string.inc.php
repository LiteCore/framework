<?php

	// Turns "a, b,,c," into ['a', 'b', 'c'], also works with \r\n
	function string_split($string, $delimiters=',') {
		return preg_split('#(\s*['. preg_quote($delimiters, '#') .']\s*)+#', (string)$string, -1, PREG_SPLIT_NO_EMPTY);
	}

	// Replace parts of string with contents from recursive array (like strtr() but recursively)
	function string_translate($string, $array) {

		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$string = string_translate($str, $value);
			} else {
				$string = str_replace($key, $value, $str);
			}
		}

		return $string;
	}

	// Collapse the middle part of a long string using ellipsis
	function string_ellipsis($text, $maxlength=72) {

		if (strlen($text) > ($maxlength + 6)) {
			$half = floor($maxlength / 2);
			return substr($text, 0, $half) . '…' . substr($text, -$half);
		}

		return $text;
	}

	// Slice a string into two arrays at the nth position
	function string_slice($string, $position) {
		return [substr($string, 0, $position), substr($string, $position)];
	}

	// Adds padding to the beginning of each line of a string
	function string_pad_lines($string, $padding, $pad_type=STR_PAD_LEFT) {

		if ($pad_type & STR_PAD_BOTH || $pad_type & STR_PAD_LEFT) {
			$string = preg_replace('#^#m', preg_quote($padding), $string);
		}

		if ($pad_type & STR_PAD_BOTH || $pad_type & STR_PAD_RIGHT) {
			$string = preg_replace('#$#m', preg_quote($padding), $string);
		}

		return $string;
	}
