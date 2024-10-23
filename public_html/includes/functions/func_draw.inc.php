<?php

	function draw_fonticon($class, $parameters='') {

		switch(true) {

			// Bootstrap Icons
			case (substr($class, 0, 3) == 'bi-'):
				document::$head_tags['bootstrap-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">';
				return '<i class="bi '. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Fontawesome 4
			case (substr($class, 0, 3) == 'fa-'):
				//document::$head_tags['fontawesome'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css">'; // Uncomment if removed from lib_document
				return '<i class="fa '. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Fontawesome 5
			case (substr($class, 0, 7) == 'far fa-'):
			case (substr($class, 0, 7) == 'fab fa-'):
			case (substr($class, 0, 7) == 'fas fa-'):
				document::$head_tags['fontawesome5'] = '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">';
				return '<i class="'. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Foundation
			case (substr($class, 0, 3) == 'fi-'):
				document::$head_tags['foundation-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/foundation-icons/latest/foundation-icons.min.css">';
				return '<i class="'. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Glyphicon
			case (substr($class, 0, 10) == 'glyphicon-'):
				//document::$head_tags['glyphicon'] = '<link rel="stylesheet" href="'/path/to/glyphicon.min.css">'; // Not embedded in release
				return '<span class="glyphicon '. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></span>';

			// Ion Icons
			case (substr($class, 0, 4) == 'ion-'):
				document::$head_tags['ionicons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/ionicons/latest/css/ionicons.min.css">';
				return '<i class="'. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Material Design Icons
			case (substr($class, 0, 4) == 'mdi-'):
				document::$head_tags['material-design-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">';
				return '<i class="mdi '. $class .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';
		}

		switch ($class) {
			case 'add':         return draw_fonticon('fa-plus');
			case 'cancel':      return draw_fonticon('fa-times');
			case 'company':     return draw_fonticon('fa-building', 'style="color: #888;"');
			case 'download':    return draw_fonticon('fa-download');
			case 'edit':        return draw_fonticon('fa-pencil');
			case 'fail':        return draw_fonticon('fa-times', 'style="color: #c00;"');
			case 'false':       return draw_fonticon('fa-times', 'style="color: #c00;"');
			case 'female':      return draw_fonticon('fa-female', 'style="color: #e77be9;"');
			case 'folder':      return draw_fonticon('fa-folder', 'style="color: #cc6;"');
			case 'folder-open': return draw_fonticon('fa-folder-open', 'style="color: #cc6;"');
			case 'group':       return draw_fonticon('fa-group', 'style="color: #888;"');
			case 'remove':      return draw_fonticon('fa-times', 'style="color: #c33;"');
			case 'delete':      return draw_fonticon('fa-trash-o');
			case 'male':        return draw_fonticon('fa-male', 'style="color: #0a94c3;"');
			case 'move-up':     return draw_fonticon('fa-arrow-up', 'style="color: #39c;"');
			case 'move-down':   return draw_fonticon('fa-arrow-down', 'style="color: #39c;"');
			case 'ok':          return draw_fonticon('fa-check', 'style="color: #8c4;"');
			case 'on':          return draw_fonticon('fa-circle', 'style="color: #8c4;"');
			case 'off':         return draw_fonticon('fa-circle', 'style="color: #f64;"');
			case 'secure':      return draw_fonticon('fa-lock');
			case 'semi-off':    return draw_fonticon('fa-circle', 'style="color: #ded90f;"');
			case 'save':        return draw_fonticon('fa-floppy-o');
			case 'send':        return draw_fonticon('fa-paper-plane');
			case 'true':        return draw_fonticon('fa-check', 'style="color: #8c4;"');
			case 'user':        return draw_fonticon('fa-user', 'style="color: #888;"');
			case 'warning':     return draw_fonticon('fa-exclamation-triangle', 'style="color: #c00;"');
			default: trigger_error('Unknown font icon ('. $class .')', E_USER_WARNING); return;
		}
	}

	function draw_image($image, $width=null, $height=null, $clipping='fit', $parameters='') {

		if ($width && $height) {
			if (preg_match('#style="#', $parameters)) {
				$parameters = preg_replace('#style="(.*?)"#', 'style="$1 aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"', $parameters);
			} else {
				$parameters .= ' style="aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"';
			}
		}

		return '<img '. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="'. functions::escape_attr($clipping) .'"' : '') .' src="'. document::href_rlink($image) .'" '. ($parameters ? ' '. $parameters : '') .'>';
	}

	function draw_image_thumbnail($image, $width=0, $height=0, $clipping='fit', $parameters='') {

		$thumbnail = functions::image_thumbnail($image, $width, $height, $clipping);
		$thumbnail_2x = functions::image_thumbnail($image, $width*2, $height*2, $clipping);

		if ($width && $height) {
			if (preg_match('#style="#', $parameters)) {
				$parameters = preg_replace('#style="(.*?)"#', 'style="$1 aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"', $parameters);
			} else {
				$parameters .= ' style="aspect-ratio: '. functions::image_aspect_ratio($width, $height) .';"';
			}
		}

		return '<img '. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="'. functions::escape_attr($clipping) .'"' : '') .' src="'. document::href_rlink($thumbnail) .'" srcset="'. document::href_rlink($thumbnail) .' 1x, '. document::href_rlink($thumbnail_2x) .' 2x"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function draw_lightbox($selector='', $parameters=[]) {

		document::$head_tags['featherlight'] = '<link rel="stylesheet" href="'. document::href_rlink('app://assets/featherlight/featherlight.min.css') .'">';
		document::$foot_tags['featherlight'] = '<script src="'. document::href_rlink('app://assets/featherlight/featherlight.min.js') .'"></script>';
		document::$javascript['featherlight'] = implode(PHP_EOL, [
			'$.featherlight.autoBind = \'[data-toggle="lightbox"]\';',
			'$.featherlight.defaults.loading = \'<div class="loader" style="width: 128px; height: 128px; opacity: 0.5;"></div>\';',
			'$.featherlight.defaults.closeIcon = \'&#x2716;\';',
			'$.featherlight.defaults.targetAttr = \'data-target\';',
		]);

		$selector = str_replace("'", '"', $selector);

		if (empty($selector)) return;

		if (preg_match('#^(https?:)?//#', $selector)) {
			$js = ['$.featherlight(\''. $selector .'\', {'];
		} else {
			$js = ['$(\''. $selector .'\').featherlight({'];
		}

		foreach ($parameters as $key => $value) {
			switch (gettype($parameters[$key])) {

				case 'NULL':
					$js[] = '  '. $key .': null,';
					break;

				case 'boolean':
					$js[] = '  '. $key .': '. ($value ? 'true' : 'false') .',';
					break;

				case 'integer':
					$js[] = '  '. $key .': '. $value .',';
					break;

				case 'string':
					if (preg_match('#^\s*function\s*\(#', $value)) {
						$js[] = '  '. $key .': '. $value .',';
					} else {
						$js[] = '  '. $key .': "'. addslashes($value) .'",';
					}
					break;

				case 'array':
					$js[] = '  '. $key .': ["'. implode('", "', $value) .'"],';
					break;
			}
		}

		$js[] = '});';

		document::$javascript['featherlight-'.$selector] = implode(PHP_EOL, $js);
	}

	function draw_pagination($pages) {

		$pages = ceil($pages);

		if ($pages < 2) return false;

		if (!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1) {
			 $_GET['page'] = 1;
		}

		if ($_GET['page'] > 1) {
			document::$head_tags['prev'] = '<link rel="prev" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']-1]) .'">';
		}

		if ($_GET['page'] < $pages) {
			document::$head_tags['next'] = '<link rel="next" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]) .'">';
		}

		if ($_GET['page'] < $pages) {
			document::$head_tags['prerender'] = '<link rel="prerender" href="'. document::href_link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]) .'">';
		}

		$pagination = new ent_view('app://frontend/template/partials/pagination.inc.php');

		$pagination->snippets['items'][] = [
			'page' => $_GET['page']-1,
			'title' => language::translate('title_previous', 'Previous'),
			'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']-1]),
			'disabled' => ($_GET['page'] <= 1),
			'active' => false,
		];

		for ($i=1; $i<=$pages; $i++) {

			if ($i < $pages-5) {
				if ($i > 1 && $i < $_GET['page'] - 1 && $_GET['page'] > 4) {
					$rewind = round(($_GET['page'] - 1) / 2);
					$pagination->snippets['items'][] = [
						'page' => $rewind,
						'title' => ($rewind == $_GET['page']-2) ? $rewind : '...',
						'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $rewind]),
						'disabled' => false,
						'active' => false,
					];
					$i = $_GET['page'] - 1;
					if ($i > $pages-4) $i = $pages-4;
				}
			}

			if ($i > 5) {
				if ($i > $_GET['page'] + 1 && $i < $pages) {
					$forward = round(($_GET['page']+1+$pages)/2);
					$pagination->snippets['items'][] = [
						'page' => $forward,
						'title' => ($forward == $_GET['page']+2) ? $forward : '...',
						'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $forward]),
						'disabled' => false,
						'active' => false,
					];
					$i = $pages;
				}
			}

			$pagination->snippets['items'][] = [
				'page' => $i,
				'title' => $i,
				'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $i]),
				'disabled' => false,
				'active' => ($i == $_GET['page']),
			];
		}

		$pagination->snippets['items'][] = [
			'page' => $_GET['page']+1,
			'title' => language::translate('title_next', 'Next'),
			'link' => document::link($_SERVER['REQUEST_URI'], ['page' => $_GET['page']+1]),
			'disabled' => ($_GET['page'] >= $pages) ? true : false,
			'active' => false,
		];

		return (string)$pagination;
	}
