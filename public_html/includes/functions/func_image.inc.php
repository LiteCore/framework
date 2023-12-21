<?php

	function image_scale_by_width($width, $ratio) {
		list($x, $y) = explode(':', $ratio);
		return [$width, round($width / $x * $y)];
	}

	function image_process($source, $options) {

		try {

			$source = str_replace('\\', '/', functions::file_realpath($source));
			$source = preg_replace('#^'. preg_quote(FS_DIR_STORAGE, '#') .'#', 'storage://', $source);
			$source = preg_replace('#^'. preg_quote(FS_DIR_APP, '#') .'#', 'app://', $source);

			if (!is_file($source)) {
				trigger_error('Could not find source image ('. $source .')', E_USER_WARNING);
				$source = 'storage://images/no_image.png';
			}

			$options = [
				'destination' => fallback($options['destination'], 'storage://cache/'),
				'width' => fallback($options['width'], 0),
				'height' => fallback($options['height'], 0),
				'clipping' => fallback($options['clipping'], 'fit_only_bigger'),
				'quality' => fallback($options['quality'], settings::get('image_quality')),
				'trim' => fallback($options['trim'], false),
				'interlaced' => !empty($options['interlaced']),
				'overwrite' => fallback($options['overwrite'], false),
				'watermark' => fallback($options['watermark'], false),
				'extension' => fallback($options['extension']),
			];

			// If destination is a folder
			if (is_dir($options['destination']) || substr($options['destination'], -1) == '/') {

				// If destination is a cache folder without filename
				if (preg_match('#^'. preg_quote('storage://cache/', '#') .'$#', $options['destination'])) {

					if (!$options['extension']) {
						if (settings::get('avif_enabled') && isset($_SERVER['HTTP_ACCEPT']) && preg_match('#image/avif#', $_SERVER['HTTP_ACCEPT'])) {
							$options['extension'] = 'avif';

						} else if (settings::get('webp_enabled') && isset($_SERVER['HTTP_ACCEPT']) && preg_match('#image/webp#', $_SERVER['HTTP_ACCEPT'])) {
							$options['extension'] = 'webp';

						} else {
							$options['extension'] = pathinfo($source, PATHINFO_EXTENSION);
						}
					}

					switch (strtolower($options['clipping'])) {

						case 'crop':
							$clipping_filename_flag = '_c';
							break;

						case 'crop_only_bigger':
							$clipping_filename_flag = '_cob';
							break;

						case 'stretch':
							$clipping_filename_flag = '_s';
							break;

						case 'fit':
							$clipping_filename_flag = '_f';
							break;

						case 'fit_use_whitespacing':
							$clipping_filename_flag = '_fwb';
							break;

						case 'fit_only_bigger':
							$clipping_filename_flag = '_fob';
							break;

						case 'fit_only_bigger_use_whitespacing':
							$clipping_filename_flag = '_fobws';
							break;

						default:
							trigger_error("Unknown image clipping method ($clipping)", E_USER_WARNING);
							return;
					}

					$filename = implode([
						sha1($source),
						$options['trim'] ? '_t' : '',
						($options['width'] && $options['height']) ? '_'.(int)$options['width'] .'x'. (int)$options['height'] : '',
						$clipping_filename_flag,
						$options['watermark'] ? '_wm' : '',
						settings::get('image_thumbnail_interlaced') ? '_i' : '',
						'.'.$options['extension'],
					]);

					$options['destination'] = 'storage://cache/'. substr($filename, 0, 2) . '/' . $filename;

				} else {
					$options['destination'] = rtrim($options['destination'], '/') .'/'. basename($source);
				}
			}

			// Who uses GIF these days?
			$options['destination'] = preg_replace('#\.gif$#', '.png', $options['destination']);

			// Return an already existing file
			if (is_file($options['destination'])) {
				if ($options['overwrite'] || filemtime($source) >= filemtime($options['destination'])) {
					unlink($options['destination']);
				} else {
					return $options['destination'];
				}
			} else if (!is_dir(dirname($options['destination']))) {
				if (!mkdir(dirname($options['destination']), 0777, true)) {
					trigger_error('Could not create destination folder', E_USER_WARNING);
					return false;
				}
			}

			// Process the image
			$image = new ent_image($source);

			if (!empty($options['trim'])) {
				$image->trim();
			}

			if ($options['width'] > 0 || $options['height'] > 0) {
				if (!$image->resample($options['width'], $options['height'], $options['clipping'])) return;
			}

			if (!empty($options['watermark'])) {

				if ($options['watermark'] === true) {
					$options['watermark'] = 'storage://images/logotype.png';
				}

				if (!$image->watermark($options['watermark'], 'RIGHT', 'BOTTOM')) return;
			}

			if (!$image->save($options['destination'], $options['quality'], !empty($options['interlaced']))) return;

			return $options['destination'];

		} catch (Exception $e) {
			trigger_error('Could not process image: ' . $e->getMessage(), E_USER_WARNING);
		}
	}

	function image_aspect_ratio($width, $height) {

		$ratio = [$width, $height];

		for ($x = $ratio[1]; $x > 1; $x--) {
			if (($ratio[0] % $x) == 0 && ($ratio[1] % $x) == 0) {
				$ratio = [$ratio[0] / $x, $ratio[1] / $x];
			}
		}

		return implode('/', $ratio);
	}

	function image_resample($source, $destination, $width=0, $height=0, $clipping='FIT_ONLY_BIGGER', $quality=null) {

		return image_process($source, [
			'destination' => $destination,
			'width' => $width,
			'height' => $height,
			'clipping' => $clipping,
			'trim' => false,
			'quality' => $quality,
		]);
	}

	function image_thumbnail($source, $width=0, $height=0, $clipping='fit_only_bigger', $trim=false, $extension='') {

		if (pathinfo($source, PATHINFO_EXTENSION) == 'svg') {
			return $source;
		}

		return image_process($source, [
			'width' => $width,
			'height' => $height,
			'clipping' => $clipping,
			'trim' => $trim,
			'quality' => settings::get('image_thumbnail_quality'),
			'interlaced' => settings::get('image_thumbnail_interlaced'),
			'extension' => $extension,
		]);
	}

	function image_relative_file($file) {

		$file = str_replace('\\', '/', $file);
		$file = preg_replace('#^(storage://|'. preg_quote(FS_DIR_STORAGE, '#') .')#', '', $file);
		$file = preg_replace('#^(app://|'. preg_quote(FS_DIR_APP, '#') .')#', '', $file);

		return $file;
	}

	function image_delete_cache($file) {

		$cachename = sha1(image_relative_file($file));

		functions::file_delete('storage://cache/'. substr($cache_name, 0, 2) .'/' . $cache_name .'*');
	}
