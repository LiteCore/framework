<?php

	if (!empty($_GET['addon_id'])) {
		$addon = new ent_addon($_GET['addon_id']);
	}
	else {
		$addon = new ent_addon();
	}

	if (!$_POST) {
		$_POST = $addon->data;
	}

	breadcrumbs::add(!empty($addon->data['id']) ? language::translate('title_edit_addon', 'Edit Add-on') : language::translate('title_create_new_addon', 'Create New Add-on'));

	if (isset($_POST['save'])) {

		try {

			if (empty($_POST['id'])) {
				throw new Exception(language::translate('error_must_enter_id', 'You must enter an ID'));
			}

			if (empty($_POST['name'])) {
				throw new Exception(language::translate('error_must_enter_name', 'You must enter a name'));
			}

			if (empty($_POST['install'])) $_POST['install'] = '';
			if (empty($_POST['uninstall'])) $_POST['uninstall'] = '';
			if (empty($_POST['upgrades'])) $_POST['upgrades'] = [];
			if (empty($_POST['settings'])) $_POST['settings'] = [];
			if (empty($_POST['aliases'])) $_POST['aliases'] = [];
			if (empty($_POST['files'])) $_POST['files'] = [];

			foreach ([
				'id',
				'status',
				'name',
				'description',
				'author',
				'version',
				'aliases',
				'settings',
				'install',
				'uninstall',
				'upgrades',
				'files',
			] as $field) {
				if (isset($_POST[$field])) {
					$addon->data[$field] = $_POST[$field];
				}
			}

			$addon->save();

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/addons'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['delete'])) {

		try {

			if (empty($addon->data['id'])) {
				 throw new Exception(language::translate('error_must_provide_addon', 'You must provide an add-on'));
			}

			$addon->delete();

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/addons'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['upload'])) {

		try {

			if (empty($addon->data['id'])) {
				throw new Exception(language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'));
			}

			if (empty($_FILES['files'])) {
				throw new Exception('No files uploaded');
			}

			if (empty($_POST['paths'])) {
				throw new Exception('No paths defined for uploaded files');
			}

			foreach (array_keys($_FILES['files']['tmp_name']) as $key) {
				$new_file = $addon->data['location'] . functions::file_strip_path($_POST['paths'][$key]);
				mkdir(dirname($new_file), 0777, true);
				move_uploaded_file($_FILES['files']['tmp_name'][$key], $new_file);
			}

			header('Location: ' . document::link());
			exit;

		} catch (Exception $e) {
			http_response_code(400);
			notices::add('errors', $e->getMessage());
		}
	}

	if (!empty($_POST['storage_action'])) {

		try {

			if (empty($addon->data['id'])) {
				throw new Exception(language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'));
			}

			if (empty($_POST['file'])) {
				throw new Exception(language::translate('error_must_specify_a_file', 'You must specify a file'));
			}

			$file = $addon->data['location'] . functions::file_strip_path($_POST['file']);

			if (!file_exists($file)) {
				throw new Exception(language::translate('error_file_does_not_exist', 'File does not exist'));
			}

			switch ($_POST['storage_action']) {

				case 'delete':

					functions::file_delete($file);
					break;

				case 'rename':

					if (empty($_POST['new_name'])) {
						throw new Exception(language::translate('error_must_provide_new_name', 'You must provide a new name'));
					}

					functions::file_move($file, $addon->data['location'] . functions::file_strip_path($_POST['new_name']));

					break;

				default:
					throw new Exception(language::translate('error_unknown_action', 'Unknown action'));
			}

			header('Location: ' . document::link());
			exit;
		}
		catch (Exception $e) {
			die($e->getMessage());
			http_response_code(400);
			notices::add('errors', $e->getMessage());
		}
	}

	$on_error_options = [
		'warning' => language::translate('title_warning', 'Warning'),
		'ignore' => language::translate('title_ignore', 'Ignore'),
		'cancel' => language::translate('title_cancel', 'Cancel'),
	];

	$method_options = [
		'replace' => language::translate('title_replace', 'Replace'),
		'before' => language::translate('title_before', 'Before'),
		'after' => language::translate('title_after', 'After'),
		'top' => language::translate('title_top', 'Top'),
		'bottom' => language::translate('title_bottom', 'Bottom'),
		'all' => language::translate('title_all', 'All'),
	];

	$type_options = [
		'inline' => language::translate('title_inline', 'Inline'),
		'multiline' => language::translate('title_multiline', 'Multiline'),
		'regex' => language::translate('title_regex', 'RegEx'),
	];

	// List of files.
	$files_datalist = [];

	$skip_list = [
		'#.*(?<!\.inc\.php)$#',
		'#^assets/#',
		'#^index.php$#',
		'#^includes/app_header.inc.php$#',
		'#^includes/nodes/nod_vmod.inc.php$#',
		'#^includes/wrappers/wrap_app.inc.php$#',
		'#^includes/wrappers/wrap_storage.inc.php$#',
		'#^install/#',
		'#^storage/#',
	];

	$scripts = functions::file_search(FS_DIR_APP . '**.php', GLOB_BRACE);

	foreach ($scripts as $script) {

		$relative_path = functions::file_relative_path($script);

		foreach ($skip_list as $pattern) {
			if (preg_match($pattern, $relative_path)) continue 2;
		}

		$files_datalist[] = $relative_path;
	}

	// Files tree.
	$draw_folder_contents = function($directory) use ($addon, &$draw_folder_contents) {
		$output = '';

		foreach (scandir($directory) as $file) {

			if (in_array($file, ['.', '..'])) continue;

			if ($directory == 'storage://addons/'.$addon->data['id'].'/' && $file == 'vmod.xml') continue;

			$relative_path = preg_replace('#^'. preg_quote('storage://addons/'.$addon->data['id'].'/', '#') .'#', '', $directory . $file);
			if (is_dir($directory.$file)) {
				$output .= '<li>'. functions::draw_fonticon('fa-folder fa-lg', 'style="color: #7ccdff;"') .' <span class="item" data-path="'. $relative_path .'">'. $file .'/</span>'. $draw_folder_contents($directory.$file.'/') .'</li>';
			}
			else {
				$output .= '<li>'. functions::draw_fonticon('fa-file-o') .' <span class="item" data-path="'. $relative_path .'">'. $file .'</span><li>';
			}
		}

		if (!$output) return;

		return '<ul class="flex flex-rows">'. PHP_EOL . $output . PHP_EOL . '</ul>';
	};

	functions::draw_lightbox();
?>

<style>
.file-browser {
	background: var(--default-background-color);
	line-height: 2;
}
.file-browser .list {
	height: 415px;
	overflow-y: auto;
}
.file-browser .item {
	cursor: default;
}
.file-browser .item:hover {
	background: rgba(255, 255, 255, 0.5);
}

.file-browser .upload-bar {
	display: flex;
	flex-direction: row;
}
.file-browser .upload-bar .btn {
	line-height: 1;
}

.context-menu {
	position: absolute;
	z-index: 10000;
	background: #fff;
	border-radius: var(--border-radius);
	overflow: hidden;
}
.context-menu .item {
	padding: .5em 1em;
	cursor: pointer;
	border-radius: inherit;
}
.context-menu .item:hover {
	background: #ccc;
}

.dropzone.in {
	position: relative;
}
.dropzone .drag-notice {
	display: none;
}
.dropzone.in .drag-notice {
	content: ' ';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	justify-content: center;
	text-align: center;
	flex-direction: column;
	background: rgba(0, 0, 0, 0.25);
	font-size: 2.5em;
	color: #fff;
}

.operation {
	background: #f8f8f8;
	padding: 1em;
	border-radius: 4px;
	margin-bottom: 2em;
}
html.dark-mode .operation {
	background: #232a3e;
}

.nav-tabs .fa-times-circle {
	color: #c00;
}
.nav-tabs .fa-plus {
	color: #0c0;
}

.script {
	position: relative;
}
.script .filename {
	position: absolute;
	display: inline-block;
	top: 0;
	right: 2em;
	padding: .5em 1em;
	border-radius: 0 0 4px 4px;
	background: #fff3;
	backdrop-filter: blur(2px);
	font-size: .8em;
	color: #fffc;
}

#settings .setting:not(:first-child) {
	border-top: 1px solid var(--default-border-color);
	padding-top: 2em;
	margin-top: 2em;
}

.sources .form-code {
	height: max-content;
	max-height: 100vh;
}

fieldset {
	border: none;
	padding: 0;
}

input[name*="[op_note]"],
input[name*="[find]"][name$="[content]"],
input[name*="[insert]"][name$="[content]"] {
	height: initial;
}

textarea[name*="[op_note]"],
textarea[name*="[find]"][name$="[content]"],
textarea[name*="[insert]"][name$="[content]"] {
	height: auto;
	transition: all 100ms linear;
}

.nav-tabs a.warning {
	color: red;
}

input.warning,
textarea.warning {
	box-shadow: 0 0 5px 3px rgba(255 0,0, 0.7);
}
</style>

<div class="card card-app">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo !empty($addon->data['id']) ? language::translate('title_edit_addon', 'Edit Add-on') : language::translate('title_create_new_addon', 'Create New Add-on'); ?>
		</div>
	</div>

	<?php echo functions::form_begin('addon_form', 'post', false, true); ?>

		<nav class="nav nav-tabs">
			<a class="nav-link active" href="#tab-general" data-toggle="tab"><?php echo language::translate('title_general', 'General'); ?></a>
			<a class="nav-link" href="#tab-modifications" data-toggle="tab"><?php echo language::translate('title_modifications', 'Modifications'); ?></a>
			<a class="nav-link" href="#tab-aliases" data-toggle="tab"><?php echo language::translate('title_aliases', 'Aliases'); ?></a>
			<a class="nav-link" href="#tab-settings" data-toggle="tab"><?php echo language::translate('title_settings', 'Settings'); ?></a>
			<a class="nav-link" href="#tab-install" data-toggle="tab"><?php echo language::translate('title_install_uninstall', 'Install/Uninstall'); ?></a>
		</nav>

		<div class="card-body">
			<div class="tab-content">
				<div id="tab-general" class="tab-pane active">

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label><?php echo language::translate('title_status', 'Status'); ?></label>
								<?php echo functions::form_toggle('status', 'e/d', true); ?>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_id', 'ID'); ?></label>
								<?php echo functions::form_input_text('id', true, 'required placeholder="my_awesome_addon" pattern="[0-9a-zA-Z_-]+"'); ?>
							</div>

							<div class="row">
								<div class="form-group col-md-8">
									<label><?php echo language::translate('title_name', 'Name'); ?></label>
									<?php echo functions::form_input_text('name', true, 'required placeholder="My Awesome Add-on"'); ?>
								</div>

								<div class="form-group col-md-4">
									<label><?php echo language::translate('title_version', 'Version'); ?></label>
									<?php echo functions::form_input_text('version', true, 'placeholder="'. date('Y-m-d') .'"'); ?>
								</div>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_description', 'Description'); ?></label>
								<?php echo functions::form_input_text('description', true); ?>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_author', 'Author'); ?></label>
								<?php echo functions::form_input_text('author', true); ?>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_storage_location', 'Storage Location'); ?></label>
								<div class="form-input" readyonly><?php echo !empty($addon->data['location']) ? $addon->data['location'] : '<em>'. language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage') .'</em>'; ?></div>
							</div>

							<?php if (!empty($addon->data['id'])) { ?>
							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_date_created', 'Date Created'); ?></label>
									<div><?php echo language::strftime('%e %b %Y %H:%M', strtotime($addon->data['date_created'])); ?></div>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_date_updated', 'Date Updated'); ?></label>
									<div><?php echo !empty($addon->data['date_updated']) ? language::strftime('%e %b %Y %H:%M', strtotime($addon->data['date_updated'])): '-'; ?></div>
								</div>
							</div>
							<?php } ?>
						</div>

						<div class="col-md-8">
							<div class="form-group col-md-6">
								<label><?php echo language::translate('title_file_storage', 'File Storage'); ?></label>
								<div class="file-browser form-input">
									<div class="dropzone">

										<?php if (!empty($addon->data['id'])) { ?>
										<ul class="list flex flex-rows">
											<li><strong><?php echo functions::draw_fonticon('fa-folder fa-lg', 'style="color: #7ccdff;"'); ?> [<?php echo language::translate('title_root', 'Root'); ?>]</strong>
												<?php echo $draw_folder_contents($addon->data['location']); ?>
											</li>
										</ul>

										<div class="drag-notice">
											<?php echo language::translate('text_drag_and_drop_files_and_folders_here', 'Drag and drop files and folders here'); ?>
										</div>
										<?php } else { ?>
										<div>
											<em><?php echo language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'); ?></em>
										</div>
										<?php } ?>
									</div>

									<?php if (!empty($addon->data['id'])) { ?>
									<div class="upload-bar">
										<?php echo functions::form_input_file('files[]', 'multiple'); ?>
										<?php echo functions::form_button('upload', ['true', language::translate('title_upload', 'Upload')]); ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>

				</div>

				<div id="tab-modifications" class="tab-pane">

					<h2><?php echo language::translate('title_modifications', 'Modifications'); ?></h2>

					<nav class="nav nav-tabs">
						<?php foreach (array_keys($addon->data['files']) as $f) { ?>
						<a class="nav-link" data-toggle="tab" href="#tab-<?php echo $f; ?>">
							<span class="file"><?php echo functions::escape_html($_POST['files'][$f]['name']); ?></span> <span class="remove" title="<?php language::translate('title_remove', 'Remove')?>"><?php echo functions::draw_fonticon('fa-times-circle'); ?></span>
						</a>
						<?php } ?>
						<a class="nav-link add" href="#"><?php echo functions::draw_fonticon('fa-plus'); ?></a>
					</nav>

					<div id="files" class="tab-content">

						<?php if (!empty($_POST['files'])) foreach (array_keys($_POST['files']) as $f) { ?>
						<div id="tab-<?php echo $f; ?>" data-tab-index="<?php echo $f; ?>" class="tab-pane">

							<div class="row">
								<div class="col-md-6">

									<h3><?php echo language::translate('title_file_to_modify', 'File To Modify'); ?></h3>

									<div class="form-group">
										<label><?php echo language::translate('title_file_pattern', 'File Pattern'); ?></label>
										<?php echo functions::form_input_text('files['.$f.'][name]', true, 'placeholder="path/to/file.php" list="scripts"'); ?>
									</div>

									<div class="sources"></div>
								</div>

								<div class="col-md-6">

									<h3><?php echo language::translate('title_operations', 'Operations'); ?></h3>

									<div class="operations">
										<?php $i=1; foreach (array_keys($_POST['files'][$f]['operations']) as $o) { ?>
										<fieldset class="operation">

											<div class="float-end">
												<a class="btn btn-default btn-sm move-up" href="#"><?php echo functions::draw_fonticon('move-up'); ?></a>
												<a class="btn btn-default btn-sm move-down" href="#"><?php echo functions::draw_fonticon('move-down'); ?></a>
												<a class="btn btn-default btn-sm remove" href="#"><?php echo functions::draw_fonticon('remove'); ?></a>
											</div>

											<h3><?php echo language::translate('title_operation', 'Operation'); ?> #<span class="number"><?php echo $i++;?></span></h3>

											<div class="row">
												<div class="form-group col-md-3">
													<label><?php echo language::translate('title_method', 'Method'); ?></label>
													<?php echo functions::form_select('files['.$f.'][operations]['.$o.'][method]', $method_options, true); ?>
												</div>

												<div class="form-group col-md-6">
													<label><?php echo language::translate('title_match_type', 'Match Type'); ?></label>
													<?php echo functions::form_toggle_buttons('files['.$f.'][operations]['.$o.'][type]', $type_options, (!isset($_POST['files'][$f]['operations'][$o]['type']) || $_POST['files'][$f]['operations'][$o]['type'] == '') ? 'multiline' : true); ?>
												</div>

												<div class="form-group col-md-3">
													<label><?php echo language::translate('title_on_error', 'On Error'); ?></label>
													<?php echo functions::form_select('files['.$f.'][operations]['.$o.'][onerror]', $on_error_options, true); ?>
												</div>
											</div>

											<div class="form-group">
												<h4><?php echo language::translate('title_find', 'Find'); ?></h4>
												<?php if (isset($_POST['files'][$f]['operations'][$o]['type']) && in_array($_POST['files'][$f]['operations'][$o]['type'], ['inline', 'regex'])) { ?>
												<?php echo functions::form_input_text('files['.$f.'][operations]['.$o.'][find][content]', true, 'class="form-code" required'); ?>
												<?php } else { ?>
												<?php echo functions::form_input_code('files['.$f.'][operations]['.$o.'][find][content]', true, 'required'); ?>
												<?php }?>
											</div>

											<div class="row" style="font-size: .8em;">
												<div class="form-group col-md-2">
													<label><?php echo language::translate('title_index', 'Index'); ?></label>
													<?php echo functions::form_input_text('files['.$f.'][operations]['.$o.'][find][index]', true, 'placeholder="1,3,.."'); ?>
												</div>

												<div class="form-group col-md-2">
													<label><?php echo language::translate('title_offset_before', 'Offset Before'); ?></label>
													<?php echo functions::form_input_text('files['.$f.'][operations]['.$o.'][find][offset-before]', true, 'placeholder="0"'); ?>
												</div>

												<div class="form-group col-md-2">
													<label><?php echo language::translate('title_offset_after', 'Offset After'); ?></label>
													<?php echo functions::form_input_text('files['.$f.'][operations]['.$o.'][find][offset-after]', true, 'placeholder="0"'); ?>
												</div>
											</div>

											<div class="form-group">
												<h4><?php echo language::translate('title_insert', 'Insert'); ?></h4>
												<?php if (isset($_POST['files'][$f]['operations'][$o]['type']) && in_array($_POST['files'][$f]['operations'][$o]['type'], ['inline', 'regex'])) { ?>
												<?php echo functions::form_input_text('files['.$f.'][operations]['.$o.'][insert][content]', true, 'class="form-code"'); ?>
												<?php } else { ?>
												<?php echo functions::form_input_code('files['.$f.'][operations]['.$o.'][insert][content]', true); ?>
												<?php }?>
											</div>
											<div class="form-group">
												<h4><?php echo language::translate('title_op_note', 'Notes'); ?></h4>
												<?php echo functions::form_textarea('files['.$f.'][operations]['.$o.'][op_note]', true); ?>
											</div>

										</fieldset>
										<?php } ?>

									</div>

									<div class="text-end">
										<a class="btn btn-default add" href="#">
											<?php echo functions::draw_fonticon('fa-plus', 'style="color: #0c0;"'); ?> <?php echo language::translate('title_add_operation', 'Add Operation'); ?>
										</a>
									</div>

								</div>
							</div>

						</div>
						<?php } ?>
					</div>

				</div>

				<div id="tab-aliases" class="tab-pane">

					<h2><?php echo language::translate('title_aliases', 'Aliases'); ?></h2>

					<div class="aliases">

						<?php if (!empty($_POST['aliases'])) foreach (array_keys($_POST['aliases']) as $key) { ?>
						<fieldset class="alias">
							<div class="row">
								<div class="form-group col-md-4">
									<label><?php echo language::translate('title_key', 'Key'); ?></label>
									<div class="input-group">
										<span class="input-group-text" style="font-family: monospace;">{alias:</span>
										<?php echo functions::form_input_text('aliases['.$key.'][key]', true, 'required'); ?>
										<span class="input-group-text" style="font-family: monospace;">}</span>
									</div>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_value', 'Value'); ?></label>
									<?php echo functions::form_input_text('aliases['.$key.'][value]'); ?>
								</div>

								<div class="col-md-2" style="align-self: center;">
									<?php echo functions::form_button('aliases[new_alias_index][move_up]', functions::draw_fonticon('move-up'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_up', 'Move Up')) .'"'); ?>
									<?php echo functions::form_button('aliases[new_alias_index][move_down]', functions::draw_fonticon('move-down'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_down', 'Move Down')) .'"'); ?>
									<?php echo functions::form_button('aliases[new_alias_index][remove]', functions::draw_fonticon('remove'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_remove', 'Remove')) .'"'); ?>
								</div>
							</div>
						</fieldset>
						<?php } ?>

					</div>

					<div class="form-group" style="margin-top: 2em;">
						<?php echo functions::form_button('add_alias', language::translate('title_add_alias', 'Add alias'), 'button', 'class="btn btn-default"', 'add'); ?>
					</div>

				</div>

				<div id="tab-settings" class="tab-pane">

					<h2><?php echo language::translate('title_settings', 'Settings'); ?></h2>

					<div id="settings" style="max-width: 1200px;">
						<?php if (!empty($_POST['settings'])) foreach (array_keys($_POST['settings']) as $key) { ?>
						<fieldset class="setting">
							<div class="row">
								<div class="form-group col-md-4">
									<label><?php echo language::translate('title_key', 'Key'); ?></label>
									<div class="input-group">
										<span class="input-group-text" style="font-family: monospace;">{setting:</span>
										<?php echo functions::form_input_text('settings['.$key.'][key]', true, 'required'); ?>
										<span class="input-group-text" style="font-family: monospace;">}</span>
									</div>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_title', 'Title'); ?></label>
									<?php echo functions::form_input_text('settings['.$key.'][title]', true, 'required'); ?>
								</div>

								<div class="col-md-2 text-center" style="align-self: center;">
									<?php echo functions::form_button('settings['.$key.'][move_up]', functions::draw_fonticon('move-up'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_up', 'Move Up')) .'"'); ?>
									<?php echo functions::form_button('settings['.$key.'][move_down]', functions::draw_fonticon('move-down'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_down', 'Move Down')) .'"'); ?>
									<?php echo functions::form_button('settings['.$key.'][remove]', functions::draw_fonticon('remove'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_remove', 'Remove')) .'"'); ?>
								</div>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_description', 'Description'); ?></label>
								<?php echo functions::form_input_text('settings['.$key.'][description]', true, 'required'); ?>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_function', 'Function'); ?></label>
									<?php echo functions::form_input_text('settings['.$key.'][function]', true, 'required placeholder="text()"'); ?>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo language::translate('title_default_value', 'Default Value'); ?></label>
									<?php echo functions::form_input_text('settings['.$key.'][default_value]'); ?>
								</div>
							</div>
						</fieldset>
						<?php } ?>
					</div>

					<div class="form-group" style="margin-top: 2em;">
						<?php echo functions::form_button('add_setting', language::translate('title_add_setting', 'Add Setting'), 'button', 'class="btn btn-default"', 'add'); ?>
					</div>

				</div>

				<div id="tab-install" class="tab-pane">

					<div class="row">
						<div class="col-md-6">
							<h2><?php echo language::translate('title_install', 'Install'); ?></h2>

							<div class="form-group">
								<label><?php echo language::translate('title_script', 'Script'); ?></label>
								<?php echo functions::form_input_code('install', true, 'style="height: 200px;"'); ?>
							</div>
						</div>

						<div class="col-md-6">
							<h2><?php echo language::translate('title_uninstall', 'Uninstall'); ?></h2>
							<div class="form-group">
								<label><?php echo language::translate('title_script', 'Script'); ?></label>
								<?php echo functions::form_input_code('uninstall', true, 'style="height: 200px;"'); ?>
							</div>
						</div>
					</div>

					<h2><?php echo language::translate('title_upgrade_patches', 'Upgrade Patches'); ?></h2>

					<div class="upgrades">
						<?php if (!empty($_POST['upgrades'])) foreach (array_keys($_POST['upgrades']) as $key) { ?>
						<fieldset class="upgrade">
							<div class="form-group" style="max-width: 250px;">
								<label><?php echo language::translate('title_version', 'Version'); ?></label>
								<?php echo functions::form_input_text('upgrades['.$key.'][version]', true); ?>
							</div>

							<div class="form-group">
								<label><?php echo language::translate('title_script', 'Script'); ?></label>
								<?php echo functions::form_input_code('upgrades['.$key.'][script]', true, 'style="height: 200px;"'); ?>
							</div>
						</fieldset>
						<?php } ?>
					</div>

					<div class="form-group" style="margin-top: 2em;">
						<?php echo functions::form_button('add_patch', language::translate('title_add_patch', 'Add Patch'), 'button', 'class="btn btn-default"', 'add'); ?>
					</div>

				</div>
			</div>

			<div class="card-action">
				<?php echo functions::form_button_predefined('save'); ?>
				<?php if (!empty($addon->data['id'])) echo functions::form_button('delete', language::translate('title_delete', 'Delete'), 'button', 'class="btn btn-danger"', 'delete'); ?>
				<?php echo functions::form_button_predefined('cancel'); ?>
			</div>
		</div>
	<?php echo functions::form_end(); ?>
</div>

<div id="modal-uninstall" style="display: none;">
	<?php echo functions::form_begin('uninstall_form', 'post'); ?>

		<h2><?php echo language::translate('title_uninstall_vmod', 'Uninstall vMod'); ?></h2>

		<p><label><?php echo functions::form_checkbox('cleanup', '1', ''); ?> <?php echo language::translate('text_remove_all_traces_of_the_vmod', 'Remove all traces of the vMod such as database tables, settings, etc.'); ?></label></p>

		<div>
			<?php echo functions::form_button('uninstall', language::translate('title_uninstall', 'Uninstall'), 'submit', 'class="btn btn-danger"'); ?>
			<?php echo functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'button'); ?>
		</div>

	<?php echo functions::form_end(); ?>
</div>

<div id="new-tab-pane-template" style="display: none;">
	<div id="tab-new_tab_index" data-tab-index="new_tab_index" class="tab-pane">

		<div class="row">
			<div class="col-md-6">

				<div class="form-group">
					<label><?php echo language::translate('title_file_pattern', 'File Pattern'); ?></label>
					<?php echo functions::form_input_text('files[new_tab_index][name]', true, 'placeholder="path/to/file.php" list="scripts"'); ?>
			 </div>

				<div class="sources"></div>
			</div>

			<div class="col-md-6">
				<div class="operations"></div>
				<div><a class="btn btn-default add" href="#"><?php echo functions::draw_fonticon('fa-plus', 'style="color: #0c0;"'); ?> <?php echo language::translate('title_add_operation', 'Add Operation'); ?></a></div>
			</div>
		</div>

	</div>
</div>

<div id="new-operation-template" style="display: none;">
	<fieldset class="operation">

		<div class="float-end">
			<a class="btn btn-default btn-sm move-up" href="#"><?php echo functions::draw_fonticon('move-up'); ?></a>
			<a class="btn btn-default btn-sm move-down" href="#"><?php echo functions::draw_fonticon('move-down'); ?></a>
			<a class="btn btn-default btn-sm remove" href="#"><?php echo functions::draw_fonticon('remove'); ?></a>
		</div>

		<h3><?php echo language::translate('title_operation', 'Operation'); ?> #<span class="number"></span></h3>

		<div class="row">
			<div class="form-group col-md-3">
				<label><?php echo language::translate('title_method', 'Method'); ?></label>
				<?php echo functions::form_select('files[current_tab_index][operations][new_operation_index][method]', $method_options, ''); ?>
			</div>

			<div class="form-group col-md-6">
				<label><?php echo language::translate('title_match_type', 'Match Type'); ?></label>
				<?php echo functions::form_toggle_buttons('files[current_tab_index][operations][new_operation_index][type]', $type_options, 'multiline'); ?>
			</div>

			<div class="form-group col-md-3">
				<label><?php echo language::translate('title_on_error', 'On Error'); ?></label>
				<?php echo functions::form_select('files[current_tab_index][operations][new_operation_index][onerror]', $on_error_options, ''); ?>
			</div>
		</div>

		<div class="form-group">
			<h4><?php echo language::translate('title_find', 'Find'); ?></h4>
			<?php echo functions::form_input_code('files[current_tab_index][operations][new_operation_index][find][content]', '', 'class="form-code" required'); ?>

		</div>

		<div class="row" style="font-size: .8em;">
			<div class="form-group col-md-2">
				<label><?php echo language::translate('title_index', 'Index'); ?></label>
				<?php echo functions::form_input_text('files[current_tab_index][operations][new_operation_index][find][index]', '', 'placeholder="1,3,.."'); ?>
			</div>

			<div class="form-group col-md-2">
				<label><?php echo language::translate('title_offset_before', 'Offset Before'); ?></label>
				<?php echo functions::form_input_text('files[current_tab_index][operations][new_operation_index][find][offset-before]', '', 'placeholder="0"'); ?>
			</div>

			<div class="form-group col-md-2">
				<label><?php echo language::translate('title_offset_after', 'Offset After'); ?></label>
				<?php echo functions::form_input_text('files[current_tab_index][operations][new_operation_index][find][offset-after]', '', 'placeholder="0"'); ?>
			</div>
		</div>

		<div class="form-group">
			<h4><?php echo language::translate('title_insert', 'Insert'); ?></h4>
			<?php echo functions::form_input_code('files[current_tab_index][operations][new_operation_index][insert][content]', '', 'class="form-code"'); ?>
		</div>
		<div class="form-group">
			<h4><?php echo language::translate('title_op_note', 'Notes'); ?></h4>
			<?php echo functions::form_textarea('files[current_tab_index][operations][new_operation_index][op_note]', true); ?>
		</div>

	</fieldset>
</div>

<datalist id="scripts">
	<?php foreach ($files_datalist as $option) { ?>
	<option><?php echo $option; ?></option>
	<?php } ?>
</datalist>

<script>
	// Tabs

	let new_tab_index = 1;
	while ($('.tab-pane[id="tab-'+new_tab_index+'"]').length) new_tab_index++;

	$('.nav-tabs').on('click', '[data-toggle="tab"]', function(e) {
		$($(this).attr('href')).find(':input[name$="[content]"]').trigger('input');
	});

	$('.nav-tabs .add').click(function(e){
		e.preventDefault();

		let tab = '<a class="nav-link" data-toggle="tab" href="#tab-'+ new_tab_index +'"><span class="file">new'+ new_tab_index +'</span> <span class="remove" title="<?php language::translate('title_remove', 'Remove')?>"><?php echo functions::draw_fonticon('fa-times-circle'); ?></span></a>'
			.replace(/new_tab_index/g, new_tab_index);

		let tab_pane = $('#new-tab-pane-template').html()
			.replace(/new_tab_index/g, new_tab_index++);

		$tab_pane = $(tab_pane);

		$(this).before(tab);
		$('#files').append($tab_pane);

		$(this).prev().click();
	});

	$('.nav-tabs').on('click', '.remove', function(e) {
		e.preventDefault();

		if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return false;

		let $tab = $(this).closest('.nav-link'),
			tab_pane = $(this).closest('.nav-link').attr('href');

		if ($tab.prev('[data-toggle="tab"]').length) {
			$tab.prev('[data-toggle="tab"]').trigger('click');
		}
		else if ($tab.next('[data-toggle="tab"]').length) {
			$tab.next('[data-toggle="tab"').trigger('click');
		}

		$(tab_pane).remove();
		$(this).closest('.nav-link').remove();
	});

	// Storage

	$('input[type="file"]').on({
			//change: function(){
			//  $(this).closest('form').submit();
			//},
		mouseenter: function(){
			$('.dropzone').addClass('in');
		},
		mouseleave: function(){
			$('.dropzone').removeClass('in');
		}
	});

	$('.dropzone').on({

		dragover: function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).addClass('in');
		},

		dragenter: function(e){
			$(this).addClass('in');
		},

		dragleave: function(e){
			let dropzone = this.getBoundingClientRect();
			if (e.originalEvent.x < dropzone.left || e.originalEvent.x > dropzone.left + dropzone.width
			|| e.originalEvent.y < dropzone.top || e.originalEvent.y > dropzone.top + dropzone.height) {
				$(this).removeClass('in');
			}
		},

		drop: function(e) {
			e.stopPropagation();
			e.preventDefault();

			let items = e.originalEvent.dataTransfer.items;

			getFilesDataTransferItems(items).then(files => {

				let form_data = new FormData();

				$.each(files, function(i, file) {
					form_data.append('files[]', file);
					form_data.append('paths[]', file.relpath);
				});

				form_data.append('upload', 'true');

				$.ajax({
					type: 'post',
					data: form_data,
					processData: false,
					contentType: false,
					dataType: 'html',
					success: function(response){
						$('.file-browser').html(
							$('.file-browser', response).html()
						);
					}
				});
			});

			$(this).removeClass('in');
		}
	});

	function getFilesDataTransferItems(dataTransferItems) {
		function traverseFileTreePromise(item, path = '', files) {
			return new Promise(resolve => {
				if (!item) return;
				if (item.isFile) {
					item.file(file => {
						file.relpath = (path || '') + file.name;
						files.push(file);
						resolve(file);
					});
				}
				else if (item.isDirectory) {
					let dirReader = item.createReader();
					dirReader.readEntries(entries => {
						let entriesPromises = [];
						for (let entr of entries)
							entriesPromises.push(
								traverseFileTreePromise(entr, (path || '') + item.name + '/', files)
							);
						resolve(Promise.all(entriesPromises));
					});
				}
			});
		}

		let files = [];
		return new Promise((resolve, reject) => {
			let entriesPromises = [];
			for (let it of dataTransferItems)
				entriesPromises.push(
					traverseFileTreePromise(it.webkitGetAsEntry(), null, files)
				);
			Promise.all(entriesPromises).then(entries => {
				resolve(files);
			});
		});
	}

	$('.file-browser').on('contextmenu', '.item', function(e) {
		e.preventDefault();

		$item = $(this);

		let $contextmenu = $([
			'<nav class="context-menu">',
			'  <ul class="flex flex-rows">',
			'    <li class="item rename"><?php echo functions::draw_fonticon('fa-pencil'); ?> <?php echo language::translate('title_rename', 'Rename'); ?></a>',
			'    <li class="item delete"><?php echo functions::draw_fonticon('fa-trash'); ?> <?php echo language::translate('title_delete', 'Delete'); ?></a>',
			'  </ul>',
			'</nav>',
		].join('\n'));


		$contextmenu.find('.rename').click(function(){

			let form_data = new FormData();
			form_data.append('storage_action', 'rename');
			form_data.append('file', $item.data('path'));

			let new_name = prompt('<?php echo language::translate('title_new_name', 'New Name'); ?>', $item.data('path'));

			if (!new_name) {
				$('.context-menu').remove();
				$('body').off('click');
				return;
			}

			form_data.append('new_name', new_name.trim());

			$.ajax({
				type: 'post',
				data: form_data,
				processData: false,
				contentType: false,
				dataType: 'html',
				success: function(response){
					$('.file-browser').html(
						$('.file-browser', response).html()
					);
					$('.context-menu').remove();
					$('body').off('click');
				}
			});
		});

		$contextmenu.find('.delete').click(function(){

			if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) {
				$('.context-menu').remove();
				$('body').off('click');
				return;
			}

			let form_data = new FormData();
			form_data.append('storage_action', 'delete');
			form_data.append('file', $item.data('path'));

			$.ajax({
				type: 'post',
				data: form_data,
				processData: false,
				contentType: false,
				dataType: 'html',
				success: function(){
					$item.closest('li').remove();
					$('.context-menu').remove();
					$('body').off('click');
				}
			});
		});

		$('body').on('click', function(e) {
			if (!$(event.target).closest('.context-menu').length) {
				$('.context-menu').remove();
				$('body').off('click');
			}
		});

		$contextmenu.css({
			left: e.pageX,
			top: e.pageY,
		}).appendTo('body');
	});

	// Modifications: Operations

	let reindex_operations = function($operations) {
		let index = 1;
		$operations.find('.operation').each(function(i, operation){
			$(operation).find('.number').text(index++);
		});
	}

	$('#files').on('change', ':input[name$="[type]"]', function(e) {
		e.preventDefault();
		let match_type = $(this).val();

		$(this).closest('.operation').find(':input[name$="[content]"]').each(function(i, field){
			switch (match_type) {

				case 'inline':
				case 'regex':
					var $newfield = $('<input class="form-code" name="'+ $(field).attr('name') +'" type="text">').val($(field).val());
					$(field).replaceWith($newfield);
					break;

				default:
					var $newfield = $('<textarea class="form-code" name="'+ $(field).attr('name') +'"></textarea>').val($(field).val());
					$(field).replaceWith($newfield);
					break;
			}
		});

		$(this).closest('.operation').find(':input[name$="[find][content]"]').trigger('input');
	});

	$('#files').on('change', ':input[name$="[method]"]', function(e) {
		e.preventDefault();

		let method = $(this).val();

		if ($.inArray(method, ['top', 'bottom']) != -1) {
			$(this).closest('.operation').find(':input[name*="[find]"]').prop('disabled', true);
		} else {
			$(this).closest('.operation').find(':input[name*="[find]"]').prop('disabled', false);
		}
	});

	$('#files :input[name$="[method]"]').trigger('change');

	// Auto expand textareas

	$('body').on('input', 'textarea.form-code', function() {
		$(this).css('height', '');
		$(this).css('height', Math.min(this.scrollHeight + 10, 250) + 'px');
	});

	$('textarea.form-code').trigger('input');

	$('.tab-content').on('input', ':input[name^="files"][name$="[name]"]', function(){

		let $tab_pane = $(this).closest('.tab-pane'),
		 tab_index = $(this).closest('.tab-pane').attr('id').replace(/^tab-/, ''),
		 tab_name = $tab_pane.find('input[name$="[name]"]').val();

		$('a[href="#tab-'+ tab_index +'"] .file').text(tab_name);

		let file_pattern = $(this).closest('.row').find(':input[name^="files"][name$="[name]"]').val(),
			url = '<?php echo document::ilink(__APP__.'/sources', ['pattern' => 'thepattern']); ?>'.replace(/thepattern/, file_pattern);

		$.get(url, function(result) {
			$tab_pane.find('.sources').html('');

			$.each(result, function(file, source_code){

				var $script = $(
					'<div class="script">' +
					'  <div class="form-code"></div>' +
					'  <div class="filename"></div>' +
					'</div>'
				);

				$script.find('.form-code').text(source_code);
				$script.find('.filename').text(file);
				$tab_pane.find('.sources').append($script);
			});

			$tab_pane.find(':input[name$="[find][content]"]').trigger('input');
		});
	});

	$(':input[name^="files"][name$="[name]"]').trigger('input');

	let new_operation_index = $(':input[name$="[find][content]"]').length || 0;

	$('#files').on('click', '.add', function(e) {
		e.preventDefault();

		let $operations = $(this).closest('.tab-pane').find('.operations'),
			tab_index = $(this).closest('.tab-pane').data('tab-index');

		 let output = $('#new-operation-template').html()
			 .replace(/current_tab_index/g, tab_index)
			 .replace(/new_operation_index/g, new_operation_index++);

		$operations.append(output);
		reindex_operations($operations);
	});

	$('#files').on('click', '.move-up, .move-down', function(e) {
		e.preventDefault();

		let $row = $(this).closest('.operation'),
			$operations = $(this).closest('.operations');

		if ($(this).is('.move-up') && $row.prevAll().length > 0) {
			$row.insertBefore($row.prev());
		}
		else if ($(this).is('.move-down') && $row.nextAll().length > 0) {
			$row.insertAfter($row.next());
		}

		reindex_operations($operations);
	});

	$('#files').on('click', '.remove', function(e) {
		e.preventDefault();

		let $operations = $(this).closest('.operations');

		if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return;

		$(this).closest('.operation').remove();
		reindex_operations($operations);

		$operations.find(':input[name$="[find][content]"]').trigger('input');
	});

	// Validate operation.
	$('#files').on('input', ':input[name*="[find]"]', function() {

		let $tab = $(this).closest('.tab-pane'),
			$operation = $(this).closest('.operation'),
			method = $operation.find(':input[name$="[method]"]').val(),
			find = $operation.find(':input[name$="[find][content]"]').val(),
			type = $operation.find(':input[name$="[type]"]:checked').val(),
			indexes = $operation.find(':input[name$="[index]"]').val().split(/\s*,\s*/).filter(Boolean),
			offset_before = $operation.find(':input[name$="[offset-before]"]').val(),
			offset_after = $operation.find(':input[name$="[offset-after]"]').val()
			onerror = $operation.find(':input[name$="[onerror]"]').val(),
			op_note = $operation.find(':input[name$="[op_note]"]').val(),
			regex_flags = 's';

		try {

			switch (method) {

				case 'top':

					find = '^';
					break;

				case 'bottom':

					find = '$';
					break;

				case 'all':

					find = '^.*$';
					break;

				case 'before':
				case 'after':
				case 'replace':

					// Trim
					find = find.trim();

					// Cook the regex pattern
					if (type == 'regex') {

						find_operators = 'g'+find.substr(find.lastIndexOf(find.substr(0, 1))+1);
						find = find.substr(1, find.lastIndexOf(find.substr(0, 1))-1);
					}
					else if (type == 'inline') {

						find = find.replace(/[\-\[\]{}()*+?.,\\\^$|#]/g, "\\$&");
					}
					else {

						// Whitespace
						find = find.split(/\r\n?|\n/);

						for (let i=0; i < find.length; i++) {
							if (find[i] = find[i].trim()) {
								find[i] = '[ \t]*'+ find[i].replace(/[\-\[\]{}()*+?.,\\\^$|#]/g, "\\$&") +'[ \t]*(?:\r\n?|\n|$)';
							}
							else if (i != (find.length -1)) {
								find[i] = '[ \t]*(?:\r\n?|\n)';
							}
						}
						find = find.join('');

						// Offset
						if (offset_before != '') {
							find = '(?:.*?(?:\r\n?|\n)){'+ offset_before +'}'+ find;
						}

						if (offset_after != '') {
							find = find + '(?:.*?(?:\r\n?|\n|$)){0,'+ offset_after +'}';
						}
					}

					regex_flags = 'gm';

				break;

				default:
					throw new Error('Unknown error');
			}

			$.each($tab.find('.script'), function(){

				let regex = new RegExp(find, regex_flags),
					source = $(this).find('.form-code').text(),
					matches = (source.match(regex) || []).length;

				if (!matches) {
					throw new Error('Failed matching content');
				}

				if (indexes && Math.max(indexes) > (matches+1)) {
					throw new Error('Failed matching an index');
				}
			});

			$operation.find(':input[name$="[find][content]"]').removeAttr('title').removeClass('warning');

		} catch (err) {
			if (onerror != 'ignore') {
				$operation.find(':input[name$="[find][content]"]').attr('title', err.message).addClass('warning');
			}
		}

		if ($tab.find(':input.warning').length) {
			$('.nav-link[href="#'+ $tab.attr('id') +'"]').addClass('warning');
		}
		else {
			$('.nav-link[href="#'+ $tab.attr('id') +'"]').removeClass('warning');
		}
	});

	// Aliases

	let new_alias_index = 0;
	while ($(':input[name^="aliases['+new_alias_index+']"]').length) new_alias_index++;

	$('button[name="add_alias"]').click(function(){

		let output = [
			'<fieldset class="alias">',
			'  <div class="row">',
			'    <div class="form-group col-md-4">',
			'      <label><?php echo language::translate('title_key', 'Key'); ?></label>',
			'      <div class="input-group">',
			'        <span class="input-group-text" style="font-family: monospace;">{alias:</span>',
			'        <?php echo functions::form_input_text('aliases[new_alias_index][key]', '', 'required'); ?>',
			'        <span class="input-group-text" style="font-family: monospace;">}</span>',
			'      </div>',
			'    </div>',
			'',
			'    <div class="form-group col-md-6">',
			'      <label><?php echo functions::escape_js(language::translate('title_value', 'Value')); ?></label>',
			'      <?php echo functions::escape_js(functions::form_input_text('aliases[new_alias_index][value]', '', 'required')); ?>',
			'    </div>',
			'',
			'    <div class="col-md-2" style="align-self: center;">',
			'     <?php echo functions::form_button('aliases[new_alias_index][move_up]', functions::draw_fonticon('move-up'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_up', 'Move Up')) .'"'); ?>',
			'     <?php echo functions::form_button('aliases[new_alias_index][move_down]', functions::draw_fonticon('move-down'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_down', 'Move Down')) .'"'); ?>',
			'     <?php echo functions::form_button('aliases[new_alias_index][remove]', functions::draw_fonticon('remove'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_remove', 'Remove')) .'"'); ?>',
			'    </div>',
			'  </div>',
			'</fieldset>'
		].join('\n')
		.replace(/new_alias_index/g, 'new_' + new_alias_index++);

		$('.aliases').append(output);
	});

	$('#aliases').on('click', 'button[name$="[move_up]"], button[name$="[move_down]"]', function(e) {
		e.preventDefault();

		let $row = $(this).closest('.alias');

		if ($(this).is('button[name$="[move_up]"]') && $row.prevAll().length > 0) {
			$row.insertBefore($row.prev());
		}
		else if ($(this).is('button[name$="[move_down]"]') && $row.nextAll().length > 0) {
			$row.insertAfter($row.next());
		}
	});

	$('#aliases').on('click', 'button[name$="[remove]"]', function(e) {
		e.preventDefault();

		if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return;
		$(this).closest('.alias').remove();
	});

	// Settings
	let new_setting_index = 0;
	while ($(':input[name^="settings['+new_setting_index+']"]').length) new_setting_index++;

	$('button[name="add_setting"]').click(function(){

		let output = [
			'<fieldset class="setting">',
			'  <div class="row">',
			'    <div class="form-group col-md-4">',
			'      <label><?php echo language::translate('title_key', 'Key'); ?></label>',
			'      <div class="input-group">',
			'        <span class="input-group-text" style="font-family: monospace;">{setting:</span>',
			'        <?php echo functions::form_input_text('settings[new_setting_index][key]', '', 'required'); ?>',
			'        <span class="input-group-text" style="font-family: monospace;">}</span>',
			'      </div>',
			'    </div>',
			'',
			'    <div class="form-group col-md-6">',
			'      <label><?php echo functions::escape_js(language::translate('title_title', 'Title')); ?></label>',
			'      <?php echo functions::escape_js(functions::form_input_text('settings[new_setting_index][title]', '', 'required')); ?>',
			'    </div>',
			'',
			'    <div class="col-md-2 text-center" style="align-self: center;">',
			'     <?php echo functions::form_button('settings[new_setting_index][move_up]', functions::draw_fonticon('move-up'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_up', 'Move Up')) .'"'); ?>',
			'     <?php echo functions::form_button('settings[new_setting_index][move_down]', functions::draw_fonticon('move-down'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_move_down', 'Move Down')) .'"'); ?>',
			'     <?php echo functions::form_button('settings[new_setting_index][remove]', functions::draw_fonticon('remove'), 'button', 'class="btn btn-default btn-sm" title="'. functions::escape_attr(language::translate('title_remove', 'Remove')) .'"'); ?>',
			'    </div>',
			'  </div>',
			'',
			'  <div class="form-group">',
			'    <label><?php echo functions::escape_js(language::translate('title_description', 'Description')); ?></label>',
			'    <?php echo functions::escape_js(functions::form_input_text('settings[new_setting_index][description]', '', 'required')); ?>',
			'  </div>',
			'',
			'  <div class="row">',
			'    <div class="form-group col-md-6">',
			'      <label><?php echo functions::escape_js(language::translate('title_function', 'Function')); ?></label>',
			'      <?php echo functions::escape_js(functions::form_input_text('settings[new_setting_index][function]', '', 'required')); ?>',
			'    </div>',
			'',
			'    <div class="form-group col-md-6">',
			'      <label><?php echo functions::escape_js(language::translate('title_default_value', 'Default Value')); ?></label>',
			'      <?php echo functions::escape_js(functions::form_input_text('settings[new_setting_index][default_value]', '')); ?>',
			'    </div>',
			'  </div>',
			'</fieldset>'
		].join('\n')
		.replace(/new_setting_index/g, 'new_' + new_setting_index++);

		$('#settings').append(output);
	});

	$('#settings').on('click', 'button[name$="[move_up]"], button[name$="[move_down]"]', function(e) {
		e.preventDefault();

		let $row = $(this).closest('.setting');

		if ($(this).is('button[name$="[move_up]"]') && $row.prevAll().length > 0) {
			$row.insertBefore($row.prev());
		}
		else if ($(this).is('button[name$="[move_down]"]') && $row.nextAll().length > 0) {
			$row.insertAfter($row.next());
		}
	});

	$('#settings').on('click', 'button[name$="[remove]"]', function(e) {
		e.preventDefault();

		if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return;
		$(this).closest('.setting').remove();
	});

	// Upgrade Patches
	let new_upgrade_patch_index = 0;
	while ($(':input[name^="upgrades['+new_upgrade_patch_index+']"]').length) new_upgrade_patch_index++;

	$('button[name="add_patch"]').click(function(){

		let output = [
			'<fieldset class="upgrade">',
			'  <div class="form-group" style="max-width: 250px;">',
			'    <label><?php echo functions::escape_js(language::translate('title_version', 'Version')); ?></label>',
			'    <?php echo functions::escape_js(functions::form_input_text('upgrades[new_upgrade_patch_index][version]', '')); ?>',
			'  </div>',
			'',
			'  <div class="form-group">',
			'    <label><?php echo functions::escape_js(language::translate('title_script', 'Script')); ?></label>',
			'    <?php echo functions::escape_js(functions::form_input_code('upgrades[new_upgrade_patch_index][script]', '', 'style="height: 200px;"')); ?>',
			'  </div>',
			'</fieldset>'
		].join('\n')
		.replace(/new_upgrade_patch_index/g, 'new_' + new_upgrade_patch_index);

		$('.upgrades').append(output);
	});

	$('.card-action button[name="delete"]').click(function(e){
		e.preventDefault();
		$.featherlight('#modal-uninstall');
	});

	$('body').on('click', '.featherlight button[name="cancel"]', function(e){
		$.featherlight.close();
	});
</script>
