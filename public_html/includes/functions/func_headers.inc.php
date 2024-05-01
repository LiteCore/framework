<?php

	// Get output headers so far
	function headers_get($name=null) {

		$headers = [];

		foreach (headers_list() as $header) {

			if ($name && preg_match('#^('.preg_quote($name, '#') .'): (.*)$#i', $header, $matches)) {
				return trim($matches[2]);
			}

			$headers[trim($matches[1])] = trim($matches[2]);
		}

		if ($name) return false;

		return $headers;
	}
