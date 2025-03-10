<?php

	$_partial = new ent_view('app://backend/template/partials/site_top_navigation.inc.php');

	$_partial->snippets = [
		'items' => [
			[
				'title' => language::$selected['code'],
				'link' => '#',
				'icon' => 'icon-search',
				'subitems' => array_map(function($language) {
					return [
						'title' => $language['name'],
						'link' => document::href_ilink(null, [], [], [], $language['code']),
					];
				}, language::$languages),
			],
			[
				'title' => language::translate('title_webmail', 'Webmail'),
				'link' => ($webmail_link = settings::get('webmail_link')) ? functions::escape_html($webmail_link) : document::href_ilink('settings/advanced', ['key' => 'webmail_link', 'action' => 'edit']),
				'icon' => 'icon-envelope',
			],
			[
				'title' => language::translate('title_control_panel', 'Control Panel'),
				'link' => ($control_panel_link = settings::get('control_panel_link')) ? functions::escape_html($control_panel_link) : document::href_ilink('settings/advanced', ['key' => 'control_panel_link', 'action' => 'edit']),
				'icon' => 'icon-cogs',
			],
			[
				'title' => language::translate('title_database_manager', 'Database Manager'),
				'link' => ($database_admin_link = settings::get('database_admin_link')) ? functions::escape_html($database_admin_link) : document::href_ilink('settings/advanced', ['key' => 'database_admin_link', 'action' => 'edit']),
				'icon' => 'icon-database',
			],
			[
				'title' => language::translate('title_frontend', 'Frontend'),
				'link' => document::href_ilink('f:'),
				'icon' => 'icon-display',
			],
			[
				'title' => language::translate('title_help', 'Help'),
				'link' => 'https://litecart.net/wiki/',
				'icon' => 'icon-question',
			],
			[
				'title' => language::translate('title_sign_out', 'Sign Out'),
				'link' => document::href_ilink('logout'),
				'icon' => 'icon-sign-out',
			],
		],
	];

	$_partial->snippets['draw_menu_item'] = function($item, $indent = 0, $is_dropdown_item=false) use (&$draw_menu_item) {

		if (!empty($item['subitems'])) {
			return implode(PHP_EOL, [
				'<li class="'. ($is_dropdown_item ? 'dropdown-item' : 'nav-item') .' dropdown'. (!empty($item['hidden-xs']) ? ' hidden-xs' : '') .'"'. (!empty($item['id']) ? ' data-id="'. functions::escape_attr($item['id']) .'"' : '') .'>',
				'  <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">',
				'		'. $item['title'],
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
			'    '. (!empty($item['icon']) ? functions::draw_fonticon($item['icon']) .' ' : '') . $item['title'],
			!empty($item['badge']) ? '    <div class="badge">'. $item['badge'] .'</div>' : '',
			'  </a>',
			'</li>',
		]);
	};

	//echo $_partial->render();
	extract($_partial->snippets);

?>
<style>
.brightness .form-toggle {
	padding: 0 !important;
	gap: 0;
}
</style>

<ul id="top-bar" class="hidden-print">
	<li>
		<div>
			<label class="nav-toggle" for="sidebar-compressed">
				<?php echo functions::draw_fonticon('icon-bars'); ?>
			</label>
		</div>
	</li>

	<li style="flex-grow: 1;">
		<div id="search" class="dropdown">
			<?php echo functions::form_input_search('query', false, 'placeholder="'. functions::escape_attr(language::translate('title_search_entire_platform', 'Search entire platform')) .'&hellip;" autocomplete="off"'); ?>
			<div class="results dropdown-menu"></div>
		</div>
	</li>

	<li>
		<div class="btn-group" data-toggle="buttons">
			<button name="font_size" class="btn btn-default btn-sm" type="button" value="decrease"><span style="font-size: .8em;">A</span></button>
			<button name="font_size" class="btn btn-default btn-sm" type="button" value="increase"><span style="font-size: 1.25em;">A</span></button>
		</div>
	</li>

	<li class="brightness">
		<?php echo functions::form_toggle('dark_mode', ['0' => functions::draw_fonticon('icon-sun'), '1' => functions::draw_fonticon('icon-moon')]); ?>
	</li>

	<?php foreach ($items as $item) echo $draw_menu_item($item); ?>

</ul>