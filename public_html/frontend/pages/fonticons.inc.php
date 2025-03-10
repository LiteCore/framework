<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/fonticons.inc.php
	 */

	document::$title[] = language::translate('title_font_icons', 'Font Icons');
	document::$description = language::translate('meta_description:font_icons', '');

	breadcrumbs::add(language::translate('title_font_icons', 'Font Icons'), document::ilink('fonticons'));

	$_page = new ent_view('app://frontend/template/pages/font_icons.inc.php');

	$font_icons = [];

	foreach (file('app://assets/litecore/less/framework/fonticons.less') as $line) {
		if (preg_match('#^\.(icon-[a-z0-9-]+):before\s*{#', $line, $matches)) {
			$font_icons[] =  $matches[1];
		}
	}

	$_page->snippets['font_icons'] = $font_icons;

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>
<style>
.font-icons {
	columns: 200px auto;
	gap: 1em;
	margin-bottom: 2em;
}

.icon {
	display: flex;
	flex-direction: row;
	align-items: center;
	margin-bottom: 1em;
}
.icon [class^="icon-"] {
	aspect-ratio: 1;
	border: 1px solid var(--default-border-color);
	border-radius: var(--border-radius);
	padding: 1rem;
	margin-right: 1em;
	font-size: 1.5em;
}

.icon .name {
	margin-top: 0.5em;
	font-family: monospace;
}

.special-icons {
	display: flex;
	flex-wrap: wrap;
	gap: 1em;
}

.special-icons i {
	font-size: 4em;
	aspect-ratio: 1;
	line-height: 1;
}
.special-icons .icon-wrench-circle {
	border: 1px solid var(--default-border-color);
}

.special-icons .icon-wrench-circle::after {
	font-family: 'Fonticons';
	content: "\e057";
	border: 1px solid red;
	font-size: 1.5em;
	font-style: normal;
}
.special-icons .icon-wrench-circle::before {
	font-size: .75em;
	line-height: 2em;
	width: 2em;
	border: 1px solid blue;
	position: absolute;
}
.special-icons .icon-wrench-triangle::after {
	font-family: 'Fonticons';
	content: "\e008";
	border: 1px solid red;
	font-size: 1.5em;
	font-style: normal;
}

.special-icons .icon-wrench-triangle::before {
	font-size: .75em;
	line-height: 2em;
	width: 2em;
	border: 1px solid blue;
	position: absolute;
	color: red;
}
.special-icons .icon-wrench-triangle::after {
	font-family: 'Fonticons';
	content: "\e008";
	border: 1px solid red;
	font-size: 1.5em;
	font-style: normal;
}
</style>

<main class="container">
	<div class="card">
		<div class="card-header">
			<div class="card-title"><?php echo language::translate('title_font_icon_kit', 'Font Icon Kit'); ?></div>
			<p><?php echo language::translate('description_font_icon_kit', 'Below is a list of all available font icons.'); ?></p>
		</div>

		<div class="card-body">
			<div class="font-icons">
				<?php foreach ($font_icons as $icon) { ?>
				<div class="icon">
					<?php echo functions::draw_fonticon($icon); ?>
					<div class="name">
						<?php echo $icon; ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>