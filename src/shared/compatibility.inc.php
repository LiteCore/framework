<?php

	// Check version
	if (version_compare(phpversion(), '8.0.0', '<') == true) {
		die('This application requires at minimum PHP 8.0+ (Detected '. phpversion() .')');
	}

	// Polyfill for glob brace on Alpine
	if (!defined('GLOB_BRACE')) {
		define('GLOB_BRACE', 0);
	}

	// Polyfill for getallheaders() on non-Apache machines
	if (!function_exists('getallheaders')) {
		function getallheaders() {
			$headers = [];
			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			return $headers;
		}
	}

	// Polyfill for some $_SERVER variables in CLI
	if (!isset($_SERVER['REQUEST_METHOD'])) { // Don't rely on php_sapi_name()
		$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/..');
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['SERVER_PORT'] = '443';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = '/';
		$_SERVER['SERVER_SOFTWARE'] = 'CLI';
		$_SERVER['SCRIPT_FILENAME'] = $argv[0] ?? 'index.php';
		$_SERVER['HTTPS'] = 'on';
	}

	// Normalize Windows paths to Unix-style
	$_SERVER['SCRIPT_FILENAME'] = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);

	if (!isset($_SERVER['SERVER_SOFTWARE'])) {
		$_SERVER['SERVER_SOFTWARE'] = 'Unknown';
	}

	if (empty($_SERVER['HTTPS'])) {
		$_SERVER['HTTPS'] = ($_SERVER['SERVER_PROTOCOL'] == 'https') ? 'on' : 'off';
	}

	if (empty($_SERVER['HTTP_HOST'])) {
		$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
	}

	if (!isset($_SERVER['HTTP_USER_AGENT'])) {
		$_SERVER['HTTP_USER_AGENT'] = '';
	}

	// Redefine $_SERVER['REMOTE_ADDR'] by CloudFlare Proxy
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {

		// IP ranges from CloudFlare (https://www.cloudflare.com/ips/) are outdated
		foreach ([
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'104.16.0.0/13',
			'104.24.0.0/14',
			'108.162.192.0/18',
			'131.0.72.0/22',
			'141.101.64.0/18',
			'162.158.0.0/15',
			'172.64.0.0/13',
			'173.245.48.0/20',
			'188.114.96.0/20',
			'190.93.240.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17',
		] as $range) {

				list($subnet, $bits) = explode('/', $range);
				$ip = ip2long($_SERVER['HTTP_CF_CONNECTING_IP']);
				$subnet = ip2long($subnet);
				$mask = -1 << (32 - $bits);
				$subnet &= $mask; // network address

				if (($ip & $mask) === $subnet) {
				foreach (array_reverse(preg_split('#\s*,\s*#', $_SERVER['HTTP_CF_CONNECTING_IP'], -1, PREG_SPLIT_NO_EMPTY)) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						$_SERVER['REMOTE_ADDR'] = $ip;
					}
				}
				break;
			}
	}

		foreach (array_reverse(preg_split('#\s*,\s*#', $_SERVER['HTTP_CF_CONNECTING_IP'], -1, PREG_SPLIT_NO_EMPTY)) as $ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
					$_SERVER['REMOTE_ADDR'] = $ip;
				}
			}
		}
