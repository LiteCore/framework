<?php

	if (!empty($_GET['page_id'])) {
		$page = new ent_page($_GET['page_id']);
	} else {
		$page = new ent_page();
	}

	if (!$_POST) {
		$_POST = $page->data;
	}

	document::$title[] = !empty($page->data['id']) ? language::translate('title_edit_page', 'Edit Page') : language::translate('title_create_new_page', 'Create New Page');

	breadcrumbs::add(language::translate('title_pages', 'Pages'), document::ilink(__APP__.'/pages'));
	breadcrumbs::add(!empty($page->data['id']) ? language::translate('title_edit_page', 'Edit Page') : language::translate('title_create_new_page', 'Create New Page'));

	if (isset($_POST['save'])) {

		try {

			if (empty($_POST['title'])) {
				throw new Exception(language::translate('error_missing_title', 'You must enter a title.'));
			}

			if (empty($_POST['status'])) $_POST['status'] = 0;

			foreach ([
				'status',
				'parent_id',
				'title',
				'content',
				'priority',
				'head_title',
				'meta_description',
			] as $field) {
				if (isset($_POST[$field])) $page->data[$field] = $_POST[$field];
			}

			$page->save();

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/pages'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (isset($_POST['delete'])) {

		try {
			if (empty($page->data['id'])) throw new Exception(language::translate('error_must_provide_page', 'You must provide a page'));

			$page->delete();

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink(__APP__.'/pages'));
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}
?>
<div class="card">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo !empty($page->data['id']) ? language::translate('title_edit_page', 'Edit Page') : language::translate('title_create_new_page', 'Create New Page'); ?>
		</div>
	</div>

	<div class="card-body">
		<?php echo functions::form_begin('pages_form', 'post', false, false, 'style="max-width: 640px;"'); ?>

			<div class="grid">
				<div class="form-group col-md-6">
					<label><?php echo language::translate('title_status', 'Status'); ?></label>
					<?php echo functions::form_toggle('status', 'e/d', (isset($_POST['status'])) ? $_POST['status'] : '1'); ?>
				</div>
				<div class="form-group col-md-6">
					<label><?php echo language::translate('title_priority', 'Priority'); ?></label>
					<?php echo functions::form_input_number('priority', true); ?>
				</div>
			</div>

			<div class="grid">
				<div class="form-group col-md-6">
					<label><?php echo language::translate('title_parent', 'Parent'); ?></label>
					<?php echo functions::form_select_page('parent_id', true); ?>
				</div>
			</div>

			<?php if (count(language::$languages) > 1) { ?>
			<nav class="tabs">
				<?php foreach (language::$languages as $language) { ?>
				<a class="tab-item<?php echo ($language['code'] == language::$selected['code']) ? ' active' : ''; ?>" data-toggle="tab" href="#<?php echo $language['code']; ?>"><?php echo $language['name']; ?></a>
				<?php } ?>
			</nav>
			<?php } ?>

			<div class="tab-content">
				<?php foreach (array_keys(language::$languages) as $language_code) { ?>
				<div id="<?php echo $language_code; ?>" class="tab-pane fade in<?php echo ($language_code == language::$selected['code']) ? ' active' : ''; ?>">
					<div class="form-group">
						<label><?php echo language::translate('title_title', 'Title'); ?></label>
						<?php echo functions::form_regional_text('title['. $language_code .']', $language_code, true, ''); ?>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_content', 'Content'); ?></label>
						<?php echo functions::form_regional_wysiwyg('content['. $language_code .']', $language_code, true, 'style="height: 400px;"'); ?>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_head_title', 'Head Title'); ?></label>
						<?php echo functions::form_regional_text('head_title['. $language_code .']', $language_code, true); ?>
					</div>

					<div class="form-group">
						<label><?php echo language::translate('title_meta_description', 'Meta Description'); ?></label>
						<?php echo functions::form_regional_text('meta_description['. $language_code .']', $language_code, true); ?>
					</div>
				</div>
				<?php } ?>
			</div>

			<div class="card-action">
				<?php echo functions::form_button('save', language::translate('title_save', 'Save'), 'submit', 'class="btn btn-success"', 'save'); ?>
				<?php echo !empty($page->data['id']) ? functions::form_button('delete', language::translate('title_delete', 'Delete'), 'submit', 'formnovalidate class="btn btn-danger" onclick="if (!confirm(&quot;'. language::translate('text_are_you_sure', 'Are you sure?') .'&quot;)) return false;"', 'delete') : false; ?>
				<?php echo functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"', 'cancel'); ?>
			</div>

		<?php echo functions::form_end(); ?>
	</div>
</div>

<script>
	$('input[name^="title"]').on('input', function(e){
		let language_code = $(this).attr('name').match(/\[(.*)\]$/)[1];
		$('.tabs a[href="#'+language_code+'"]').css('opacity', $(this).val() ? 1 : .5);
		$('input[name="head_title['+language_code+']"]').attr('placeholder', $(this).val());
	}).trigger('input');
</script>