<?php

	$site_navigation = new ent_view('app://frontend/template/partials/site_navigation.inc.php');

	$site_navigation_cache_token = cache::token('site_navigation', ['language']);
	if (!$site_navigation->snippets = cache::get($site_navigation_cache_token)) {

		$site_navigation->snippets = [
			'left' => [],
			'right' => [],
		];

		//$site_navigation->snippets['left'][] = [
		//	'id' => 'home',
		//	'icon' => '',
		//	'name' => functions::draw_fonticon('fa-home'),
		//	'link' => document::ilink(''),
		//	'badge' => '',
		//	'priority' => -1,
		//];

		$site_navigation->snippets['left'][] = [
			'id' => 'css-framework',
			'icon' => '',
			'name' => 'CSS',
			'link' => document::ilink('css'),
			'badge' => '',
			'priority' => 1,
		];

		$site_navigation->snippets['left'][] = [
			'id' => 'printables',
			'icon' => '',
			'name' => 'Printables',
			'link' => document::ilink('printables'),
			'badge' => '',
			'priority' => 2,
		];

		//$site_navigation->snippets['left'][] = [
		//	'id' => '',
		//	'name' => 'Dropdown',
		//	'link' => '#',
		//	'priority' => 1,
		//	'subitems' => [],  <-- Put items in here
		//];

		$site_navigation->snippets['right'][] = [
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

		uasort($site_navigation->snippets['left'], $sort_items);
		uasort($site_navigation->snippets['right'], $sort_items);

		cache::set($site_navigation_cache_token, $site_navigation->snippets);
	}

	//echo $site_navigation;
	extract($site_navigation->snippets);

	$draw_menu_item = function($item, $indent = 0, $is_dropdown_item=false) use (&$draw_menu_item) {

		if (!empty($item['subitems'])) {
			return implode(PHP_EOL, [
				'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'nav-item') .' dropdown'. (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_attr($item['id']) .'"' : '') .'>',
				'  <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">',
				'		'. $item['name'],
				!empty($item['badge']) ? '   <div class="badge">'. $item['badge'] .'</div>' : '',
				'  </a>',
				'  <ul class="dropdown-menu">',
				'    '. implode(PHP_EOL, array_map($draw_menu_item, $item['subitems'], [$indent+1], [true])),
				'  </ul>',
				'</li>',
			]);
		}

		return implode(PHP_EOL, [
			'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'nav-item') . (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_attr($item['id']) .'"' : '') .'>',
			'  <a class="nav-link" href="'. functions::escape_attr($item['link']) .'">',
			'    '. $item['name'],
			!empty($item['badge']) ? '    <div class="badge">'. $item['badge'] .'</div>' : '',
			'  </a>',
			'</li>',
		]);
	};

?>
<header id="header" class="container">
	<div id="navigation" class="navbar navbar-sticky">

		<div class="navbar-header">
			<a class="logotype" href="<?php echo document::href_ilink(''); ?>">
				<img src="<?php echo document::href_rlink('storage://images/logotype.png'); ?>" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>">
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
				<button type="button" class="btn btn-default" data-toggle="dismiss"><?php echo functions::draw_fonticon('fa-times'); ?></button>
			</div>

			<div class="offcanvas-body">

				<ul class="navbar-nav">

					<li class="nav-item">
						<a class="nav-link" href="<?php echo document::href_ilink(''); ?>">
							<?php echo functions::draw_fonticon('fa-home hidden-xs hidden-sm'); ?> <span class="hidden-md hidden-lg hidden-xl hidden-xxl"><?php echo language::translate('title_home', 'Home'); ?></span>
						</a>
					</li>

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
