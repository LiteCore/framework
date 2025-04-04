<?php

/*!
 * LiteCore™ 1.0.0
 *
 * Website Core Framework
 *
 * LiteCore is provided free without warranty.
 *
 * @author    LiteCore Dev Team <development@litecore.dev>
 * @license   https://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 * @link      https://litecore.dev Official Website
 *
 */

	require_once 'includes/app_header.inc.php';

	// Recognize some destinations
	route::load('app://frontend/routes/url_*.inc.php');
	route::load('app://backend/routes/url_*.inc.php');

	// Append a route for last resort
	route::add('*', [
		'pattern' => '#^(.+)$#',
		'endpoint' => 'frontend',
		'controller' => 'app://frontend/pages/$1.inc.php',
	]);

	// Find destination for the current request
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
