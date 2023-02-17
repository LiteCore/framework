<?php

  if (!empty($_GET['addon_id'])) {
    $addon = new ent_addon($_GET['addon_id']);
  } else {
    $addon = new ent_addon();
  }

  if (!$_POST) {
    $_POST = $addon->data;
  }

  breadcrumbs::add(!empty($addon->data['id']) ? language::translate('title_edit_addon', 'Edit Add-on') : language::translate('title_create_new_addon', 'Create New Add-on'));

  if (isset($_POST['save'])) {

    try {

      if (empty($_POST['id'])) throw new Exception(language::translate('error_must_enter_id', 'You must enter an ID'));
      if (empty($_POST['name'])) throw new Exception(language::translate('error_must_enter_name', 'You must enter a name'));

      if (empty($_POST['install'])) $_POST['install'] = '';
      if (empty($_POST['uninstall'])) $_POST['uninstall'] = '';
      if (empty($_POST['upgrades'])) $_POST['upgrades'] = [];
      if (empty($_POST['settings'])) $_POST['settings'] = [];
      if (empty($_POST['aliases'])) $_POST['aliases'] = [];
      if (empty($_POST['files'])) $_POST['files'] = [];

      $fields = [
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
      ];

      foreach ($fields as $field) {
        if (isset($_POST[$field])) $addon->data[$field] = $_POST[$field];
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

      if (empty($addon->data['id'])) throw new Exception(language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'));
      if (empty($_FILES['files'])) throw new Exception('No files uploaded');
      if (empty($_POST['paths'])) throw new Exception('No paths defined for uploaded files');

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

      if (empty($addon->data['id'])) throw new Exception(language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'));
      if (empty($_POST['file'])) throw new Exception(language::translate('error_must_specify_a_file', 'You must specify a file'));

      $file = $addon->data['location'] . functions::file_strip_path($_POST['file']);

      if (!file_exists($file)) throw new Exception(language::translate('error_file_does_not_exist', 'File does not exist'));

      switch ($_POST['storage_action']) {

        case 'delete':

          functions::file_delete($file);
          break;

        case 'rename':

          if (empty($_POST['new_name'])) throw new Exception(language::translate('error_must_provide_new_name', 'You must provide a new name'));
          functions::file_move($file, $addon->data['location'] . functions::file_strip_path($_POST['new_name']));

          break;

        default:
          throw new Exception(language::translate('error_unknown_action', 'Unknown action'));
      }

      header('Location: ' . document::link());
      exit;

    } catch (Exception $e) {
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
  ];

  $type_options = [
    'inline' => language::translate('title_inline', 'Inline'),
    'multiline' => language::translate('title_multiline', 'Multiline'),
    'regex' => language::translate('title_regex', 'RegEx'),
  ];

// List of files
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

// Files tree

  $draw_folder_contents = function($directory) use ($addon, &$draw_folder_contents) {
    $output = '';

    foreach (scandir($directory) as $file) {
      if (in_array($file, ['.', '..'])) continue;
      if ($directory == 'storage://addons/'.$addon->data['id'].'/' && $file == 'vmod.xml') continue;
      $relative_path = preg_replace('#^'. preg_quote('storage://addons/'.$addon->data['id'].'/', '#') .'#', '', $directory . $file);
      if (is_dir($directory.$file)) {
        $output .= '<li>'. functions::draw_fonticon('fa-folder fa-lg', 'style="color: #7ccdff;"') .' <span class="item" data-path="'. $relative_path .'">'. $file .'/</span>'. $draw_folder_contents($directory.$file.'/') .'</li>';

      } else {
        $output .= '<li>'. functions::draw_fonticon('fa-file-o') .' <span class="item" data-path="'. $relative_path .'">'. $file .'</span><li>';
      }
    }

    if (!$output) return;

    return '<ul class="list-unstyled">'. PHP_EOL . $output . PHP_EOL . '</ul>';
  };
  
  functions::draw_lightbox();
?>

<style>
.file-browser {
  height: 415px;
  overflow-y: auto;
  background: var(--default-background-color);
  line-height: 2;
}
.file-browser .item {
  cursor: default;
}
.file-browser .item:hover {
  background: rgba(255, 255, 255, 0.5);
}

.file-browser .upload-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 10000;
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
  display: flex;
  width: 100%;
  height: 100%;
  justify-content: center;
  text-align: center;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.25);
  font-size: 2.5em;
  color: #fff;
}

.operation {
  background: #f2f3f6;
  padding: 1em;
  border-radius: 4px;
  margin-bottom: 2em;
  border: 1px solid var(--default-border-color);
}
html.dark-mode .operation {
  background: #21293a;
}

.fa-times-circle {
  color: #c00;
}
.fa-plus {
  color: #0c0;
}
.operations {
  position: sticky;
  top: 0;
}

.script {
  position: relative;
}
.script .script-filename {
  position: absolute;
  display: inline-block;
  top: 0;
  right: 2em;
  padding: .5em 1em;
  border-radius: 0 0 4px 4px;
  background: #fff3;
  color: #fffc
}

.sources .form-code {
  height: max-content;
  max-height: 100vh;
}

fieldset {
  border: none;
  padding: 0;
}

input[name*="[find]"][name$="[content]"],
input[name*="[insert]"][name$="[content]"] {
  height: initial;
}

textarea[name*="[find]"][name$="[content]"],
textarea[name*="[insert]"][name$="[content]"] {
  height: auto;
  transition: all 100ms linear;
}
textarea[name*="[find]"][name$="[content]"] {
  min-height: 50px;
  max-height: 50px;
}
textarea[name*="[find]"][name$="[content]"]:focus {
  max-height: 250px;
}

textarea[name*="[insert]"][name$="[content]"] {
  min-height: 100px;
  max-height: 100px;
}
textarea[name*="[insert]"][name$="[content]"]:focus {
  max-height: 250px;
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
                <?php echo functions::form_toggle('status', true, 'e/d'); ?>
              </div>

              <div class="form-group">
                <label><?php echo language::translate('title_id', 'ID'); ?></label>
                <?php echo functions::form_text_field('id', true, 'required placeholder="my_awesome_addon" pattern="^[0-9a-zA-Z_-]+$"'); ?>
              </div>

              <div class="row">
                <div class="form-group col-md-8">
                  <label><?php echo language::translate('title_name', 'Name'); ?></label>
                  <?php echo functions::form_text_field('name', true, 'required placeholder="My Awesome Add-on"'); ?>
                </div>

                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_version', 'Version'); ?></label>
                  <?php echo functions::form_text_field('version', true, 'placeholder="'. date('Y-m-d') .'"'); ?>
                </div>
              </div>

              <div class="form-group">
                <label><?php echo language::translate('title_description', 'Description'); ?></label>
                <?php echo functions::form_text_field('description', true, ''); ?>
              </div>

              <div class="form-group">
                <label><?php echo language::translate('title_author', 'Author'); ?></label>
                <?php echo functions::form_text_field('author', true, ''); ?>
              </div>

              <div class="form-group">
                <label><?php echo language::translate('title_storage_location', 'Storage Location'); ?></label>
                <div class="form-input" readyonly><?php echo !empty($addon->data['location']) ? $addon->data['location'] : language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'); ?></div>
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
                <div class="file-browser dropzone form-input">

                  <?php if (!empty($addon->data['id'])) { ?>
                  <ul class="list-unstyled">
                    <li><strong><?php echo functions::draw_fonticon('fa-folder fa-lg', 'style="color: #7ccdff;"'); ?> [<?php echo language::translate('title_root', 'Root'); ?>]</strong>
                      <?php echo $draw_folder_contents($addon->data['location']); ?>
                    </li>
                  </ul>

                  <?php } else { ?>
                  <div>
                    <em><?php echo language::translate('text_save_addon_to_establish_file_storage', 'Save the add-on to establish a file storage'); ?></em>
                  </div>
                  <?php } ?>

                  <div class="upload-bar">
                    <?php echo functions::form_file_field('files[]', 'multiple'); ?>
                    <?php echo functions::form_button('upload', ['true', language::translate('title_upload', 'Upload')]); ?>
                  </div>

                  <div class="drag-notice">
                    <?php echo language::translate('text_drag_and_drop_files_and_folders_here', 'Drag and drop files and folders here'); ?>
                    </div>
                  </div>

                </div>
            </div>
          </div>

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
            <div id="tab-<?php echo $f; ?>" class="tab-pane">

              <div class="row">
                <div class="col-md-6">

                  <h3><?php echo language::translate('title_file_to_modify', 'File To Modify'); ?></h3>

                  <div class="form-group">
                    <label><?php echo language::translate('title_file_pattern', 'File Pattern'); ?></label>
                    <?php echo functions::form_text_field('files['.$f.'][name]', true, 'placeholder="path/to/file.php" list="scripts"'); ?>
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
                          <?php echo functions::form_select_field('files['.$f.'][operations]['.$o.'][method]', $method_options, true); ?>
                        </div>

                        <div class="form-group col-md-6">
                          <label><?php echo language::translate('title_match_type', 'Match Type'); ?></label>
                          <?php echo functions::form_toggle_buttons('files['.$f.'][operations]['.$o.'][type]', $type_options, (!isset($_POST['files'][$f]['operations'][$o]['type']) || $_POST['files'][$f]['operations'][$o]['type'] == '') ? 'multiline' : true); ?>
                        </div>

                        <div class="form-group col-md-3">
                          <label><?php echo language::translate('title_on_error', 'On Error'); ?></label>
                          <?php echo functions::form_select_field('files['.$f.'][operations]['.$o.'][onerror]', $on_error_options, true); ?>
                        </div>
                      </div>

                      <div class="form-group">
                        <h4><?php echo language::translate('title_find', 'Find'); ?></h4>
                        <?php if (isset($_POST['files'][$f]['operations'][$o]['type']) && in_array($_POST['files'][$f]['operations'][$o]['type'], ['inline', 'regex'])) { ?>
                        <?php echo functions::form_text_field('files['.$f.'][operations]['.$o.'][find][content]', true, 'class="form-code" required'); ?>
                        <?php } else { ?>
                        <?php echo functions::form_code_field('files['.$f.'][operations]['.$o.'][find][content]', true, 'required'); ?>
                        <?php }?>
                      </div>

                      <div class="row" style="font-size: .8em;">
                        <div class="form-group col-md-2">
                          <label><?php echo language::translate('title_offset_before', 'Offset Before'); ?></label>
                          <?php echo functions::form_text_field('files['.$f.'][operations]['.$o.'][find][offset-before]', true, 'placeholder="0"'); ?>
                        </div>

                        <div class="form-group col-md-2">
                          <label><?php echo language::translate('title_offset_after', 'Offset After'); ?></label>
                          <?php echo functions::form_text_field('files['.$f.'][operations]['.$o.'][find][offset-after]', true, 'placeholder="0"'); ?>
                        </div>

                        <div class="form-group col-md-2">
                          <label><?php echo language::translate('title_index', 'Index'); ?></label>
                          <?php echo functions::form_text_field('files['.$f.'][operations]['.$o.'][find][index]', true, 'placeholder="1,3,.."'); ?>
                        </div>
                      </div>

                      <div class="form-group">
                        <h4><?php echo language::translate('title_insert', 'Insert'); ?></h4>
                        <?php if (isset($_POST['files'][$f]['operations'][$o]['type']) && in_array($_POST['files'][$f]['operations'][$o]['type'], ['inline', 'regex'])) { ?>
                        <?php echo functions::form_text_field('files['.$f.'][operations]['.$o.'][insert][content]', true, 'class="form-code" required'); ?>
                        <?php } else { ?>
                        <?php echo functions::form_code_field('files['.$f.'][operations]['.$o.'][insert][content]', true, 'required'); ?>
                        <?php }?>
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

        <div id="tab-settings" class="tab-pane">

          <h2><?php echo language::translate('title_settings', 'Settings'); ?></h2>

          <div class="settings">
            <?php if (!empty($_POST['settings'])) foreach (array_keys($_POST['settings']) as $key) { ?>
            <fieldset class="setting">
              <div class="row">
                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_title', 'Title'); ?></label>
                  <?php echo functions::form_text_field('settings['.$key.'][title]', true, 'required'); ?>
                </div>

                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_description', 'Description'); ?></label>
                  <?php echo functions::form_text_field('settings['.$key.'][description]', true, 'required'); ?>
                </div>

                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_function', 'Function'); ?></label>
                  <?php echo functions::form_text_field('settings['.$key.'][function]', true, 'required placeholder="text()"'); ?>
                </div>

                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_key', 'Key'); ?></label>
                  <div class="input-group">
                    <span class="input-group-text">{$</span>
                    <?php echo functions::form_text_field('settings['.$key.'][key]', true, 'required'); ?>
                    <span class="input-group-text">}</span>
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label><?php echo language::translate('title_default_Value', 'Default Value'); ?></label>
                  <?php echo functions::form_text_field('settings['.$key.'][default_value]', true); ?>
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
                <?php echo functions::form_code_field('install', true, 'style="height: 200px;"'); ?>
              </div>
            </div>

            <div class="col-md-6">
              <h2><?php echo language::translate('title_uninstall', 'Uninstall'); ?></h2>
              <div class="form-group">
                <label><?php echo language::translate('title_script', 'Script'); ?></label>
                <?php echo functions::form_code_field('uninstall', true, 'style="height: 200px;"'); ?>
              </div>
            </div>
          </div>

          <h2><?php echo language::translate('title_upgrade_patches', 'Upgrade Patches'); ?></h2>

          <div class="upgrades">
            <?php if (!empty($_POST['upgrades'])) foreach (array_keys($_POST['upgrades']) as $key) { ?>
            <fieldset class="upgrade">
              <div class="form-group" style="max-width: 250px;">
                <label><?php echo language::translate('title_version', 'Version'); ?></label>
                <?php echo functions::form_text_field('upgrades['.$key.'][version]', true); ?>
              </div>

              <div class="form-group">
                <label><?php echo language::translate('title_script', 'Script'); ?></label>
                <?php echo functions::form_code_field('upgrades['.$key.'][script]', true, 'style="height: 200px;"'); ?>
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
        <?php echo functions::form_button('save', language::translate('title_save', 'Save'), 'submit', 'class="btn btn-success"', 'save'); ?>
        <?php echo !empty($addon->data['id']) ? functions::form_button('delete', language::translate('title_delete', 'Delete'), 'button', 'class="btn btn-danger"', 'delete') : ''; ?>
        <?php echo functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"', 'cancel'); ?>
      </div>
    </div>
  <?php echo functions::form_end(); ?>
</div>

<div id="modal-uninstall" style="display: none;">
  <?php echo functions::form_begin('uninstall_form', 'post'); ?>

    <h2><?php echo language::translate('title_uninstall_addon', 'Uninstall Add-on'); ?></h2>

    <p><label><?php echo functions::form_checkbox('cleanup', '1', ''); ?> <?php echo language::translate('text_remove_all_traces_of_the_addon', 'Remove all traces of the add-on such as database tables, settings, etc.'); ?></label></p>

    <div>
      <?php echo functions::form_button('delete', language::translate('title_uninstall', 'Uninstall'), 'submit', 'class="btn btn-danger"'); ?>
      <?php echo functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'submit'); ?>
    </div>

  <?php echo functions::form_end(); ?>
</div>

<div id="new-tab-pane-template" style="display: none;">
  <div id="tab-new_tab_index" class="tab-pane">

    <div class="row">
      <div class="col-md-6">

        <div class="form-group">
          <label><?php echo language::translate('title_file_pattern', 'File Pattern'); ?></label>
          <?php echo functions::form_text_field('files[new_tab_index][name]', true, 'placeholder="path/to/file.php" list="scripts"'); ?>
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
        <?php echo functions::form_select_field('files[new_tab_index][operations][new_operation_index][method]', $method_options, ''); ?>
      </div>

      <div class="form-group col-md-6">
        <label><?php echo language::translate('title_match_type', 'Match Type'); ?></label>
        <?php echo functions::form_toggle_buttons('files[new_tab_index][operations][new_operation_index][type]', $type_options, 'multiline'); ?>
      </div>

      <div class="form-group col-md-3">
        <label><?php echo language::translate('title_on_error', 'On Error'); ?></label>
        <?php echo functions::form_select_field('files[new_tab_index][operations][new_operation_index][onerror]', $on_error_options, ''); ?>
      </div>
    </div>

    <div class="form-group">
      <h4><?php echo language::translate('title_find', 'Find'); ?></h4>
      <?php echo functions::form_text_field('files[new_tab_index][operations][new_operation_index][find][content]', '', 'class="form-code" required'); ?>

    </div>

    <div class="row" style="font-size: .8em;">
      <div class="form-group col-md-2">
        <label><?php echo language::translate('title_offset_before', 'Offset Before'); ?></label>
        <?php echo functions::form_text_field('files[new_tab_index][operations][new_operation_index][find][offset-before]', '', 'placeholder="0"'); ?>
      </div>

      <div class="form-group col-md-2">
        <label><?php echo language::translate('title_offset_after', 'Offset After'); ?></label>
        <?php echo functions::form_text_field('files[new_tab_index][operations][new_operation_index][find][offset-after]', '', 'placeholder="0"'); ?>
      </div>

      <div class="form-group col-md-2">
        <label><?php echo language::translate('title_index', 'Index'); ?></label>
        <?php echo functions::form_text_field('files[new_tab_index][operations][new_operation_index][find][index]', '', 'placeholder="1,3,.."'); ?>
      </div>
    </div>

    <div class="form-group">
      <h4><?php echo language::translate('title_insert', 'Insert'); ?></h4>
      <?php echo functions::form_text_field('files[new_tab_index][operations][new_operation_index][insert][content]', '', 'class="form-code" required'); ?>
    </div>

  </fieldset>
</div>

<datalist id="scripts">
  <?php foreach ($files_datalist as $option) { ?>
  <option><?php echo $option; ?></option>
  <?php } ?>
</datalist>

<script>
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
        } else if (item.isDirectory) {
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
      '  <ul class="list-unstyled">',
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

// Tabs

  let new_tab_index = 0;
  while ($('.tab-pane[id="tab-'+new_tab_index+'"]').length) new_tab_index++;

  $('.nav-tabs .add').click(function(e){
    e.preventDefault();

    let tab = '<a class="nav-link" data-toggle="tab" href="#tab-'+ new_tab_index +'"><span class="file">new'+ new_tab_index +'</span> <span class="remove" title="<?php language::translate('title_remove', 'Remove')?>"><?php echo functions::draw_fonticon('fa-times-circle'); ?></span></a>'
      .replace(/new_tab_index/g, new_tab_index);

    let tab_pane = $('#new-tab-pane-template').html()
      .replace(/new_tab_index/g, new_tab_index++);

    $tab_pane = $(tab_pane).hide();

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

    } else if ($tab.next('[data-toggle="tab"]').length) {
      $tab.next('[data-toggle="tab"').trigger('click');
    }

    $(tab_pane).remove();
    $(this).closest('.nav-link').remove();
  });

// Operations

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
          var $newfield = $('<input class="form-code" name="'+ $(field).attr('name') +'" type="text" />').val($(field).val());
          $(field).replaceWith($newfield);
          break;
        default:
          var $newfield = $('<textarea class="form-code" name="'+ $(field).attr('name') +'" /></textarea>').val($(field).val());
          $(field).replaceWith($newfield);
          break;
      }
    });
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

  $('body').on('input', '.form-code', function() {
    $(this).css('height', 'auto');
    $(this).height(this.scrollHeight);
  });

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
        $tab_pane.find('.sources').append(
          $('<div class="script">').html(
            $('<div class="form-code"></div>').text(source_code).prop('outerHTML') +
            $('<div class="script-filename"></div>').text(file).prop('outerHTML')
          )
        );
      });
    });
  });

  $(':input[name^="files"][name$="[name]"]').trigger('input');

  let new_operation_index = 0;
  while ($(':input[name~="files\[[^\]]+\][operations]['+new_operation_index+']"]').length) new_operation_index++;

  $('#files').on('click', '.add', function(e) {
    e.preventDefault();

    let $operations = $(this).closest('.tab-pane').find('.operations'),
      tab_index = $(this).closest('.tab-pane').data('tab-index');

     let output = $('#new-operation-template').html()
       .replace(/tab_index/g, tab_index)
       .replace(/new_operation_index/g, new_operation_index++);

    $operations.append(output);
    reindex_operations($operations);
  });

  $('#files').on('click', '.remove', function(e) {
    e.preventDefault();

    let $operations = $(this).closest('.operations');

    if (!confirm("<?php echo language::translate('text_are_you_sure', 'Are you sure?'); ?>")) return;

    $(this).closest('.operation').remove();
    reindex_operations($operations);
  });

  $('#files').on('click', '.move-up, .move-down', function(e) {
    e.preventDefault();

    let $row = $(this).closest('.operation'),
      $operations = $(this).closest('.operations');

    if ($(this).is('.move-up') && $row.prevAll().length > 0) {
      $row.insertBefore($row.prev());
    } else if ($(this).is('.move-down') && $row.nextAll().length > 0) {
      $row.insertAfter($row.next());
    }

    reindex_operations($operations);
  });

// Settings
  let new_setting_key_index = 0;
  while ($(':input[name^="settings['+new_setting_key_index+']"]').length) new_setting_key_index++;

  $('button[name="add_setting"]').click(function(){

    let output = [
      '<fieldset class="setting">',
      '  <div class="row">',
      '    <div class="form-group col-md-4">',
      '      <label><?php echo functions::escape_js(language::translate('title_title', 'Title')); ?></label>',
      '      <?php echo functions::escape_js(functions::form_text_field('settings[new_setting_key_index][title]', '', 'required')); ?>',
      '    </div>',
      '',
      '    <div class="form-group col-md-4">',
      '      <label><?php echo functions::escape_js(language::translate('title_description', 'Description')); ?></label>',
      '      <?php echo functions::escape_js(functions::form_text_field('settings[new_setting_key_index][description]', '', 'required')); ?>',
      '    </div>',
      '',
      '    <div class="form-group col-md-4">',
      '      <label><?php echo functions::escape_js(language::translate('title_function', 'Function')); ?></label>',
      '      <?php echo functions::escape_js(functions::form_text_field('settings[new_setting_key_index][function]', '', 'required')); ?>',
      '    </div>',
      '',
      '    <div class="form-group col-md-4">',
      '      <label><?php echo functions::escape_js(language::translate('title_key', 'Key')); ?></label>',
      '      <div class="input-group">',
      '        <span class="input-group-text">{$</span>',
      '        <?php echo functions::escape_js(functions::form_text_field('settings[new_setting_key_index][key]', '', 'required')); ?>',
      '        <span class="input-group-text">}</span>',
      '      </div>',
      '    </div>',
      '',
      '    <div class="form-group col-md-4">',
      '      <label><?php echo functions::escape_js(language::translate('title_default_value', 'Default Value')); ?></label>',
      '      <?php echo functions::escape_js(functions::form_text_field('settings[new_setting_key_index][default_value]', '')); ?>',
      '    </div>',
      '  </div>',
      '</fieldset>'
    ].join('\n')
    .replace(/new_setting_key_index/, 'new_' + new_setting_key_index++);

    $('.settings').append(output);
  });

// Upgrade Patches
  let new_upgrade_patch_index = 0;
  while ($(':input[name^="upgrades['+new_upgrade_patch_index+']"]').length) new_upgrade_patch_index++;

  $('button[name="add_patch"]').click(function(){

    let output = [
      '<fieldset class="upgrade">',
      '  <div class="form-group" style="max-width: 250px;">',
      '    <label><?php echo functions::escape_js(language::translate('title_version', 'Version')); ?></label>',
      '    <?php echo functions::escape_js(functions::form_text_field('upgrades[new_upgrade_patch_index][version]', '')); ?>',
      '  </div>',
      '',
      '  <div class="form-group">',
      '    <label><?php echo functions::escape_js(language::translate('title_script', 'Script')); ?></label>',
      '    <?php echo functions::escape_js(functions::form_code_field('upgrades[new_upgrade_patch_index][script]', '', 'style="height: 200px;"')); ?>',
      '  </div>',
      '</fieldset>'
    ].join('\n')
    .replace(/new_upgrade_patch_index/g, 'new_' + new_upgrade_patch_index);

    $('.upgrades').append(output);
  });

  $('.card-action button[name="delete"]').click(function(e){
    e.preventDefault();
    $.featherlight('#modal-uninstall', {
      seamless: true,
    });
  });
</script>