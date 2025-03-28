<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/partials/site_navigation.inc.php
	 */

	$_partial = new ent_view('app://frontend/template/partials/site_navigation.inc.php');

	$site_navigation_cache_token = cache::token('site_navigation', ['language']);
	if (!$_partial->snippets = cache::get($site_navigation_cache_token)) {

		$_partial->snippets = [
			'left' => [],
			'right' => [],
		];

		$_partial->snippets['left'][] = [
			'id' => 'home',
			'icon' => '',
			'name' => language::translate('title_home', 'Home'),
			'link' => document::ilink(''),
			'badge' => '',
			'priority' => -1,
		];

		//$_partial->snippets['left'][] = [
		//	'id' => '',
		//	'name' => 'Dropdown',
		//	'link' => '#',
		//	'priority' => 1,
		//	'subitems' => [],  <-- Put items in here
		//];

		$_partial->snippets['right'][] = [
			'id' => 'contact',
			'name' => language::translate('title_contact', 'Contact'),
			'link' => document::ilink('contact'),
			'priority' => 99,
		];

		// Sort menu items by priority
		$sort_items = function($a, $b) {
			if (!isset($a['priority'])) $a['priority'] = 0;
			if (!isset($b['priority'])) $b['priority'] = 0;
			if ($a['priority'] == $b['priority']) return;
			return ($a['priority'] < $b['priority']) ? -1 : 1;
		};

		uasort($_partial->snippets['left'], $sort_items);
		uasort($_partial->snippets['right'], $sort_items);

		cache::set($site_navigation_cache_token, $_partial->snippets);
	}

	$_partial->snippets['draw_menu_item'] = function($item, $indent = 0, $is_dropdown_item=false) use (&$draw_menu_item) {

		if (!empty($item['subitems'])) {
			return implode(PHP_EOL, [
				'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'navbar-item') .' dropdown'. (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_attr($item['id']) .'"' : '') .'>',
				'	<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">',
				'		'. $item['name'],
				!empty($item['badge']) ? '   <div class="badge">'. $item['badge'] .'</div>' : '',
				'	</a>',
				'	<ul class="dropdown-menu">',
				'		'. implode(PHP_EOL, array_map($draw_menu_item, $item['subitems'], [$indent+1], [true])),
				'	</ul>',
				'</li>',
			]);
		}

		return implode(PHP_EOL, [
			'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'navbar-item') . (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_attr($item['id']) .'"' : '') .'>',
			'	<a class="nav-link" href="'. functions::escape_attr($item['link']) .'">',
			'		'. $item['name'],
			!empty($item['badge']) ? '		<div class="badge">'. $item['badge'] .'</div>' : '',
			'	</a>',
			'</li>',
		]);
	};

	//echo $_partial->render();
	extract($_partial->snippets);

?>
<header id="header" class="container">
	<div id="navigation" class="navbar navbar-sticky">

		<div class="navbar-header">
			<a class="logotype" href="<?php echo document::href_ilink(''); ?>">
				<img src="<?php echo document::href_rlink('storage://images/favicons/favicon-192x192.png'); ?>" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>"> <?php echo settings::get('site_name'); ?>
			</a>

			<button type="button" class="btn btn-default navbar-toggler hidden-md hidden-lg hidden-xl hidden-xxl" data-toggle="offcanvas" data-target="#offcanvas">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div id="offcanvas" class="offcanvas">
			<div class="offcanvas-header">
				<div class="offcanvas-title"><?php echo settings::get('site_name'); ?></div>
				<button type="button" class="btn btn-default" data-toggle="dismiss"><?php echo functions::draw_fonticon('icon-times'); ?></button>
			</div>

			<div class="offcanvas-body">

				<ul class="navbar-nav">

					<?php if ($left) { ?>
					<?php foreach ($left as $item) echo $draw_menu_item($item); ?>
					<?php } ?>
				</ul>

				<?php if ($right) { ?>
				<ul class="navbar-nav">
					<?php foreach ($right as $item) echo $draw_menu_item($item); ?>
				</ul>
				<?php } ?>
			</div>
		</div>
	</div>

</header>
