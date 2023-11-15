<?php

	if (!empty($_GET['user_id'])) {
		$user = new ent_user($_GET['user_id']);
	} else {
		$user = new ent_user();
	}

	if (!$_POST) {
		$_POST = $user->data;
	}

	document::$title[] = !empty($user->data['id']) ? language::translate('title_edit_user', 'Edit User') : language::translate('title_create_new_user', 'Create New User');

	breadcrumbs::add(language::translate('title_users', 'Users'), document::ilink(__APP__.'/users'));
	breadcrumbs::add(!empty($user->data['id']) ? language::translate('title_edit_user', 'Edit User') : language::translate('title_create_new_user', 'Create New User'));

	if (isset($_POST['sign_in'])) {

		try {

			user::load($_GET['user_id']);

			session::$data['security.timestamp'] = time();
			session::regenerate_id();

			notices::add('success', strtr(language::translate('success_logged_in_as_user', 'You are now logged in as %firstname %lastname.'), [
				'%email' => user::$data['email'],
				'%firstname' => user::$data['firstname'],
				'%lastname' => user::$data['lastname'],
			]));

			header('Location: '. document::ilink('f:'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['save'])) {

		try {
			if (empty($_POST['newsletter'])) $_POST['newsletter'] = 0;

			$fields = [
				'status',
				'email',
				'password',
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
				'notes',
			];

			foreach ($fields as $field) {
				if (isset($_POST[$field])) $user->data[$field] = $_POST[$field];
			}

			$user->save();

			if (!empty($_POST['new_password'])) $user->set_password($_POST['new_password']);

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/users'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['delete'])) {

		try {
			if (empty($user->data['id'])) throw new Exception(language::translate('error_must_provide_user', 'You must provide a user'));

			$user->delete();

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/users'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

?>
<div class="card card-app">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo !empty($user->data['id']) ? language::translate('title_edit_user', 'Edit User') : language::translate('title_create_new_user', 'Create New User'); ?>
		</div>
	</div>

	<div class="card-body">
		<?php echo functions::form_begin('user_form', 'post', '', false, 'autocomplete="off"'); ?>

			<div class="row" style="max-width: 960px;">

				<div class="col-md-8">

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_status', 'Status'); ?></label>
							<?php echo functions::form_toggle('status', 'e/d', (file_get_contents('php://input') != '') ? true : '1'); ?>
						</div>
					</div>

					<?php if (!empty($user->data['id'])) { ?>
					<div class="form-group">
						<?php echo functions::form_button('sign_in', ['true', language::translate('text_sign_in_as_user', 'Sign in as user')], 'submit', 'class="btn btn-default btn-block"'); ?>
					</div>
					<?php } ?>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_email_address', 'Email Address'); ?></label>
							<?php echo functions::form_input_email('email', true); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_newsletter', 'Newsletter'); ?></label>
							<?php echo functions::form_checkbox('newsletter', ['1', language::translate('title_subscribe', 'Subscribe')], true); ?>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_company_name', 'Company Name'); ?></label>
							<?php echo functions::form_input_text('company', true); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_tax_id', 'Tax ID / VATIN'); ?></label>
							<?php echo functions::form_input_text('tax_id', true); ?>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_firstname', 'First Name'); ?></label>
							<?php echo functions::form_input_text('firstname', true); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_lastname', 'Last Name'); ?></label>
							<?php echo functions::form_input_text('lastname', true); ?>
						</div>
						</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_address1', 'Address 1'); ?></label>
							<?php echo functions::form_input_text('address1', true); ?>
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
							<?php echo functions::form_input_text('city', true); ?>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_country', 'Country'); ?></label>
							<?php echo functions::form_input_country('country_code', true); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_zone', 'Zone'); ?></label>
							<?php echo functions::form_input_zone('zone_code', fallback($_POST['country_code']), true); ?>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_phone_number', 'Phone Number'); ?></label>
							<?php echo functions::form_input_phone('phone', true); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo !empty($user->data['id']) ? language::translate('title_new_password', 'New Password') : language::translate('title_password', 'Password'); ?></label>
							<?php echo functions::form_input_password('new_password', '', 'autocomplete="new-password"'); ?>
						</div>
					</div>

					<?php if (!empty($user->data['id'])) { ?>
					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_last_ip_address', 'Last IP Address'); ?></label>
							<?php echo functions::form_input_text('last_ip_address', true, 'readonly'); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_last_hostname', 'Last Hostname'); ?></label>
							<?php echo functions::form_input_text('last_hostname', true, 'readonly'); ?>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_last_login', 'Last Login'); ?></label>
							<?php echo functions::form_input_text('date_login', true, 'readonly'); ?>
						</div>
					</div>
					<?php } ?>

					<div class="card-action">
						<?php echo functions::form_button('save', language::translate('title_save', 'Save'), 'submit', 'class="btn btn-success"', 'save'); ?>
						<?php echo !empty($user->data['id']) ? functions::form_button('delete', language::translate('title_delete', 'Delete'), 'submit', 'formnovalidate class="btn btn-danger" onclick="if (!confirm(&quot;'. language::translate('text_are_you_sure', 'Are you sure?') .'&quot;)) return false;"', 'delete') : false; ?>
						<?php echo functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"', 'cancel'); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label><?php echo language::translate('title_notes', 'Notes'); ?></label>
						<?php echo functions::form_textarea('notes', true, 'style="height: 450px;"'); ?>
					</div>

					<?php if (!empty($user->data['id'])) { ?>
					<table class="table table-striped table-hover data-table">
						<tbody>
							<tr>
								<td><?php echo language::translate('title_orders', 'Orders'); ?><br />
									<?php echo !empty($orders['total_count']) ? (int)$orders['total_count'] : '0'; ?>
								</td>
								<td><?php echo language::translate('title_total_sales', 'Total Sales'); ?><br />
									<?php echo currency::format(fallback($orders['total_sales'], 0), false, settings::get('site_currency_code')); ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php } ?>
				</div>
			</div>

		<?php echo functions::form_end(); ?>
	</div>
</div>

<script>

// Init

	if ($('select[name="country_code"]').find('option:selected').data('tax-id-format') != '') {
		$('select[name="country_code"]').closest('table').find('input[name="tax_id"]').attr('pattern', $('select[name="country_code"]').find('option:selected').data('tax-id-format'));
	} else {
		$('select[name="country_code"]').closest('table').find('input[name="tax_id"]').removeAttr('pattern');
	}

	if ($('select[name="country_code"]').find('option:selected').data('postcode-format') != '') {
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').attr('pattern', $('select[name="country_code"]').find('option:selected').data('postcode-format'));
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').prop('required', true);
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').closest('td').find('.required').show();
	} else {
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').removeAttr('pattern');
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').prop('required', false);
		$('select[name="country_code"]').closest('table').find('input[name="postcode"]').closest('td').find('.required').hide();
	}

	if ($('select[name="country_code"]').find('option:selected').data('phone-code') != '') {
		$('select[name="country_code"]').closest('table').find('input[name="phone"]').attr('placeholder', '+' + $('select[name="country_code"]').find('option:selected').data('phone-code'));
	} else {
		$('select[name="country_code"]').closest('table').find('input[name="phone"]').removeAttr('placeholder');
	}

	if (!$('select[name="zone_code"] option').length) $('select[name="zone_code"]').closest('td').css('opacity', 0.15);

// On change country

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
			url: '<?php echo document::ilink('countries/zones.json'); ?>?country_code=' + $(this).val(),
			type: 'get',
			cache: true,
			async: true,
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				if (console) console.warn(errorThrown.message);
			},
			success: function(data) {
				$('select[name="zone_code"]').html('');
				if (data) {
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