<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/printable.inc.php
	 */

	$_page = new ent_view('app://frontend/template/pages/printable.inc.php');

	// Place your snippets here
	// ...

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>
<style>
.card-header p {
	margin-bottom: 0;
}
iframe{
	border-radius: var(--border-radius);
}
</style>

<main class="container">
	<div class="card">
		<div class="card-header">
			<div class="card-title"><?php echo language::translate('title_printable_components', 'Printable Components'); ?></div>
			<p><?php echo language::translate('description_printable_components', 'CSS framework for printable components.'); ?></p>
		</div>

		<div class="card-body">
			<iframe src="<?php echo document::href_ilink('printable_preview'); ?>" style="width: 100%; border: 0;"></iframe>
		</div>
	</div>
</main>

<script>
	$('iframe').on('load', function() {
		var $iframe = $(this);
		$iframe.height($iframe.contents().find('body').height());
		$iframe.contents().find('body').css({
			'box-shadow': 'inset 0 0 1em rgba(0, 0, 0, .25)'
		});
	});
</script>