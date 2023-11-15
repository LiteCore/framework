<?php

	header('X-Robots-Tag: noindex');
	document::$head_tags['noindex'] = '<meta name="robots" content="noindex" />';

	user::require_login();

	document::$title[] = language::translate('title_edit_account', 'Edit Account');

	breadcrumbs::add(language::translate('title_account', 'Account'), '');
	breadcrumbs::add(language::translate('title_edit_account', 'Edit Account'));

	$user = new ent_user(user::$data['id']);

	if (!$_POST) {
		$_POST = $user->data;
	}

	if (isset($_POST['save_account'])) {

		try {
			if (isset($_POST['email'])) $_POST['email'] = strtolower($_POST['email']);

			if (database::query("select id from ". DB_TABLE_PREFIX ."users where email = '". database::input($_POST['email']) ."' and id != ". (int)$user->data['id'] ." limit 1;")->num_rows) throw new Exception(language::translate('error_email_already_registered', 'The email address already exists in our user database.'));

			if (empty($_POST['email'])) throw new Exception(language::translate('error_email_missing', 'You must enter an email address.'));

			if (!password_verify($_POST['password'], user::$data['password_hash'])) {
				throw new Exception(language::translate('error_wrong_password', 'Wrong password'));
			}

			if (!empty($_POST['new_password'])) {
				if (empty($_POST['confirmed_password'])) throw new Exception(language::translate('error_missing_confirmed_password', 'You must confirm your password.'));
				if (isset($_POST['new_password']) && isset($_POST['confirmed_password']) && $_POST['new_password'] != $_POST['confirmed_password']) throw new Exception(language::translate('error_passwords_missmatch', 'The passwords did not match.'));
				if (!functions::password_check_strength($_POST['password'])) throw new Exception(language::translate('error_password_not_strong_enough', 'The password is not strong enough'));
			}

			$fields = [
				'email',
			];

			foreach ($fields as $field) {
				if (isset($_POST[$field])) $user->data[$field] = $_POST[$field];
			}

			if (!empty($_POST['new_password'])) {
				$user->set_password($_POST['new_password']);
			}

			$user->data['password_reset_token'] = '';
			$user->data['date_expire_sessions'] = date('Y-m-d H:i:s');
			$user->save();

			user::load($user->data['id']);

			session::regenerate_id();
			session::$data['user_security_timestamp'] = strtotime($user->data['date_expire_sessions']);

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::link());
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['save_details'])) {

		try {
			if (!isset($_POST['newsletter'])) $_POST['newsletter'] = 0;

			if (empty($_POST['firstname'])) throw new Exception(language::translate('error_missing_firstname', 'You must enter a first name.'));
			if (empty($_POST['lastname'])) throw new Exception(language::translate('error_missing_lastname', 'You must enter a last name.'));
			if (empty($_POST['address1'])) throw new Exception(language::translate('error_missing_address1', 'You must enter an address.'));
			if (empty($_POST['city'])) throw new Exception(language::translate('error_missing_city', 'You must enter a city.'));
			if (empty($_POST['postcode']) && !empty($_POST['country_code']) && reference::country($_POST['country_code'])->postcode_format) throw new Exception(language::translate('error_missing_postcode', 'You must enter a postcode.'));
			if (empty($_POST['country_code'])) throw new Exception(language::translate('error_missing_country', 'You must select a country.'));
			if (empty($_POST['zone_code']) && settings::get('user_field_zone') && reference::country($_POST['country_code'])->zones) throw new Exception(language::translate('error_missing_zone', 'You must select a zone.'));

			$fields = [
				'tax_id',
				'company',
				'firstname',
				'lastname',
				'address1',
				'address2',
				'postcode',
				'city',
				'country_code',
				'zone_code',
				'phone',
				'newsletter',
			];

			foreach ($fields as $field) {
				if (isset($_POST[$field])) $user->data[$field] = $_POST[$field];
			}

			$user->save();
			user::$data = $user->data;

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::link());
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	//$_page = new ent_view();
	//echo $_page->render(FS_DIR_TEMPLATE . 'pages/edit_account.inc.php');
?>
<main id="main" class="container">
	<div class="row layout">
		<div class="col-md-3">
			<div id="sidebar">
				<?php include 'app://frontend/partials/box_account_links.inc.php'; ?>
			</div>
		</div>

		<div class="col-md-9">
			<div id="content">
				{{notices}}

				<section id="box-edit-account" class="card">
					<div class="card-header">
						<h1 class="card-title"><?php echo language::translate('title_sign_in_and_security', 'Sign-In and Security'); ?></h1>
					</div>

					<div class="card-body">
						<?php echo functions::form_begin('customer_account_form', 'post', null, false, 'style="max-width: 640px;"'); ?>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_email_address', 'Email Address'); ?></label>
									<?php echo functions::form_input_email('email', true, 'required'); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_password', 'Password'); ?></label>
									<?php echo functions::form_input_password('password', '', 'required'); ?>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_new_password', 'New Password'); ?> (<?php echo language::translate('text_or_leave_blank', 'Or leave blank'); ?>)</label>
									<?php echo functions::form_input_password('new_password', ''); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_confirm_new_password', 'Confirm New Password'); ?></label>
									<?php echo functions::form_input_password('confirmed_password', ''); ?>
								</div>
							</div>

							<p><?php echo functions::form_button('save_account', language::translate('title_save', 'Save')); ?></p>

						<?php echo functions::form_end(); ?>
					</div>
				</section>

				<section id="box-edit-details" class="card">
					<div class="card-header">
						<h1 class="card-title"><?php echo language::translate('title_customer_profile', 'Customer Profile'); ?></h1>
					</div>

					<div class="card-body">
						<?php echo functions::form_begin('customer_details_form', 'post', null, false, 'style="max-width: 640px;"'); ?>

							<div class="form-group">
								<?php echo functions::form_toggle_buttons('type', ['individual' => language::translate('title_individual', 'Individual'), 'company' => language::translate('title_company', 'Company')], empty($_POST['type']) ? 'individual' : true); ?>
							</div>

							<div class="company-details" <?php echo (empty($_POST['type']) || $_POST['type'] == 'individual') ? 'style="display: none;"' : ''; ?>>
								<div class="row">
									<div class="form-group col-6">
										<label><?php echo language::translate('title_company_name', 'Company Name'); ?></label>
										<?php echo functions::form_input_text('company', true, 'required'); ?>
									</div>

									<div class="form-group col-6">
										<label><?php echo language::translate('title_tax_id', 'Tax ID'); ?></label>
										<?php echo functions::form_input_text('tax_id', true); ?>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_firstname', 'First Name'); ?></label>
									<?php echo functions::form_input_text('firstname', true, 'required'); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_lastname', 'Last Name'); ?></label>
									<?php echo functions::form_input_text('lastname', true, 'required'); ?>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_address1', 'Address 1'); ?></label>
									<?php echo functions::form_input_text('address1', true, 'required'); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_address2', 'Address 2'); ?></label>
									<?php echo functions::form_input_text('address2', true); ?>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_postcode', 'Postal Code'); ?></label>
									<?php echo functions::form_input_text('postcode', true); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_city', 'City'); ?></label>
									<?php echo functions::form_input_text('city', true, 'required'); ?>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_country', 'Country'); ?></label>
									<?php echo functions::form_input_country('country_code', true, 'required'); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_zone_state_province', 'Zone/State/Province'); ?></label>
									<?php echo form_input_zone('zone_code', fallback($_POST['country_code']), true, 'required'); ?>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_phone_number', 'Phone Number'); ?></label>
									<?php echo functions::form_input_phone('phone', true, 'required placeholder="'. (isset($_POST['country_code']) ? reference::country($_POST['country_code'])->phone_code : '') .'"'); ?>
								</div>
							</div>

							<div class="form-group">
								<?php echo functions::form_checkbox('newsletter', ['1', language::translate('consent_newsletter', 'I would like to be notified occasionally via e-mail when there are new products or campaigns.')], true); ?>
							</div>

							<div>
								<?php echo functions::form_button('save_details', language::translate('title_save', 'Save')); ?>
							</div>

						<?php echo functions::form_end(); ?>
					</div>
				</section>
			</div>
		</div>
	</div>
</main>

<script>
	$('input[name="type"]').change(function(){
		if ($(this).val() == 'company') {
			$('.company-details :input').prop('disabled', false);
			$('.company-details').slideDown('fast');
		} else {
			$('.company-details :input').prop('disabled', true);
			$('.company-details').slideUp('fast');
		}
	}).first().trigger('change');

	$('form[name="customer_form"]').on('input', ':input', function() {
		if ($(this).val() == '') return;
		$('body').css('cursor', 'wait');
		$.ajax({
			url: '<?php echo document::ilink('ajax/get_address.json'); ?>?trigger='+$(this).attr('name'),
			type: 'post',
			data: $(this).closest('form').serialize(),
			cache: false,
			async: true,
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				if (console) console.warn(errorThrown.message);
			},
			success: function(data) {
				if (data['alert']) {
					alert(data['alert']);
					return;
				}
				$.each(data, function(key, value) {
					console.log(key +' '+ value);
					if ($('input[name="'+key+'"]').length && $('input[name="'+key+'"]').val() == '') $('input[name="'+key+'"]').val(data[key]);
				});
			},
			complete: function() {
				$('body').css('cursor', 'auto');
			}
		});
	});

	$('select[name="country_code"]').change(function(e) {

		if ($(this).find('option:selected').data('tax-id-format')) {
			$('input[name="tax_id"]').attr('pattern', $(this).find('option:selected').data('tax-id-format'));
		} else {
			$('input[name="tax_id"]').removeAttr('pattern');
		}

		if ($(this).find('option:selected').data('postcode-format')) {
			$('input[name="postcode"]').attr('pattern', $(this).find('option:selected').data('postcode-format'));
		} else {
			$('input[name="postcode"]').removeAttr('pattern');
		}

		if ($(this).find('option:selected').data('phone-code')) {
			$('input[name="phone"]').attr('placeholder', '+' + $(this).find('option:selected').data('phone-code'));
		} else {
			$('input[name="phone"]').removeAttr('placeholder');
		}

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

	if ($('select[name="country_code"] option:selected').data('tax-id-format')) {
		$('input[name="tax_id"]').attr('pattern', $('select[name="country_code"] option:selected').data('tax-id-format'));
	} else {
		$('input[name="tax_id"]').removeAttr('pattern');
	}

	if ($('select[name="country_code"] option:selected').data('postcode-format')) {
		$('input[name="postcode"]').attr('pattern', $('select[name="country_code"] option:selected').data('postcode-format'));
	} else {
		$('input[name="postcode"]').removeAttr('pattern');
	}

	if ($('select[name="country_code"] option:selected').data('phone-code')) {
		$('input[name="phone"]').attr('placeholder', '+' + $('select[name="country_code"] option:selected').data('phone-code'));
	} else {
		$('input[name="phone"]').removeAttr('placeholder');
	}
</script>