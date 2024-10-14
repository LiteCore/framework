<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/index.inc.php
	 */

	$_page = new ent_view('app:/frontend/template/pages/index.inc.php');

	// Place your snippets here
	// ...

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>
<main id="content" class="container">
	<h1>
		<?php echo PLATFORM_NAME; ?>/<?php echo PLATFORM_VERSION; ?>
	</h1>
</main>