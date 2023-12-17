<?php
	if (!empty($_POST['send'])) {

		try {
			if (settings::get('captcha_enabled')) {
				$captcha = functions::captcha_get('contact_us');
				if (empty($captcha) || $captcha != $_POST['captcha']) throw new Exception(language::translate('error_invalid_captcha', 'Invalid CAPTCHA given'));
			}

			if (empty($_POST['name'])) throw new Exception(language::translate('error_must_enter_name', 'You must enter a name'));
			if (empty($_POST['subject'])) throw new Exception(language::translate('error_must_enter_subject', 'You must enter a subject'));
			if (empty($_POST['email'])) throw new Exception(language::translate('error_must_enter_email', 'You must enter a valid email address'));
			if (empty($_POST['message'])) throw new Exception(language::translate('error_must_enter_message', 'You must enter a message'));

			$message = strtr(language::translate('email_user_feedback', "** This is an email message from %sender_name <%sender_email> **\r\n\r\n%message"), [
				'%sender_name' => $_POST['name'],
				'%sender_email' => $_POST['email'],
				'%message' => $_POST['message'],
			]);

			$email = new ent_email();
			$email->set_sender($_POST['email'], $_POST['name'])
						->add_recipient(settings::get('site_email'), settings::get('site_name'))
						->set_subject($_POST['subject'])
						->add_body($message);

			$result = $email->send();

			if (!$result) {
				throw new Exception(language::translate('error_sending_email_for_unknown_reason', 'The email could not be sent for an unknown reason'));
			}

			notices::add('success', language::translate('success_your_email_was_sent', 'Your email has successfully been sent'));
			header('Location: '. document::link());
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	$box_contact_us = new ent_view(FS_DIR_TEMPLATE . 'partials/box_contact_us.inc.php');

	//echo $box_contact_us;
	extract($box_contact_us->snippets);
?>
<section id="box-contact-us" class="card">

	<div class="card-body">
		<div class="row">
			<div class="col-md-8">

				<h1 style="margin-top: 0;"><?php echo language::translate('title_contact_us', 'Contact Us'); ?></h1>

				<?php echo functions::form_draw_form_begin('contact_form', 'post'); ?>

					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_firstname', 'First Name'); ?></label>
							<?php echo functions::form_input_draw_text('firstname', true, 'required'); ?>
						</div>

						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_lastname', 'Last Name'); ?></label>
							<?php echo functions::form_input_draw_text('lastname', true, 'required'); ?>
						</div>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_email_address', 'Email Address'); ?></label>
						<?php echo functions::form_input_draw_email('email', true, 'required'); ?>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_subject', 'Subject'); ?></label>
						<?php echo functions::form_input_draw_text('subject', true, 'required'); ?>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_message', 'Message'); ?></label>
						<?php echo functions::form_draw_textarea('message', true, 'required style="height: 250px;"'); ?>
					</div>

					<?php if (settings::get('captcha_enabled')) { ?>
					<div class="row">
						<div class="form-group col-md-6">
							<label><?php echo language::translate('title_captcha', 'CAPTCHA'); ?></label>
							<?php echo functions::form_input_draw_captcha('captcha', 'contact_us', 'required'); ?>
						</div>
					</div>
					<?php } ?>

					<p><?php echo functions::form_draw_button('send', language::translate('title_send', 'Send'), 'submit', 'style="font-weight: bold;"'); ?></p>

				<?php echo functions::form_draw_form_end(); ?>
			</div>

			<div class="col-md-4">
				<h2><?php echo language::translate('title_contact_details', 'Contact Details'); ?></h2>

				<p class="address"><?php echo nl2br(settings::get('site_postal_address')); ?></p>

				<?php if (settings::get('site_phone')) { ?><p class="phone"><?php echo functions::draw_fonticon('fa-phone'); ?> <a href="tel:<?php echo settings::get('site_site_phone'); ?>"><?php echo settings::get('site_phone'); ?></a></p><?php } ?>

				<p class="email"><?php echo functions::draw_fonticon('fa-envelope'); ?> <a href="mailto:<?php echo settings::get('site_email'); ?>"><?php echo settings::get('site_email'); ?></a></p>
			</div>

		</div>
	</div>
</section>