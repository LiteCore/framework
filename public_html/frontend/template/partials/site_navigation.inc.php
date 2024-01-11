<?php

	$draw_menu_item = function($item, $indent = 0, $is_dropdown_item=false) use (&$draw_menu_item) {

		if (!empty($item['subitems'])) {
			return implode(PHP_EOL, [
				'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'nav-item') .' dropdown'. (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_html($item['id']) .'"' : '') .'>',
				'	<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">',
				'		'. $item['name'] .' <span class="caret"></span>',
				'	</a>',
				'	<ul class="dropdown-menu">',
				'		'. implode(PHP_EOL, array_map($draw_menu_item, $item['subitems'], [$indent+1], [true])),
				'	</ul>',
				'</li>',
			]);
		}

		return implode(PHP_EOL, [
			'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'nav-item') . (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_html($item['id']) .'"' : '') .'>',
			'	<a class="nav-link" href="'. functions::escape_html($item['link']) .'">',
			'		'. $item['name'],
			'	</a>',
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

					<?php foreach ($items as $item) echo $draw_menu_item($item); ?>
				</ul>

				<ul class="navbar-nav">


				</ul>
			</div>
		</div>
	</div>
</header>
