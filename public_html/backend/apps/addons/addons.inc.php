<?php

	if (isset($_POST['enable']) || isset($_POST['disable'])) {

		try {
			if (empty($_POST['addons'])) throw new Exception(t('error_must_select_addons', 'You must select add-ons'));

			foreach ($_POST['addons'] as $addon) {

				if ((!$addon = basename($addon)) || (!is_dir('storage://addons/'. $addon .'/') && !is_dir('storage://addons/'. $addon .'.disabled/'))) {
					throw new Exception(t('error_invalid_addon_folder', 'Invalid add-on folder') .' ('. $addon .')');
				}

				if (!empty($_POST['enable'])) {
					if (!is_dir('storage://addons/'. $addon .'.disabled/')) continue;
					rename('storage://addons/'. $addon .'.disabled/', 'storage://addons/'. $addon .'/');
				} else {
					if (!is_dir('storage://addons/'. $addon .'/')) continue;
					rename('storage://addons/'. $addon .'/', 'storage://addons/'. $addon .'.disabled/');
				}
			}

			notices::add('success', t('success_changes_saved', 'Changes saved'));
			reload();
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['delete'])) {

		try {
			if (empty($_POST['addons'])) {
				throw new Exception(t('error_must_select_addons', 'You must select add-ons'));
			}

			foreach ($_POST['addons'] as $addon) {

				if (!$addon = basename($addon) || !is_dir($addon)) {
					throw new Exception(t('error_invalid_addon_folder', 'Invalid add-on folder'));
				}

				functions::file_delete('storage://addons/' . basename($addon) .'/');
			}

			notices::add('success', t('success_changes_saved', 'Changes saved'));
			reload();
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['upload'])) {

		try {
			if (!isset($_FILES['addon']['tmp_name']) || !is_uploaded_file($_FILES['addon']['tmp_name'])) {
				throw new Exception(t('error_must_select_file_to_upload', 'You must select a file to upload'));
			}

			if (!$id = preg_replace('#^(.*?)(-[0-9\.]+)?(\.vmod)?\.zip$#', '$1', $_FILES['vmod']['name'])) {
				throw new Exception(t('error_could_not_determine_archive_name', 'Could not determine archive name'));
			}

			$folder = 'storage://addons/'.$id.'/';

			$zip = new ZipArchive();
			if ($zip->open($_FILES['vmod']['tmp_name'], ZipArchive::RDONLY) !== true) { // ZipArchive::CREATE throws an error with temp files in PHP 8.
				throw new Exception('Failed opening ZIP archive');
			}

			if (!$addon = $zip->getFromName('vmod.xml')) {
				throw new Exception('Could not find vmod.xml');
			}

			$dom = new DOMDocument('1.0', 'UTF-8');

			if (!@$dom->loadXML($addon) || !$dom->getElementsByTagName('vmod')) {
				throw new Exception(t('error_xml_file_is_not_valid_vmod', 'XML file is not a valid vMod file'));
			}

			if (is_dir($folder)) {
				functions::file_delete($folder);
			}

			if (!$zip->extractTo(functions::file_realpath($folder))) {
				throw new Exception('Failed extracting contents from ZIP archive');
			}

			$zip->close();

			notices::add('success', t('success_changes_saved', 'Changes saved'));
			reload();
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	// Installed add-ons
	$installed_addons = preg_split('#[\r\n]+#', file_get_contents('storage://addons/.installed'), -1, PREG_SPLIT_NO_EMPTY);

	// Table Rows
	$addons = [];

	foreach (functions::file_search('storage://addons/*/') as $folder) {

		if (preg_match('#/.cache/#', $folder)) continue;

		$folder_name = preg_replace('#^storage://addons/#', '', $folder);
		$addon = new ent_addon($folder_name);

		$current_addon = [
			'id' => $addon->data['id'],
			'folder' => $addon->data['folder'],
			'status' => $addon->data['status'],
			'installed' => $addon->data['installed'],
			'location' => $addon->data['location'],
			'name' => $addon->data['name'],
			'version' => $addon->data['version'],
			'author' => $addon->data['author'],
			'configurable' => !empty($this->data['settings']),
			'errors' => null,
		];

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->load($addon->data['location'] . 'vmod.xml');

		$vmod = vmod::parse_xml($dom, $addon->data['location'] . 'vmod.xml');

		// Check for errors
		try {

			foreach (array_keys($vmod['files']) as $key) {

				foreach (glob(FS_DIR_APP . $vmod['files'][$key]['name'], GLOB_BRACE) as $file) {

					$buffer = file_get_contents($file);

					foreach ($vmod['files'][$key]['operations'] as $i => $operation) {

						$found = preg_match_all($operation['find']['pattern'], $buffer, $matches, PREG_OFFSET_CAPTURE);

						if (!$found) {
							switch ($operation['onerror']) {
								case 'ignore':
									continue 2;
								case 'abort':
								case 'warning':
								default:
									throw new Exception('Operation #'. ($i+1) .' failed in '. preg_replace('#^'. preg_quote(FS_DIR_APP, '#') .'#', '', $file), E_USER_WARNING);
									continue 2;
							}
						}

						if (!empty($operation['find']['indexes'])) {
							rsort($operation['find']['indexes']);

							foreach ($operation['find']['indexes'] as $index) {
								$index = $index - 1; // [0] is the 1st in computer language

								if ($found > $index) {
									$buffer = substr_replace($buffer, preg_replace($operation['find']['pattern'], $operation['insert'], $matches[0][$index][0]), $matches[0][$index][1], strlen($matches[0][$index][0]));
								}
							}

						} else {
							$buffer = preg_replace($operation['find']['pattern'], $operation['insert'], $buffer, -1, $count);

							if (!$count && $operation['onerror'] != 'skip') {
								throw new Exception("Failed to perform insert");
								continue;
							}
						}
					}
				}
			}

		} catch (Exception $e) {
			$current_addon['errors'] = $e->getMessage();
		}

		$addons[] = $current_addon;
	}

	// Number of Rows
	$num_rows = count($addons);

?>

<div class="card">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo t('title_installed_addons', 'Installed Add-ons'); ?>
		</div>
	</div>

	<div class="card-action">
		<?php echo functions::form_button_link(document::ilink(__APP__.'/edit_addon'), t('title_create_new_addon', 'Create New Add-on'), '', 'add'); ?>
	</div>

	<?php echo functions::form_begin('addon_form', 'post', '', true); ?>

		<table class="table data-table">
			<thead>
				<tr>
					<th><?php echo functions::draw_fonticon('icon-square-check', 'data-toggle="checkbox-toggle"'); ?></th>
					<th></th>
					<th class="main"><?php echo t('title_name', 'Name'); ?> / <?php echo t('title_version', 'Version'); ?></th>
					<th><?php echo t('title_vmod_health', 'vMod Health'); ?></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($addons as $addon) { ?>
				<tr class="<?php echo $addon['status'] ? null : 'semi-transparent'; ?>">
					<td><?php echo functions::form_checkbox('addons[]', $addon['id']); ?></td>
					<td><?php echo functions::draw_fonticon($addon['status'] ? 'on' : 'off'); ?></td>
					<td><a class="link" href="<?php echo document::href_ilink(__APP__.'/edit_addon', ['addon_id' => $addon['id']]); ?>"><?php echo $addon['name']; ?> / <?php echo $addon['version']; ?></a></td>
					<td class="text-center">
						<?php if (empty($addon['errors'])) { ?>
						<span style="color: #8c4"><?php echo functions::draw_fonticon('ok'); ?> <?php echo t('title_ok', 'OK'); ?></span>
						<?php } else { ?>
						<span style="color: #c00"><?php echo functions::draw_fonticon('warning'); ?> <?php echo t('title_fail', 'Fail'); ?></span>
						<?php } ?>
					</td>
					<td class="text-center"><a class="btn btn-default btn-sm" href="<?php echo document::href_ilink(__APP__.'/download', ['addon_id' => $addon['id']]); ?>" title="<?php echo t('title_download', 'Download'); ?>"><?php echo functions::draw_fonticon('icon-download'); ?> <?php echo t('title_download', 'Download'); ?></a></td>
					<td></td>
					<td><a class="btn btn-default btn-sm" href="<?php echo document::href_ilink(__APP__.'/edit_addon', ['addon_id' => $addon['id']]); ?>" title="<?php echo t('title_edit', 'Edit'); ?>"><?php echo functions::draw_fonticon('edit'); ?></a></td>
				</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="7"><?php echo t('title_addons', 'Add-ons'); ?>: <?php echo language::number_format($num_rows); ?></td>
				</tr>
			</tfoot>
		</table>

		<div class="card-body">
			<div class="grid">
				<div class="col-md-6">
					<fieldset id="actions">
						<legend><?php echo t('text_with_selected', 'With selected'); ?>:</legend>

						<ul class="flex flex-columns">
							<li>
								<div class="btn-group">
									<?php echo functions::form_button('enable', t('title_enable', 'Enable'), 'submit', '', 'on'); ?>
									<?php echo functions::form_button('disable', t('title_disable', 'Disable'), 'submit', '', 'off'); ?>
								</div>
							</li>
							<li>
								<?php echo functions::form_button_predefined('delete'); ?>
							</li>
						</ul>
					</fieldset>
			</div>

			<div class="col-md-6">
				<fieldset>
					<legend><?php echo t('title_upload_new_addon', 'Upload a New Add-on'); ?>:</legend>

					<div class="input-group">
						<?php echo functions::form_input_file('addon', 'accept="application/zip,application/xml"'); ?>
						<?php echo functions::form_button('upload', t('title_upload', 'Upload'), 'submit'); ?>
					</div>
				</fieldset>
			</div>
		</div>

	<?php echo functions::form_end(); ?>
</div>

<script>
	$('.data-table :checkbox').change(function() {
		$('#actions').prop('disabled', !$('.data-table :checked').length);
	}).first().trigger('change');
</script>
