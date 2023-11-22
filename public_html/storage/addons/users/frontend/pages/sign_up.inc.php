<?php

	header('X-Robots-Tag: noindex');
	document::$title[] = language::translate('title_create_account', 'Create Account');

	breadcrumbs::add(language::translate('title_create_account', 'Create Account'));

	if (!$_POST) {
		$_POST = user::$data;
	}

	if (!empty(user::$data['id'])) {
		notices::add('errors', language::translate('error_already_logged_in', 'You are already logged in'));
	}

	if (!empty($_POST['register'])) {

		try {

			if (isset($_POST['email'])) {
				$_POST['email'] = strtolower($_POST['email']);
			}

			if (empty($_POST['newsletter'])) {
				$_POST['newsletter'] = 0;
			}

			if (settings::get('captcha_enabled')) {
				$captcha = functions::captcha_get('create_account');
				if (empty($captcha) || $captcha != $_POST['captcha']) {
					throw new Exception(language::translate('error_invalid_captcha', 'Invalid CAPTCHA given'));
				}
			}

			if (empty($_POST['email'])) {
				throw new Exception(language::translate('error_missing_email', 'You must enter an email address.'));
			}

			if (database::query("select id from ". DB_TABLE_PREFIX ."users where email = '". database::input($_POST['email']) ."' limit 1;")->num_rows) {
				throw new Exception(language::translate('error_email_already_registered', 'The email address already exists in our user database. Please login or select a different email address.'));
			}

			if (empty($_POST['password'])) {
				throw new Exception(language::translate('error_missing_password', 'You must enter a password.'));
			}

			if (!functions::password_check_strength($_POST['password'])) {
				throw new Exception(language::translate('error_password_not_strong_enough', 'The password is not strong enough'));
			}

			if (empty($_POST['confirmed_password'])) {
				throw new Exception(language::translate('error_missing_confirmed_password', 'You must confirm your password'));
			}

			if ($_POST['confirmed_password'] != $_POST['password']) {
				throw new Exception(language::translate('error_passwords_missmatch', 'The passwords did not match'));
			}

			if (empty($_POST['firstname'])) {
				throw new Exception(language::translate('error_missing_firstname', 'You must enter a first name.'));
			}

			if (empty($_POST['lastname'])) {
				throw new Exception(language::translate('error_missing_lastname', 'You must enter a last name.'));
			}

			if (empty($_POST['country_code'])) {
				throw new Exception(language::translate('error_missing_country', 'You must select a country.'));
			}

			if (empty($_POST['zone_code']) && reference::country($_POST['country_code'])->zones) {
				throw new Exception(language::translate('error_missing_zone', 'You must select a zone.'));
			}

			$user = new ent_user();

			$user->data['status'] = 1;

			$fields = [
				'email',
				'firstname',
				'lastname',
				'country_code',
				'zone_code',
				'newsletter',
			];

			foreach ($fields as $field) {
				if (isset($_POST[$field])) {
					$user->data[$field] = $_POST[$field];
				}
			}

			$user->set_password($_POST['password']);

			$user->save();

			database::query(
				"update ". DB_TABLE_PREFIX ."users
				set last_ip_address = '". database::input($_SERVER['REMOTE_ADDR']) ."',
						last_hostname = '". database::input(gethostbyaddr($_SERVER['REMOTE_ADDR'])) ."',
						last_user_agent = '". database::input($_SERVER['HTTP_USER_AGENT']) ."'
				where id = ". (int)$user->data['id'] ."
				limit 1;"
			);

			user::load($user->data['id']);

			$aliases = [
				'%site_name' => settings::get('site_name'),
				'%site_link' => document::ilink(''),
				'%user_id' => $user->data['id'],
				'%user_firstname' => $user->data['firstname'],
				'%user_lastname' => $user->data['lastname'],
				'%user_email' => $user->data['email'],
			];

			$subject = language::translate('email_subject_user_account_created', 'User Account Created');
			$message = strtr(language::translate('email_account_created', "Thank you %user_firstname %user_lastname for signing up to %site_name!\r\n\r\nYour account has now been created. \r\n\r\nSign in using your email address %user_email.\r\n\r\n%site_name\r\n\r\n%site_link"), $aliases);

			$email = new ent_email();
			$email->add_recipient($_POST['email'], $_POST['firstname'] .' '. $_POST['lastname'])
						->set_subject($subject)
						->add_body($message)
						->send();

			notices::add('success', language::translate('success_your_account_has_been_created', 'Your account has been created.'));
			header('Location: '. document::ilink(''));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	$_page = new ent_view();

	$_page->snippets = [
		'consent' => null,
	];

	if ($privacy_policy_id = settings::get('privacy_policy')) {

			$aliases = [
				'%privacy_policy_link' => document::href_ilink('information', ['page_id' => $privacy_policy_id]),
			];

			$_page->snippets['consent'] = strtr(language::translate('consent:privacy_policy', 'I have read the <a href="%privacy_policy_link" target="_blank">Privacy Policy</a> and I consent.'), $aliases);
	}

	//echo $_page->render(FS_DIR_TEMPLATE . 'pages/create_account.inc.php');
	extract($_page->snippets);

	$company_type_options = [
		'individual' => language::translate('title_individual', 'Individual'),
		'business' => language::translate('title_business', 'Business'),
	];
?>
<section id="box-sign-up" class="card">

	<div class="card-header">
		<h1 class="card-title"><?php echo language::translate('title_sign_up', 'Sign Up'); ?></h1>
	</div>

	<div class="card-body">
		<?php echo functions::form_begin('create_account_form', 'post', false, false, 'style="max-width: 640px;"'); ?>

			<div class="row">
				<div class="form-group col-6">
					<label><?php echo language::translate('title_username', 'Username'); ?></label>
					<?php echo functions::form_input_text('username', true, 'required'); ?>
				</div>

				<div class="form-group col-6">
					<label><?php echo language::translate('title_email', 'Email'); ?></label>
					<?php echo functions::form_input_email('email', true, 'required'); ?>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-6">
					<label><?php echo language::translate('title_firstname', 'First Name'); ?></label>
					<?php echo functions::form_input_text('firstname', true, 'required'); ?>
				</div>

				<div class="form-group col-6">
					<label><?php echo language::translate('title_lastname', 'Last Name'); ?></label>
					<?php echo functions::form_input_text('lastname', true, 'required'); ?>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-6">
					<label><?php echo language::translate('title_country', 'Country'); ?></label>
					<?php echo functions::form_select_country('country_code', true, 'required'); ?>
				</div>

				<div class="form-group col-6">
					<label><?php echo language::translate('title_zone_state_province', 'Zone/State/Province'); ?></label>
					<?php echo functions::form_select_zone('zone_code', fallback($_POST['country_code']), true, 'required'); ?>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-6">
					<label><?php echo language::translate('title_desired_password', 'Desired Password'); ?></label>
					<?php echo functions::form_input_password('password', '', 'required'); ?>
				</div>

				<div class="form-group col-6">
					<label><?php echo language::translate('title_confirm_password', 'Confirm Password'); ?></label>
					<?php echo functions::form_input_password('confirmed_password', '', 'required'); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo functions::form_checkbox('newsletter', ['1', language::translate('consent_newsletter', 'I would like to be notified occasionally via e-mail when there are new products or campaigns.')], true); ?>
			</div>

			<?php if ($consent) { ?>
			<div class="form-group">
				<?php echo functions::form_checkbox('terms_agreed', ['1', $consent], true, 'required'); ?>
			</div>
			<?php } ?>

			<?php if (settings::get('captcha_enabled')) { ?>
			<div class="row">
				<div class="form-group col-6">
					<label><?php echo language::translate('title_captcha', 'CAPTCHA'); ?></label>
					<?php echo functions::form_input_captcha('captcha', 'create_account', 'required'); ?>
				</div>
			</div>
			<?php } ?>

			<div class="btn-group">
				<?php echo functions::form_button('register', language::translate('title_register', 'Register')); ?>
			</div>

		<?php echo functions::form_end(); ?>
	</div>
</section>

<script>
	$('select[name="country_code"]').change(function(e) {

		$('body').css('cursor', 'wait');
		$.ajax({
			url: '<?php echo document::ilink('ajax/zones.json'); ?>?country_code=' + $(this).val(),
			type: 'get',
			cache: true,
			async: true,
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				if (console) console.warn(errorThrown.message);
			},
			success: function(data) {
				$("select[name='zone_code']").html('');
				if (data.length) {
					$('select[name="zone_code"]').prop('disabled', false);
					$.each(data, function(i, zone) {
						$('select[name="zone_code"]').append('<option value="'+ zone.code +'">'+ zone.name +'</option>');
					});
				} else {
					$('select[name="zone_code"]').prop('disabled', true);
				}
			},
			complete: function() {
				$('body').css('cursor', 'auto');
			}
		});
	});
</script>