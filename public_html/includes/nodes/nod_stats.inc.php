<?php

	class stats {

		private static $capture_timestamp;
		private static $capture_duration;
		public static $data;

		public static function init() {
			event::register('before_capture', [__CLASS__, 'before_capture']);
			event::register('after_capture', [__CLASS__, 'after_capture']);
		}

		public static function before_capture() {
			self::$capture_timestamp = microtime(true);
		}

		public static function after_capture() {

			self::$capture_duration = microtime(true) - self::$capture_timestamp;

			if (($page_parse_time = microtime(true) - SCRIPT_TIMESTAMP_START) > 5) {
				notices::add('warnings', sprintf(language::translate('text_long_execution_time', 'We apologize for the inconvenience that the server seems temporary overloaded right now.'), number_format($page_parse_time, 1, ',', ' ')));
				error_log('Warning: Long page execution time '. number_format($page_parse_time, 3, ',', ' ') .' s - '. $_SERVER['REQUEST_URI']);
			}
		}

		public static function render() {

			// Page parse time
			$page_parse_time = microtime(true) - SCRIPT_TIMESTAMP_START;

			$output = implode(PHP_EOL, [
				'<!--',
				'  - Cache Enabled: '. (cache::$enabled ? 'Yes' : 'No'),
				'  - Memory Peak: ' . number_format(memory_get_peak_usage(true) / 1e6, 2, '.', ' ') . ' MB / '. ini_get('memory_limit'),
				'  - Included Files: ' . count(get_included_files()),
				'  - Page Load: ' . number_format($page_parse_time * 1000, 0, '.', ' ') . ' ms',
				'    - Before Content: ' . number_format(self::$data['before_content'] * 1000, 0, '.', ' ') . ' ms',
				'    - Content Capturing: ' . number_format(self::$data['content'] * 1000, 0, '.', ' ') . ' ms',
				'    - After Content: ' . number_format(self::$data['after_content'] * 1000, 0, '.', ' ') . ' ms',
				'    - Rendering: ' . number_format(self::$data['rendering'] * 1000, 0, '.', ' ') . ' ms',
				'  - Content Capture Time: ' . number_format(self::$capture_duration * 1000, 0, '.', ' ') . ' ms',
				'  - Database Queries: ' . number_format(database::$stats['queries'], 0, '.', ' '),
				'  - Database Duration: ' . number_format(database::$stats['duration'] * 1000, 0, '.', ' ') . ' ms',
				'  - Network Requests: ' . number_format(http_client::$stats['requests'], 0, '.', ' '),
				'  - Network Duration: ' . number_format(http_client::$stats['duration'] * 1000, 0, '.', ' ') . ' ms',
				'  - vMod: ' . number_format(vmod::$time_elapsed * 1000, 0, '.', ' ') . ' ms',
				'-->',
			]);

			return $output;
		}
	}
