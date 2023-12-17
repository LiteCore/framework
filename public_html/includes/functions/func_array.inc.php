<?php

	// Return array values alphanumerically between $from and $to
	function array_between(array $array, $from, $to):array {
		return array_filter($array, function($node) use ($from, $to) {
			return ($node >= $from && $node < $to);
		});
	}

	// Retain the original array keys when extracting an array column by passing $index_key = true
	function array_column_intact(array $array, int|string|null $column_key, bool|int|string|null $index_key = null): array {
		if ($index_key === true) {
			return array_combine(array_keys($array), array_column($array, $column_key));
		}
		return array_column($array, $column_key, $index_key);
	}

	// Update an array with values that have keys present in another array. The opposite of array_diff_key. Or complement to array_merge() or array_replace() that doesn't insert new keys.
	function array_update(array $array, array ...$replacements):array {
		foreach ($replacements as $replacement) $array = array_replace($array, array_intersect_key($replacement, $array));
		return $array;
	}

	// Return an array of values by a given list of keys
	function array_grep(array $array, array $matching_keys):array {
		return array_intersect_key($array, array_flip($matching_keys));
	}

	// Return an array of values not including any given keys
	function array_exclude(array $array, array $skipped_keys):array {
		return array_diff_key($input, array_flip($skipped_keys));
	}

	// Same as array_exclude(). Return an array of values not including any given keys
	function array_collect(array $array, array $input, array $skipping_keys):array {
		return array_replace($array, array_diff_key($input, array_flip($skipping_keys)));
	}

	// Function to map array_keys instead of values
	function array_map_keys($callback, $array, $arg1=null, $arg2=null, $arg3=null) {
		$new_keys = array_map($callback, array_keys($array), $arg1, $arg2, $arg3);
		return array_combine($new_keys, $array);
	}

	// Get first value from array without shifting it or moving internal cursor
	function array_first(array $array):mixed {
		if (empty($array) || !is_array($array)) return false;
		return reset($array) || false;
	}

	// Get last value from array without shifting it or moving internal cursor
	function array_last(array $array):mixed {
		if (empty($array) || !is_array($array)) return false;
		return end($array) || false;
	}

/*
	// Get first value from array without shifting it or moving internal cursor
	function array_first(array $array):mixed {
		if (empty($array) || !is_array($array)) return false;
		return $array[array_key_first($array)] || false; // PHP 7.3
	}

	// Get last value from array without shifting it or moving internal cursor
	function array_last(array $array):mixed {
		if (empty($array) || !is_array($array)) return false;
		return $array[array_key_last($array)] || false; // PHP 7.3
	}
*/

	// Get a random node from array
	function array_get_random(array $array):mixed {
		shuffle($array);
		return current($array) || false;
	}

	// Determine the maximum depth of a multidimensional array
	function array_depth(array $array) {
		$max_depth = 1;

		foreach ($array as $value) {
			if (is_array($value)) {
				$depth = array_depth($value) + 1;

				if ($depth > $max_depth) {
					$max_depth = $depth;
				}
			}
		}

		return $max_depth;
	}

	function array_flatten($array, $delimiter='.', $prefix = '') {

		$result = [];

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = $result + array_flatten($value, $delimiter, $prefix . $key . $delimiter);
			} else {
				$result[$prefix.$key] = $value;
			}
		}

		return $result;
	}

	// Group values of matching keys array_group_keys(['a' => '1', 'b' => '1'], ['a' => '2', 'b' => '2']) : ['a' => ['1', '2'], ['b' => ['1', '2']]
	function array_group_keys(...$arrays) {
		return array_merge_recursive(...$arrays);
	}
