<?php

	function draw_fonticon($icon, $parameters='') {

		if (!$icon) {
			return '';
		}

		switch(true) {

			// LiteCore Fonticons
			case (preg_match('#^icon-#', $icon)):
				return '<i class="'. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Bootstrap Icons
			case (preg_match('#^bi-#', $icon)):
				document::$head_tags['bootstrap-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">';
				return '<i class="bi '. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Fontawesome 4
			case (preg_match('#^fa-#', $icon)):
				trigger_error('Fontawesome 4 icon `'. functions::escape_html($icon) .'` is deprecated. Please use Fontawesome 5 instead.', E_USER_DEPRECATED);
				document::$head_tags['fontawesome4'] = '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/v4-shims.css">';
				document::$head_tags['fontawesome5'] = '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">';
				return '<i class="fa '. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Fontawesome 5
			case (preg_match('#^far fa-#', $icon)):
			case (preg_match('#^fab fa-#', $icon)):
			case (preg_match('#^fas fa-#', $icon)):
				document::$head_tags['fontawesome5'] = '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">';
				return '<i class="'. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Foundation
			case (preg_match('#^fi-#', $icon)):
				document::$head_tags['foundation-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/foundation-icons/latest/foundation-icons.min.css">';
				return '<i class="'. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Ion Icons
			case (preg_match('#^ion-#', $icon)):
				document::$head_tags['ionicons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/ionicons/latest/css/ionicons.min.css">';
				return '<i class="'. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';

			// Material Design Icons
			case (preg_match('#^mdi-#', $icon)):
				document::$head_tags['material-design-icons'] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">';
				return '<i class="mdi '. $icon .'"'. ($parameters ? ' ' . $parameters : '') .'></i>';
		}

		switch ($icon) {
			case 'add':         return draw_fonticon('icon-square-pen');
			case 'cancel':      return draw_fonticon('icon-times');
			case 'company':     return draw_fonticon('icon-building', 'style="color: #888;"');
			case 'delete':      return draw_fonticon('icon-trash');
			case 'download':    return draw_fonticon('icon-download');
			case 'edit':        return draw_fonticon('icon-pen');
			case 'failed':      return draw_fonticon('icon-times', 'style="color: #c00;"');
			case 'false':       return draw_fonticon('icon-times', 'style="color: #c00;"');
			case 'female':      return draw_fonticon('icon-female', 'style="color: #e77be9;"');
			case 'folder':      return draw_fonticon('icon-folder', 'style="color: #cc6;"');
			case 'folder-open': return draw_fonticon('icon-folder-open', 'style="color: #cc6;"');
			case 'group':       return draw_fonticon('icon-group', 'style="color: #888;"');
			case 'remove':      return draw_fonticon('icon-times', 'style="color: #c33;"');
			case 'male':        return draw_fonticon('icon-male', 'style="color: #0a94c3;"');
			case 'move-up':     return draw_fonticon('icon-arrow-up', 'style="color: #39c;"');
			case 'move-down':   return draw_fonticon('icon-arrow-down', 'style="color: #39c;"');
			case 'ok':          return draw_fonticon('icon-check', 'style="color: #8c4;"');
			case 'on':          return draw_fonticon('icon-bullet', 'style="color: #8c4;"');
			case 'off':         return draw_fonticon('icon-bullet', 'style="color: #f64;"');
			case 'print':       return draw_fonticon('icon-print', 'style="color: #ded90f;"');
			case 'remove':      return draw_fonticon('icon-times', 'style="color: #c00;"');
			case 'secure':      return draw_fonticon('icon-lock');
			case 'semi-off':    return draw_fonticon('icon-bullet', 'style="color: #ded90f;"');
			case 'save':        return draw_fonticon('icon-memory-card');
			case 'send':        return draw_fonticon('icon-paper-plane');
			case 'success':     return draw_fonticon('icon-check', 'style="color: #8c4;"');
			case 'true':        return draw_fonticon('icon-check', 'style="color: #8c4;"');
			case 'user':        return draw_fonticon('icon-user', 'style="color: #888;"');
			case 'warning':     return draw_fonticon('icon-exclamation-triangle', 'style="color: #c00;"');
			default: trigger_error('Unknown font icon ('. $icon .')', E_USER_WARNING); return;
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

	function draw_script($src) {

		if (preg_match('#^(app|storage)://#', $src)) {
			$tag = '<script defer integrity="sha256-'. base64_encode(hash_file('sha256', $src, true)) .'" src="'. document::href_rlink($src) .'"></script>';
		} else {
			$tag = '<script src="'. document::href_link($src) .'">'. $content .'</script>';
		}

		return $tag;
	}

	function draw_style($href) {

		if (preg_match('#^(app|storage)://#', $href)) {
			$tag = '<link rel="stylesheet" integrity="sha256-'. base64_encode(hash_file('sha256', $href, true)) .'" href="'. document::href_rlink($href) .'">';
		} else {
			$tag = '<link rel="stylesheet" href="'. document::href_link($href) .'">';
		}

		return $tag;
	}

	function draw_thumbnail($image, $width=0, $height=0, $clipping='fit', $parameters='') {

		if (!is_file($image)) {
			$image = 'storage://images/no_image.png';
		}

		if (!$width && !$height) {
			$entity = new ent_image($image);
			$width = $entity->width;
			$height = $entity->height;
		}

		if (!$width) {
			$aspect_ratio = (new ent_image($image))->aspect_ratio;
			list($width, $height) = functions::image_scale_by_height($height, $aspect_ratio);
		}

		if (!$height) {
			$aspect_ratio = (new ent_image($image))->aspect_ratio;
			list($width, $height) = functions::image_scale_by_width($width, $aspect_ratio);
		}

		if (empty($aspect_ratio)) {
			$aspect_ratio = functions::image_aspect_ratio($width, $height);
		}

		switch (strtolower($clipping)) {

			case '':
				$clipping = '';
				break;

			case 'fit':
				$clipping = 'fit';
				break;

			case 'crop':
				$clipping = 'crop';
				break;

			default:
				trigger_error('Invalid clipping mode ('. $clipping .')', E_USER_WARNING);
				break;
		}

		$thumbnail = functions::image_thumbnail($image, $width, $height, $clipping);
		$thumbnail_2x = functions::image_thumbnail($image, $width*2, $height*2, $clipping);

		if ($width && $height) {
			if (preg_match('#style="#', $parameters)) {
				$parameters = preg_replace('#style="(.*?)"#', 'style="$1 aspect-ratio: '. $aspect_ratio .';"', $parameters);
			} else {
				$parameters .= ' style="aspect-ratio: '. $aspect_ratio .';"';
			}
		}

		return '<img '. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="thumbnail '. functions::escape_attr($clipping) .'"' : '') .' src="'. document::href_rlink($thumbnail) .'" srcset="'. document::href_rlink($thumbnail) .' 1x, '. document::href_rlink($thumbnail_2x) .' 2x"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function draw_lightbox($selector='', $parameters=[]) {

		if (!$selector && !$parameters) return;

		if (preg_match('#^(https?:)?//#', $selector)) {
			$js = ['$.litebox(\''. $selector .'\', {'];

		} else if ($selector) {
			$js = ['$(\''. $selector .'\').litebox({'];

		} else {
			$js = ['$.litebox({'];
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

		document::add_script($js, 'litebox-'. $selector);
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
