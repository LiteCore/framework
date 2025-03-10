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
.icon-check {
	color:rgb(31, 156, 0);
	margin-inline-end: 1em;
}

code::before {
	content: '$';
  font-weight: bold;
	color: #333;
}
</style>

<main id="content" class="container">
	<div class="grid">
		<div class="col-6">

			<h1>
				<?php echo PLATFORM_NAME; ?>/<?php echo PLATFORM_VERSION; ?>
			</h1>

			<p class="text-lg text-medium text-justify">
				LiteCore is an <strong>all-in-one minimalistic website framework</strong> built with PHP. It is simple to use, easy to engineer, and a boilerplate for building websites. Born out of an obsession for thinking lightweight.
			</p>

			<p class="text-lg">
				LiteCore might do things a little differently from what you are used to in overcomplex frameworks. But everything is done with simplicity in mind. One of the best ways to learn is by doing. And while you go on doing things, we intend to make that as easy as possible for you.
			</p>

			<p class="text-lg">
				Reach your goals faster by doing less of all the work. Spend your time wisely on what you really like to be doing.
			</p>

			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Install Now</h3>
				</div>

				<div class="card-body">
					<code>
						bash -c "$(curl https://litecore.dev/install.sh)"
					</code>

					<h3>Or, by skipping the wizard:</h3>
					<pre><code><?php echo implode(PHP_EOL, [
							'bash -c "$(curl https://litecore.dev/install.sh)" -y \\',
							'	--dir=/path/to/where \\',
							'	--db_hostname=localhost \\',
							'	--db_username=user \\',
							'	--db_password=secret \\',
							'	--db_name=database',
						]); ?></code></pre>

					<h3>More options:</h3>
					<code>
						bash -c "$(curl https://litecore.dev/install.sh)" --help
					</code>
				</div>
			</div>

			<a class="btn btn-default btn-lg" href="<?php echo document::href_ilink('about'); ?>">
				Learn More
			</a>

		</div>

		<div class="col-6">

			<div class="card">

				<div class="card-header">
					<div class="card-title">The LiteCore website framework contains:</div>
				</div>

				<div class="card-body">
					<ul class="list list-unstyled">
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> Folder Structure</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> PHP Framework</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> HTML Template</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> jQuery 4</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> LESS & CSS Framework</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> Font Icon Kit</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> vMod Ready</li>
					</ul>
				</div>

			</div>

			<div class="panel">
				<div class="panel-body">
					...
				</div>
				<div class="panel-footer">
					...
				</div>
			</div>

		</div>
</main>