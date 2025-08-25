<?php

	document::$title[] = t('title_logotype', 'Logotype');

	breadcrumbs::add(t('title_appearance', 'Appearance'));
	breadcrumbs::add(t('title_logotype', 'Logotype'), document::ilink());

	if (isset($_POST['upload'])) {

		try {

			if (empty($_FILES['image'])) {
				throw new Exception(t('error_missing_image', 'You must select an image'));
			}

			$image = new ent_image($_FILES['image']['tmp_name']);

			if (!$image->width) {
				throw new Exception(t('error_invalid_image', 'The image is invalid'));
			}

			$file = 'storage://images/' . $filename;

			if (is_file($file)) {
				unlink($file);
			}

			functions::image_delete_cache($file);

			$image->resample(512, 512, 'FIT_ONLY_BIGGER');

			if (!$image->save($file)) {
				throw new Exception(t('error_failed_uploading_image', 'The uploaded image failed saving to disk. Make sure permissions are set.'));
			}

			notices::add('success', t('success_changes_saved', 'Changes saved'));
			reload();
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}
?>
<div class="card">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo t('title_logotype', 'Logotype'); ?>
		</div>
	</div>

	<div class="card-body">
		<?php echo functions::form_begin('logotype_form', 'post', false, true); ?>

			<div style="max-width: 480px;">
				<img class="thumbnail fit" src="<?php echo document::href_rlink('storage://images/logotype.svg'); ?>" alt="" style="margin: 0 0 2em 0;">
			</div>

			<div class="form-group" style="max-width: 480px;">
				<label><?php echo t('title_new_image', 'New Image'); ?></label>
				<div class="input-group">
					<?php echo functions::form_input_file('image', 'accept="image/*"'); ?>
					<?php echo functions::form_button('upload', t('title_upload', 'Upload'), 'submit'); ?>
				</div>
			</div>

		<?php echo functions::form_end(); ?>
	</div>
</div>