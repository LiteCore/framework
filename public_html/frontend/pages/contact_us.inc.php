<?php
  document::$snippets['title'][] = language::translate('contact:head_title', 'Contact');
  document::$snippets['description'] = language::translate('contact:meta_description', '');

  if (!empty($_GET['page_id'])) {
    breadcrumbs::add(language::translate('title_contact', 'Contact'), document::ilink('contact'));
  } else {
    breadcrumbs::add(language::translate('title_contact', 'Contact'));
  }

  if (!$_POST) {
    $_POST = [
      'firstname' => user::$data['firstname'],
      'lastname' => user::$data['lastname'],
      'email' => user::$data['email'],
    ];
  }

  if (!empty($_POST['send'])) {

    try {

      if (settings::get('captcha_enabled')) {
        $captcha = functions::captcha_get('contact_us');

        if (empty($captcha) || $captcha != $_POST['captcha']) {
          throw new Exception(language::translate('error_invalid_captcha', 'Invalid CAPTCHA given'));
        }
      }


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

    // Collect scraps
      if (empty(user::$data['id'])) {
        user::$data = array_replace(user::$data, array_intersect_key(array_filter(array_diff_key($_POST, array_flip(['id']))), user::$data));
      }

      $message = strtr(language::translate('email_user_feedback', "** This is an email message from %sender_name <%sender_email> **\r\n\r\n%message"), [
        '%sender_name' => $_POST['firstname'] .' '. $_POST['lastname'],
        '%sender_email' => $_POST['email'],
        '%message' => $_POST['message'],
      ]);

      $email = new ent_email();
      $email->set_sender($_POST['email'], $_POST['firstname'] .' '. $_POST['lastname'])
            ->add_recipient(settings::get('store_email'), settings::get('store_name'))
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

  $_page = new ent_view();

  //echo $_page->render(FS_DIR_TEMPLATE . 'pages/contact.inc.php');
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
                <label><?php echo language::translate('title_name', 'Name'); ?></label>
                <?php echo functions::form_text_field('name', true, 'required'); ?>
              </div>

              <div class="form-group col-md-6">
                <label><?php echo language::translate('title_email_address', 'Email Address'); ?></label>
                <?php echo functions::form_email_field('email', true, 'required'); ?>
              </div>
            </div>

            <div class="form-group">
              <label><?php echo language::translate('title_subject', 'Subject'); ?></label>
              <?php echo functions::form_text_field('subject', true, 'required'); ?>
            </div>

            <div class="form-group">
              <label><?php echo language::translate('title_message', 'Message'); ?></label>
              <?php echo functions::form_textarea('message', true, 'required style="height: 250px;"'); ?>
            </div>

            <?php if (settings::get('captcha_enabled')) { ?>
            <div class="form-group" style="max-width: 250px;">
              <label><?php echo language::translate('title_captcha', 'CAPTCHA'); ?></label>
              <?php echo functions::form_captcha_field('captcha', 'contact_us', 'required'); ?>
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
          <p class="address"><?php echo nl2br(settings::get('store_postal_address')); ?></p>

          <?php if (settings::get('store_phone')) { ?>
          <p class="phone"><?php echo functions::draw_fonticon('fa-phone'); ?> <a href="tel:<?php echo settings::get('store_phone'); ?>"><?php echo settings::get('store_phone'); ?></a></p>
          <?php } ?>

          <p class="email"><?php echo functions::draw_fonticon('fa-envelope'); ?> <a href="mailto:<?php echo settings::get('store_email'); ?>"><?php echo settings::get('store_email'); ?></a></p>
        </div>

      </article>
    </div>
  </div>

</main>