<?php

	$box_apps_menu = new ent_view('app://backend/template/partials/box_apps_menu.inc.php');
	$box_apps_menu->snippets['apps'] = [];

	//$apps = functions::admin_get_apps();
	$apps = &$GLOBALS['apps'];

	foreach ($apps as $app) {

		if (!empty(administrator::$data['apps']) && empty(administrator::$data['apps'][$app['id']]['status'])) continue;

		$box_apps_menu->snippets['apps'][$app['id']] = [
			'id' => $app['id'],
			'name' => $app['name'],
			'link' => document::ilink($app['id'] .'/'. $app['default']),
			'theme' => [
				'icon' => !(empty($app['theme']['icon'])) ? $app['theme']['icon'] : 'icon-plus',
				'color' => !(empty($app['theme']['color'])) ? $app['theme']['color'] : '#97a3b5',
			],
			'active' => (defined('__APP__') && __APP__ == $app['id']),
			'menu' => [],
		];

		if (!empty($app['menu'])) {
			foreach ($app['menu'] as $item) {

				if (!empty(administrator::$data['apps']) && (empty(administrator::$data['apps'][$app['id']]['status']) || !in_array($item['doc'], administrator::$data['apps'][$app['id']]['docs']))) continue;

				$params = !empty($item['params']) ? array_merge(['app' => $app['id'], 'doc' => $item['doc']], $item['params']) : ['app' => $app['id'], 'doc' => $item['doc']];

				if (defined('__DOC__') && __DOC__ == $item['doc']) {
					$selected = true;
					if (!empty($item['params'])) {
						foreach ($item['params'] as $param => $value) {
							if (!isset($_GET[$param]) || $_GET[$param] != $value) {
								$selected = false;
								break;
							}
						}
					}
				} else {
					$selected = false;
				}

				$box_apps_menu->snippets['apps'][$app['id']]['menu'][] = [
					'title' => $item['title'],
					'doc' => $item['doc'],
					'link' => document::ilink($app['id'] .'/'. $item['doc'], fallback($item['params'], [])),
					'active' => $selected,
				];
			}
		}
	}

	echo $box_apps_menu;
