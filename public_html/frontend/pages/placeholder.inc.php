<?php

	// Usage: <img src="placeholder?size=400x400&text=Lorem+Ipsum+Dolor">

	if (empty($_GET['size'])) {
		http_response_code(400);
		exit;
	}

	$buffer = implode(PHP_EOL, [
		'<?xml version="1.0" encoding="utf-8"?>',
		'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {width} {height}" width="{width}px" height="{height}px">',
		'	<g>',
		'		<rect width="100%" height="100%" fill="#cccccc"/>',
		'		<text x="50%" y="50%" text-anchor="middle" alignment-baseline="middle" font-size="32" dominant-baseline="middle" font-family="Arial" fill="#ffffff">{width}x{height}</text>',
		'		<text x="50%" y="60%" text-anchor="middle" alignment-baseline="middle" font-size="24" dominant-baseline="middle" font-family="Arial" fill="#ffffff">{text}</text>',
		'	</g>',
		'</svg>',
	]);

	$size = explode('x', $_GET['size']);

	$buffer = strtr($buffer, [
		'{width}' => (int)$size[0],
		'{height}' => (int)$size[1],
		'{text}' => isset($_GET['text']) ? $_GET['text'] : '',
	]);

	foreach([
		'Content-Type: image/svg+xml',
		'Content-Length: ' . strlen($buffer),
		'Expires: ' . gmdate('r', strtotime('+1 year')),
		'Cache-Control: public, max-age=' . 60*60*24*365,
		'Pragma: cache',
		'Content-Disposition: inline; filename="placeholder-'. (int)$size[0] .'x'. (int)$size[1] .'.svg"',
		'Content-Transfer-Encoding: binary',
		'Last-Modified: ' . gmdate('r', filemtime(__FILE__)),
		'ETag: ' . md5($buffer),
	] as $header) {
		header($header);
	}

	echo $buffer;
	exit;
