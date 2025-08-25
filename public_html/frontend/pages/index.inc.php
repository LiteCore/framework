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
	<section class="grid">
		<div class="col-6">

			<h1>
				<?php echo PLATFORM_NAME; ?>/<?php echo PLATFORM_VERSION; ?>
			</h1>

			<p class="text-lg text-medium text-justify">
				LiteCore is an <strong>all-in-one minimalistic website framework</strong> built with PHP. It is simple to use, easy to engineer, and a boilerplate for building websites. Born out of an obsession for thinking lightweight.
			</p>

			<p class="text-lg">
				LiteCore might do things a little differently from what you are used to in overcomplex frameworks. But everything is done with simplicity in mind. One of the best ways to learn is by doing. And while you go on doing things, we intend to make it as easy for you as possible.
			</p>

			<p class="text-lg">
				Reach your goals faster by doing less of all the boring work. Spend your time wisely on what you really like to be doing.
			</p>

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
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> Web Frontend</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> Web Backend</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> HTML Template</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> LESS & CSS Framework</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> Font Icon Kit</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> jQuery 4</li>
						<li><?php echo functions::draw_fonticon('icon-check iconfx-shadow'); ?> vMod Ready</li>
					</ul>
				</div>

			</div>
		</div>
	</section>
</main>