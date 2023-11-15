<?php

	header('X-Robots-Tag: noindex');

	document::$title[] = language::translate('title_sign_in', 'Sign In');

	breadcrumbs::add(language::translate('title_sign_in', 'Sign In'));

	if (!$_POST) {
		$_POST['email'] = user::$data['email'];
	}

	if (empty($_POST['remember_me'])) $_POST['remember_me'] = false;

	if (!empty(user::$data['id'])) {
		notices::add('notices', language::translate('text_already_logged_in', 'You are already logged in'));
	}

	if (!empty($_POST['login'])) {

		try {

			if (!empty($_COOKIE['user_remember_me'])) {
				header('Set-Cookie: user_remember_me=; Path='. WS_DIR_APP .'; Max-Age=-1; HttpOnly; SameSite=Lax', false);
			}

			if (empty($_POST['email']) || empty($_POST['password'])) {
				throw new Exception(language::translate('error_missing_login_credentials', 'You must provide both your email address and password'));
			}

			$user = database::query(
				"select * from ". DB_TABLE_PREFIX ."users
				where lower(email) = '". database::input(strtolower($_POST['email'])) ."'
				limit 1;"
			)->fetch();

			if (!$user) {
				throw new Exception(language::translate('error_email_not_found_in_database', 'The email does not exist in our database'));
			}

			if (empty($user['status'])) {
				throw new Exception(language::translate('error_user_account_disabled_or_not_activated', 'The user account is disabled or not activated'));
			}

			if (!empty($user['date_blocked_until']) && date('Y-m-d H:i:s') < $user['date_blocked_until']) {
				throw new Exception(sprintf(language::translate('error_account_is_blocked', 'The account is blocked until %s'), language::strftime(language::$selected['format_datetime'], strtotime($user['date_blocked_until']))));
			}

			if (!password_verify($_POST['password'], $user['password_hash'])) {

				if (++$user['login_attempts'] < 3) {

					database::query(
						"update ". DB_TABLE_PREFIX ."users
						set login_attempts = login_attempts + 1
						where id = ". (int)$user['id'] ."
						limit 1;"
					);

					throw new Exception(language::translate('error_wrong_password_or_account', 'Wrong password or the account does not exist'));

				} else {

					database::query(
						"update ". DB_TABLE_PREFIX ."users
						set login_attempts = 0,
						date_blocked_until = '". date('Y-m-d H:i:00', strtotime('+15 minutes')) ."'
						where id = ". (int)$user['id'] ."
						limit 1;"
					);

					throw new Exception(strtr(language::translate('error_account_has_been_blocked', 'The account has been temporarily blocked %n minutes'), ['%n' => 15, '%d' => 15]));
				}
			}

			if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
				database::query(
					"update ". DB_TABLE_PREFIX ."users
					set password_hash = '". database::input(password_hash($_POST['password'], PASSWORD_DEFAULT)) ."'
					where id = ". (int)$user['id'] ."
					limit 1;"
				);
			}

			database::query(
				"update ". DB_TABLE_PREFIX ."users
				set login_attempts = 0,
					num_logins = num_logins + 1,
					last_ip_address = '". database::input($_SERVER['REMOTE_ADDR']) ."',
					last_hostname = '". database::input(gethostbyaddr($_SERVER['REMOTE_ADDR'])) ."',
					last_user_agent = '". database::input($_SERVER['HTTP_USER_AGENT']) ."',
					date_login = '". date('Y-m-d H:i:s') ."'
				where id = ". (int)$user['id'] ."
				limit 1;"
			);

			user::load($user['id']);

			session::$data['user_security_timestamp'] = time();
			session::regenerate_id();

			if (!empty($_POST['remember_me'])) {
				$checksum = sha1($user['email'] . $user['password_hash'] . $_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ? $_SERVER['HTTP_USER_AGENT'] : ''));
				header('Set-Cookie: user_remember_me='. $user['email'] .':'. $checksum .'; Path='. WS_DIR_APP .'; Expires='. gmdate('r', strtotime('+3 months')) .'; HttpOnly; SameSite=Lax', false);
			}

			notices::add('success', strtr(language::translate('success_logged_in_as_user', 'You are now logged in as %firstname %lastname.'), [
				'%email' => user::$data['email'],
				'%firstname' => user::$data['firstname'],
				'%lastname' => user::$data['lastname'],
			]));

			if (!empty($_POST['redirect_url'])) {
				$redirect_url = new ent_link($_POST['redirect_url']);
				$redirect_url->host = '';
			} else {
				$redirect_url = document::ilink('');
			}

			header('Location: '. $redirect_url);
			exit;

		} catch (Exception $e) {
			http_response_code(401); // Troublesome with HTTP Auth (e.g. .htpasswd)
			notices::add('errors', $e->getMessage());
		}
	}

	//$_page = new ent_view();
	//echo $_page->render(FS_DIR_TEMPLATE . 'pages/sign_in.inc.php');
?>
<div class="row">
	<section id="box-sign-in" class="card col-md-6" style="margin-bottom: 0;">

		<div class="card-header">
			<h2 class="card-title"><?php echo language::translate('title_sign_in', 'Sign In'); ?></h2>
		</div>

		<div class="card-body">
			<?php echo functions::form_begin('sign_in_form', 'post', document::ilink('sign_in')); ?>
				<?php echo functions::form_input_hidden('redirect_url', true); ?>

				<div class="form-group">
					<?php echo functions::form_input_email('email', true, 'placeholder="'. language::translate('title_email_address', 'Email Address') .'"'); ?>
				</div>

				<div class="form-group">
					<?php echo functions::form_input_password('password', '', 'placeholder="'. language::translate('title_password', 'Password') .'"'); ?>
				</div>

				<div class="form-group">
					<?php echo functions::form_checkbox('remember_me', ['1', language::translate('title_remember_me', 'Remember Me')], true); ?>
				</div>

				<div>
					<?php echo functions::form_button('login', language::translate('title_sign_in', 'Sign In'), 'submit', 'class="btn btn-default btn-block"'); ?>
				</div>

				<p class="text-center">
					<a href="<?php echo document::ilink('reset_password', ['email' => fallback($_POST['email'])]); ?>"><?php echo language::translate('text_lost_your_password', 'Lost your password?'); ?></a>
				</p>

			<?php echo functions::form_end(); ?>
		</div>
	</section>

	<section id="box-sign-up-note" class="card col-md-6" style="margin-bottom: 0;">
		<div class="card-header">
			<h2 class="card-title"><?php echo language::translate('title_sign_up', 'Sign Up'); ?></h2>
		</div>

		<div class="card-body">
			<ul>
				<li></li>
			</ul>

			<div>
				<a class="btn btn-default" href="<?php echo document::href_ilink('sign_up'); ?>"><?php echo language::translate('title_sign_up_now', 'Sign Up Now'); ?></a>
			</div>
		</div>
	</section>
</div>
