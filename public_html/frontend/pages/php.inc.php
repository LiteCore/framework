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
<style>

</style>

<main id="content" class="container">
	<div class="grid">
		<div class="col-6">

			<h1>
				<?php echo language::translate('title_php_framework', 'PHP Framework'); ?>
			</h1>

			<h2><?php echo language::translate('title_create_a_new_script', 'Create a New Script'); ?></h2>

			<div class="card">
				<div class="card-body">
					<pre><code><?php echo htmlspecialchars(implode(PHP_EOL, [
						'<?php',
						'',
						'	include \'includes/app_header.inc.php\'; // Include the framework',
						'',
						'	// Now do what you like'
					])); ?></code></pre>
				</div>
			</div>

			<h2><?php echo language::translate('title_create_a_new_page', 'Create a New Web Page'); ?></h2>

			<div class="card">
				<div class="card-body">
					<pre><code><?php echo htmlspecialchars(implode(PHP_EOL, [
						'<?php',
						'	// Put the page in the pages directory and name it my_page.inc.php',
						'?>',
						'',
						'<style>',
						'...',
						'</style>',
						'',
						'<main id="content" class="container">',
						'	Output your HTML here',
						'</main>',
						'',
						'<script>',
						'...',
						'</script>'
					])); ?></code></pre>
				</div>
			</div>

			<h2><?php echo language::translate('title_create_a_new_page_and_use_a_template_view_for_html', 'Create a New Web Page and Use a Template View for HTML'); ?></h2>

			<div class="card">
				<div class="card-body">
					<pre><code><?php echo htmlspecialchars(implode(PHP_EOL, [
						'<?php',
						'',
						'	$_page = new ent_view(\'app:/frontend/template/pages/my_page.inc.php\');',
						'',
						' $page->snippets = [',
						'		\'title\' => language::translate(\'title_my_page\', \'My Page\'),',
						'		\'description\' => language::translate(\'description_my_page\', \'This is my page\'),',
						'	];',
						'',
						'	echo $_page->render();',
					])); ?></code></pre>
				</div>
			</div>

			<h2><?php echo language::translate('title_send_an_email', 'Send an Email'); ?></h2>

			<div class="card">
				<div class="card-body">
					<pre><code><?php echo htmlspecialchars(implode(PHP_EOL, [

						'	$email = new ent_email();',
						'	$email->add_recipient(\'user@email.com\', \'John Doe\')',
						'		->set_subject(\'Hello World\')',
						'		->add_body(\'This is a test message\')',
						'   ->add_attachment(\'path/to/file.pdf\')',
						'		->send();'
					])); ?></code></pre>
				</div>
			</div>

		</div>

		<div class="col-6">

		</div>
</main>