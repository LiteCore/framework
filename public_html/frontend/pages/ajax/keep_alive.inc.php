<?php

	class_exists('session', true);

	header('X-Robots-Tag: noindex');
	header('Content-Type: text/plain; charset=UTF-8');

	$replies = [
		'Oh it\'s you again!',
		'I see you are still here.',
		'Do you come here a lot?',
		'Nice to see you are still awake.',
		'It\'s great to have you here.',
	];

	echo $replies[array_rand($replies)];
	exit;
