<?php

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
