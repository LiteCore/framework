<?php
	header('X-Robots-Tag: noindex');

	cart::reset();
	user::reset();

	session::regenerate_id();
	session::$data['cart']['uid'] = null;

	header('Set-Cookie: cart[uid]=; Path='. WS_DIR_APP .'; Max-Age=-1; SameSite=Lax', false);

	if (!empty($_COOKIE['user_remember_me'])) {
		header('Set-Cookie: user_remember_me=; Path='. WS_DIR_APP .'; Max-Age=-1; HttpOnly; SameSite=Lax', false);
	}

	notices::add('success', language::translate('description_logged_out', 'You are now logged out.'));

	header('Location: ' . document::ilink(''));
	exit;
