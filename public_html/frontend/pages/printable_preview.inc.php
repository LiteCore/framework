<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/printables.inc.php
	 */

	document::$layout = 'printable';

	$_page = new ent_view('app://frontend/template/pages/printables.inc.php');

	// Place your snippets here
	// ...

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>

<section class="page" data-size="US-Letter">
	<header>
		Header
	</header>

	<main style="align-content: center; text-align: center;">
		<h1>US-Letter</h1>
	</main>

	<footer>
		Footer
	</footer>
</section>

<section class="page" data-size="A4">
	<header>
		Header
	</header>

	<main style="align-content: center; text-align: center;">
		<h1>A4</h1>
	</main>

	<footer>
		Footer
	</footer>
</section>

<section class="page" data-size="A4R">
	<header>
		Header
	</header>

	<main style="align-content: center; text-align: center;">
		<h1>A4R</h1>
	</main>

	<footer>
		Footer
	</footer>
</section>