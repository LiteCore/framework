<?php

	document::$title[] = language::translate('title_csv_import_export', 'CSV Import/Export');

	breadcrumbs::add(language::translate('title_csv_import_export', 'CSV Import/Export'));

	if (isset($_POST['import'])) {

		try {

			if (empty($_POST['type'])) throw new Exception(language::translate('error_must_select_type', 'You must select type'));

			if (!isset($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
				throw new Exception(language::translate('error_must_select_file_to_upload', 'You must select a file to upload'));
			}

			$csv = file_get_contents($_FILES['file']['tmp_name']);

			if (!$csv = functions::csv_decode($csv, $_POST['delimiter'], $_POST['enclosure'], $_POST['escapechar'], $_POST['charset'])) {
				throw new Exception(language::translate('error_failed_decoding_csv', 'Failed decoding CSV'));
			}

			if (!empty($_POST['reset'])) {

				echo PHP_EOL
					 . 'Wiping data...' . PHP_EOL . PHP_EOL;

				switch ($_POST['type']) {

					case 'users':

						database::multi_query(
							"truncate ". DB_TABLE_PREFIX ."users;
							update ". DB_TABLE_PREFIX ."orders set user_id = 0;"
						);

						break;

					case 'newsletter_recipients':

						database::multi_query(
							"truncate ". DB_TABLE_PREFIX ."newsletter_recipients;"
						);

						break;
				}
			}

			$updated = 0;
			$inserted = 0;
			$line = 1;

			foreach ($csv as $row) {
				$line++;

				switch ($_POST['type']) {

					case 'users':

					// Find user
						if (!empty($row['id']) && $user = database::query("select id from ". DB_TABLE_PREFIX ."users where id = ". (int)$row['id'] ." limit 1;")->fetch()) {
							$user = new ent_user($user['id']);

						} else if (!empty($row['code']) && $user = database::query("select id from ". DB_TABLE_PREFIX ."users where code = '". database::input($row['code']) ."' limit 1;")->fetch()) {
							$user = new ent_user($user['id']);

						} else if (!empty($row['email']) && $user = database::query("select id from ". DB_TABLE_PREFIX ."users where email = '". database::input($row['email']) ."' limit 1;")->fetch()) {
							$user = new ent_user($user['id']);
						}

						if (!empty($user->data['id'])) {

							if (empty($_POST['overwrite'])) {
								echo "Skip updating existing user on line $line" . PHP_EOL;
								continue 2;
							}

							echo 'Updating existing user '. ((!empty($row['firstname']) && !empty($row['lastname'])) ? $row['firstname'] .' '. $row['lastname'] : "on line $line") . PHP_EOL;
							$updated++;

						} else {

							if (empty($_POST['insert'])) {
								echo "Skip inserting new user on line $line" . PHP_EOL;
								continue 2;
							}

							echo 'Inserting new user: '. ((!empty($row['firstname']) && !empty($row['lastname'])) ? $row['firstname'] .' '. $row['lastname'] : "on line $line") . PHP_EOL;
							$inserted++;

							if (!empty($row['id'])) {
								database::query(
									"insert into ". DB_TABLE_PREFIX ."users (id, date_created)
									values (". (int)$row['id'] .", '". date('Y-m-d H:i:s') ."');"
								);
								$user = new ent_user($row['id']);
							} else {
								$user = new ent_user();
							}
						}

					// Set user data
						$fields = [
							'email',
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
							if (isset($row[$field])) $user->data[$field] = $row[$field];
						}

						if (!empty($row['new_password'])) $user->set_password($row['new_password']);

						$user->save();

						break;

					case 'newsletter_recipients':

					// Find newsletter recipient
						if (!empty($row['id']) && $recipient = database::query("select id from ". DB_TABLE_PREFIX ."newsletter_recipients where id = ". (int)$row['id'] ." limit 1;")->fetch()) {
							$recipient = new ent_newsletter_recipient($recipient['id']);

						} else if (!empty($row['email']) && $recipient = database::query("select id from ". DB_TABLE_PREFIX ."newsletter_recipients where email = '". database::input($row['email']) ."' limit 1;")->fetch()) {
							$recipient = new ent_newsletter_recipient($recipient['id']);
						}

						if (!empty($recipient->data['id'])) {

							if (empty($_POST['overwrite'])) {
								echo "Skip updating existing newsletter recipient on line $line" . PHP_EOL;
								continue 2;
							}

							echo 'Updating existing newsletter recipient '. fallback($row['email'], "on line $line") . PHP_EOL;
							$updated++;

						} else {

							if (empty($_POST['insert'])) {
								echo "Skip inserting new newsletter recipient on line $line" . PHP_EOL;
								continue 2;
							}

							echo 'Inserting new newsletter recipient: '. fallback($row['email'], "on line $line") . PHP_EOL;
							$inserted++;

							if (!empty($row['id'])) {
								database::query(
									"insert into ". DB_TABLE_PREFIX ."newsletter_recipients (id, date_created)
									values (". (int)$row['id'] .", '". date('Y-m-d H:i:s') ."');"
								);
								$recipient = new ent_newsletter_recipient($row['id']);
							} else {
								$recipient = new ent_newsletter_recipient();
							}
						}

					// Set newsletter recipient data
						$fields = [
							'email',
							'user_ip',
						];

						foreach ($fields as $field) {
							if (isset($row[$field])) $recipient->data[$field] = $row[$field];
						}

						$recipient->save();

						break;
				}
			}

			notices::add($updated ? 'success' : 'notice', strtr(language::translate('success_updated_n_existing_entries', 'Updated %n existing entries'), ['%n' => $updated]));
			notices::add($inserted ? 'success' : 'notice', strtr(language::translate('success_insert_n_new_entries', 'Inserted %n new entries'), ['%n' => $inserted]));

			header('Location: '. document::ilink());
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['export'])) {

		try {

			if (empty($_POST['type'])) throw new Exception(language::translate('error_must_select_type', 'You must select type'));

			switch ($_POST['type']) {

				case 'users':

					$csv = database::query(
						"select * from ". DB_TABLE_PREFIX ."users
						order by date_created asc;"
					)->export($result)->fetch_all();

					if (!$csv) {
						$csv = [array_fill_keys($result->fields(), '')];
					}

					break;

				case 'newsletter_recipients':

					$csv = database::query(
						"select * from ". DB_TABLE_PREFIX ."newsletter_recipients
						order by date_created asc;"
					)->export($result)->fetch_all();

					if (!$csv) {
						$csv = [array_fill_keys($result->fields(), '')];
					}

					break;

				default:
					throw new Exception(language::translate('error_invalid_type', 'Invalid type'));
					break;
			}

			ob_clean();

			if ($_POST['output'] == 'screen') {
				header('Content-Type: text/plain; charset='. $_POST['charset']);
			} else {
				header('Content-Type: application/csv; charset='. $_POST['charset']);
				header('Content-Disposition: attachment; filename=users.csv');
			}

			switch($_POST['eol']) {
				case 'Linux':
					echo functions::csv_encode($csv, $_POST['delimiter'], $_POST['enclosure'], $_POST['escapechar'], $_POST['charset'], "\r");
					break;
				case 'Mac':
					echo functions::csv_encode($csv, $_POST['delimiter'], $_POST['enclosure'], $_POST['escapechar'], $_POST['charset'], "\n");
					break;
				case 'Win':
				default:
					echo functions::csv_encode($csv, $_POST['delimiter'], $_POST['enclosure'], $_POST['escapechar'], $_POST['charset'], "\r\n");
					break;
			}

			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

?>

<div class="card card-app">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo language::translate('title_csv_import_export', 'CSV Import/Export'); ?>
		</div>
	</div>

	<div class="card-body">
		<div class="row">

			<div class="col-sm-6 col-lg-4">
				<?php echo functions::form_begin('import_form', 'post', '', true); ?>

					<fieldset>
						<legend><?php echo language::translate('title_import', 'Import'); ?></legend>

						<div class="form-group">
							<label><?php echo language::translate('title_type', 'Type'); ?></label>
							<div class="form-input">
								<?php echo functions::form_input_radio_button('type', ['users', language::translate('title_users', 'Users')], true); ?>
								<?php echo functions::form_input_radio_button('type', ['newsletter_recipients', language::translate('title_newsletter_recipients', 'Newsletter Recipients')], true); ?>
							</div>
						</div>

						<div class="form-group">
							<label><?php echo language::translate('title_csv_file', 'CSV File'); ?></label>
							<?php echo functions::form_input_file('file', 'accept=".csv, .dsv, .tab, .tsv"'); ?></td>
						</div>

						<div class="row">
							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_delimiter', 'Delimiter'); ?></label>
								<?php echo functions::form_select('delimiter', ['' => language::translate('title_auto', 'Auto') .' ('. language::translate('text_default', 'default') .')', ',' => ',',  ';' => ';', "\t" => 'TAB', '|' => '|'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_enclosure', 'Enclosure'); ?></label>
								<?php echo functions::form_select('enclosure', ['"' => '" ('. language::translate('text_default', 'default') .')'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_escape_character', 'Escape Character'); ?></label>
								<?php echo functions::form_select('escapechar', ['"' => '" ('. language::translate('text_default', 'default') .')', '\\' => '\\'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_charset', 'Charset'); ?></label>
								<?php echo functions::form_input_encoding('charset', !empty($_POST['charset']) ? true : 'UTF-8'); ?>
							</div>
						</div>

						<div class="form-group">
							<?php echo functions::form_checkbox('reset', ['1', language::translate('text_wipe_storage_clean_before_inserting_data', 'Wipe storage clean before inserting data')], true); ?>
							<?php echo functions::form_checkbox('insert', ['1', language::translate('text_insert_new_entries', 'Insert new entries')], true); ?>
							<?php echo functions::form_checkbox('overwrite', ['1', language::translate('text_overwrite_existing_entries', 'Overwrite existing entries')], true); ?>
						</div>

						<?php echo functions::form_button('import', language::translate('title_import', 'Import'), 'submit'); ?>
					</fieldset>

				<?php echo functions::form_end(); ?>
			</div>

			<div class="col-sm-6 col-lg-4">
				<?php echo functions::form_begin('export_form', 'post'); ?>

					<fieldset>
						<legend><?php echo language::translate('title_export', 'Export'); ?></legend>

						<div class="form-group">
							<label><?php echo language::translate('title_type', 'Type'); ?></label>
							<div class="form-input">
								<?php echo functions::form_input_radio_button('type', ['users', language::translate('title_users', 'Users')], true); ?>
								<?php echo functions::form_input_radio_button('type', ['newsletter_recipients', language::translate('title_newsletter_recipients', 'Newsletter Recipients')], true); ?>
							</div>
						</div>

						<div class="row">
							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_delimiter', 'Delimiter'); ?></label>
								<?php echo functions::form_select('delimiter', [',' => ', ('. language::translate('text_default', 'default') .')', ';' => ';', "\t" => 'TAB', '|' => '|'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_enclosure', 'Enclosure'); ?></label>
								<?php echo functions::form_select('enclosure', ['"' => '" ('. language::translate('text_default', 'default') .')'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_escape_character', 'Escape Character'); ?></label>
								<?php echo functions::form_select('escapechar', ['"' => '" ('. language::translate('text_default', 'default') .')', '\\' => '\\'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_charset', 'Charset'); ?></label>
								<?php echo functions::form_input_encoding('charset', !empty($_POST['charset']) ? true : 'UTF-8'); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_line_ending', 'Line Ending'); ?></label>
								<?php echo functions::form_select('eol', ['Win', 'Mac', 'Linux'], true); ?>
							</div>

							<div class="form-group col-sm-6">
								<label><?php echo language::translate('title_output', 'Output'); ?></label>
								<?php echo functions::form_select('output', ['file' => language::translate('title_file', 'File'), 'screen' => language::translate('title_screen', 'Screen')], true); ?>
							</div>
						</div>

						<?php echo functions::form_button('export', language::translate('title_export', 'Export'), 'submit'); ?>
					</fieldset>

				<?php echo functions::form_end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	$('form[name="import_form"] input[name="reset"]').click(function(){
		if ($(this).is(':checked') && !confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return false;
	});

	$('form[name="import_form"] input[name="insert"]').change(function(){
		$('form[name="import_form"] input[name="reset"]').prop('checked', false).prop('disabled', !$(this).is(':checked'));
	}).trigger('change');
</script>
