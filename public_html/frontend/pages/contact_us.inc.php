<?php
	document::$title[] = language::translate('contact:head_title', 'Contact Us');
	document::$description = language::translate('contact:meta_description', '');

	breadcrumbs::add(language::translate('title_contact_us', 'Contact Us'));

	if (!empty($_POST['send'])) {

		try {

			if (empty($_POST['firstname'])) {
				throw new Exception(language::translate('error_missing_firstname', 'You must provide a firstname'));
			}

			if (empty($_POST['lastname'])) {
				throw new Exception(language::translate('error_missing_lastname', 'You must provide a lastname'));
			}

			if (empty($_POST['subject'])) {
				throw new Exception(language::translate('error_missing_subject', 'You must provide a subject'));
			}

			if (empty($_POST['email'])) {
				throw new Exception(language::translate('error_missing_email', 'You must provide a valid email address'));
			}

			if (empty($_POST['message'])) {
				throw new Exception(language::translate('error_missing_message', 'You must provide a message'));
			}

			if (settings::get('captcha_enabled') && !functions::captcha_validate('contact_us')) {
				throw new Exception(language::translate('error_invalid_captcha', 'Invalid CAPTCHA given'));
			}

			$message = strtr(language::translate('email_user_feedback', implode("\r\n", [
				'** This is an email message from %sender_name <%sender_email> **',
				'',
				'%message',
			]), [
				'%sender_name' => $_POST['firstname'] .' '. $_POST['lastname'],
				'%sender_email' => $_POST['email'],
				'%message' => $_POST['message'],
			]));

			$email = new ent_email();
			$email->set_sender($_POST['email'], $_POST['firstname'] .' '. $_POST['lastname'])
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

	$_page = new ent_view('app://frontend/template/pages/contact.inc.php');
	//echo $_page->render();
	extract($_page->snippets);
?>
<main id="main" class="container">

	<div class="row layout">

		<div class="col-md-8">
			<section id="box-contact-us" class="card">
				<div class="card-body">
					{{notices}}

					<h1><?php echo language::translate('title_contact_us', 'Contact Us'); ?></h1>

					<?php echo functions::form_begin('contact_form', 'post'); ?>

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

						<div class="form-group">
							<label><?php echo language::translate('title_email_address', 'Email Address'); ?></label>
							<?php echo functions::form_input_email('email', true, 'required'); ?>
						</div>

						<div class="form-group">
							<label><?php echo language::translate('title_subject', 'Subject'); ?></label>
							<?php echo functions::form_input_text('subject', true, 'required'); ?>
						</div>

						<div class="form-group">
							<label><?php echo language::translate('title_message', 'Message'); ?></label>
							<?php echo functions::form_textarea('message', true, 'required style="height: 250px;"'); ?>
						</div>

						<?php if (settings::get('captcha_enabled')) { ?>
						<div class="form-group" style="max-width: 250px;">
							<label><?php echo language::translate('title_captcha', 'CAPTCHA'); ?></label>
							<?php echo functions::form_input_captcha('contact_us'); ?>
						</div>
						<?php } ?>

						<p><?php echo functions::form_button('send', language::translate('title_send', 'Send'), 'submit', 'style="font-weight: bold;"'); ?></p>

					<?php echo functions::form_end(); ?>
				</div>
			</section>
		</div>

		<div class="col-md-4">
			<article class="card">

				<div class="card-header">
					<h2 class="card-title"><?php echo language::translate('title_contact_details', 'Contact Details'); ?></h2>
				</div>

				<div class="card-body">
					<p class="address"><?php echo nl2br(settings::get('site_postal_address', '')); ?></p>

					<?php if (settings::get('site_phone')) { ?>
					<p class="phone"><?php echo functions::draw_fonticon('fa-phone'); ?> <a href="tel:<?php echo settings::get('site_phone'); ?>"><?php echo settings::get('site_phone'); ?></a></p>
					<?php } ?>

					<p class="email"><?php echo functions::draw_fonticon('fa-envelope'); ?> <a href="mailto:<?php echo settings::get('site_email'); ?>"><?php echo settings::get('site_email'); ?></a></p>
				</div>

			</article>
		</div>
	</div>

</main>
