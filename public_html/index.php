<?php
/*!
 * LiteBase™ 1.0.0
 *
 * Website Core Framework
 *
 * LiteBase is provided free without warranty.
 *
 * @author    LiteCart Dev Team <development@litecart.net>
 * @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 * @link      https://www.litecart.net Official Website
 *
 */

	require_once('includes/app_header.inc.php');

	route::load('app://frontend/routes/url_*.inc.php');
	route::load('app://backend/routes/url_*.inc.php');

	// Append last destination route
	route::add('*', [
		'pattern' => '#^(.*)$#',
		'endpoint' => 'frontend',
		'controller' => 'app://frontend/pages/$1.inc.php',
	]);

	route::identify();

	// Initialize endpoint
	if (!empty(route::$selected['endpoint']) && route::$selected['endpoint'] == 'backend') {
		require 'app://backend/init.inc.php';
	} else {
		require 'app://frontend/init.inc.php';
	}

	// Run operations before processing the route
	event::fire('before_capture');

	// Process the route and capture the content
	route::process();

	require_once 'app://includes/app_footer.inc.php';
