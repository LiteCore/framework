<?php

	//! The original imagecopyresampled function is broken. This is a fixed version of it.
	/*!
	 *  \param dst_im Destination image
	 *  \param src_im Source image
	 *  \param dstX X coordinate of the top left corner of the destination area
	 *  \param dstY Y coordinate of the top left corner of the destination area
	 *  \param srcX X coordinate of the top left corner of the source area
	 *  \param srcY Y coordinate of the top left corner of the source area
	 *  \param dstW Width of the destination area
	 *  \param dstH Height of the destination area
	 *  \param srcW Width of the source area
	 *  \param srcH Height of the source area
	 */
	if (!function_exists('ImageCopyResampledFixed')) {
		function ImageCopyResampledFixed(&$dst_im, &$src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH, $whiteSpace) {
			$iSrcWidth = ImageSX($src_im);
			$iSrcHeight = ImageSY($src_im);
			$imgCropped = ImageCreateTrueColor($iSrcWidth-$srcX, $iSrcHeight-$srcY);
			ImageAlphaBlending($imgCropped, true);
			ImageFill($imgCropped, 0, 0, ImageColorAllocateAlpha($imgCropped, $whiteSpace[0], $whiteSpace[1], $whiteSpace[2], 127));
			ImageCopy($imgCropped, $src_im, 0, 0, $srcX, $srcY, $iSrcWidth-$srcX, $iSrcHeight-$srcY);
			$result = ImageCopyResampled($dst_im, $imgCropped, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			ImageDestroy($imgCropped);
			return $result;
		}
	}

	class ent_image {
		private $_data = [];
		private $_file = null;
		private $_image = null;
		private $_whitespace = [255, 255, 255];

		public function __construct($file=null, $library=null) {

			if ($file) {
				$this->set($file);
			}

			if ($library) {
				$this->_data['library'] = $library;
			}

			$this->_whitespace = preg_split('#\s*,\s*#', settings::get('image_whitespace_color'), -1, PREG_SPLIT_NO_EMPTY);
		}

		public function &__get($name) {

			if (array_key_exists($name, $this->_data)) {
				return $this->_data[$name];
			}

			$this->_data[$name] = null;

			switch ($name) {

				case 'library':

					if (extension_loaded('imagick')) {
						$this->_data['library'] = 'imagick';

					} else if (extension_loaded('gd')) {
						$this->_data['library'] = 'gd';

					} else {
						throw new Exception('No image processing library available');
					}

					break;

				case 'type':

					if (!$this->_image) {

						if (function_exists('exif_imagetype')) {
							$image_type = exif_imagetype($this->_file);
						} else {
							$params = getimagesize($this->_file);
							$image_type = $params[2];
							$this->_data['type'] = 'gif';
						}

						switch ($image_type) {
							case 1: $this->_data['type'] = 'gif'; break 2;
							case 2: $this->_data['type'] = 'jpg'; break 2;
							case 3: $this->_data['type'] = 'png'; break 2;
							case 18: $this->_data['type'] = 'webp'; break 2;
							case 19: $this->_data['type'] = 'avif'; break 2;

							case false:
								if (strpos(file_get_contents($this->_file, false, null, 0, 256), '<svg') !== false) {
									$this->_data['type'] = 'svg';
									break 2;
								}
								break;

							default:
								// Set PNG for other graphics formats
								$this->_data['type'] = 'png';
								break 2;
						}

					} else {
						$this->load();
					}

					switch ($this->library) {
						case 'imagick':
							$this->_data['type'] = strtr(strtolower($this->_image->getImageFormat()), ['jpeg' => 'jpg']);
							break 2;

						case 'gd':
							break 2;
					}

					break;

				case 'width':
				case 'height':

					switch ($this->library) {

						case 'imagick':

							// Prevent DoS attack
							Imagick::setResourceLimit(imagick::RESOURCETYPE_AREA, 24e6);
							Imagick::setResourceLimit(imagick::RESOURCETYPE_MEMORY, 512e6);
							Imagick::setResourceLimit(imagick::RESOURCETYPE_DISK, 512e6);

							if (!$this->_image) {
								$this->load();
							}

							if (!$this->_image) {
								throw new Exception('Not a valid image object');
							}

							try {
								$this->_data['width'] = $this->_image->getImageWidth();
								$this->_data['height'] = $this->_image->getImageHeight();
							} catch (\ImagickException $e) {
								throw new Exception("Error getting source image dimensions ($this->_file)");
							}

							break 2;

						case 'gd':

							if ($this->type == 'svg') {
								$this->_data['width'] = 0;
								$this->_data['height'] = 0;
								break 2;
							}

							if ($this->_image) {
								$this->_data['width'] = ImageSX($this->_image);
								$this->_data['height'] = ImageSY($this->_image);
								break 2;
							}

							list($this->_data['width'], $this->_data['height']) = GetImageSize($this->_file);
							break 2;
					}

					break;

				case 'aspect_ratio':

					$ratio = [$this->width, $this->height];

					for ($x = $ratio[1]; $x > 1; $x--) {
						if (($ratio[0] % $x) == 0 && ($ratio[1] % $x) == 0) {
							$ratio = [$ratio[0] / $x, $ratio[1] / $x];
						}
					}

					$this->data['ratio'] = implode('/', $ratio);
					break;
			}

			return $this->_data[$name];
		}

		public function &__isset($name) {
			return $this->__get($name);
		}

		public function __set($name, $value) {
			trigger_error("Setting data is prohibited ($name)", E_USER_WARNING);
		}

		public function set($file) {

			$this->_file = null;
			$this->_image = null;
			unset($this->_data['type']);
			unset($this->_data['width']);
			unset($this->_data['height']);
			unset($this->_data['aspect_ratio']);

			if (empty($file)) {
				throw new Exception('Could not set image to an empty source file');
			}

			// Handle remote images (Safe for allow_url_fopen = off)
			if (preg_match('#^https?://#', $file)) {

				$client = new http_client();
				$response = $client->call('GET', $file);

				if ($client->last_response['status_code'] != 200) {
					throw new Exception('Remote image location '. $file .' returned an unexpected http response code ('. $client->last_response['status_code'] .')');
				}

				return $this->load_from_string($response);

			} else {
				$file = functions::file_realpath($file);
			}

			if (!is_file($file)) {
				throw new Exception('Could not set image source to a non-existing source ('. $file .')');
			}

			$this->_file = $file;

			return true;
		}

		public function load($file=null) {

			if (!empty($file)) {
				$this->set($file);
			}

			unset($this->_data['type']);
			unset($this->_data['width']);
			unset($this->_data['height']);
			unset($this->_data['aspect_ratio']);

			if (empty($this->_file)) {
				throw new Exception('Could not load image from empty source location');
			}

			switch ($this->library) {

				case 'imagick':

					try {

						// Prevent DoS attack
						Imagick::setResourceLimit(imagick::RESOURCETYPE_AREA, 24e6);
						Imagick::setResourceLimit(imagick::RESOURCETYPE_MEMORY, 256e6);
						Imagick::setResourceLimit(imagick::RESOURCETYPE_DISK, 256e6);

						$this->_image = new Imagick($this->_file);
						$this->_data['type'] = strtr(strtolower($this->_image->getImageFormat()), ['jpeg' => 'jpg']);

						return true;

					} catch (\ImagickException $e) {
						throw new Exception("Error loading image ($this->_file)");
					}

					break;

				case 'gd':

					if ($this->width * $this->height > 64e6) {
						throw new Exception('Refused to load image larger than 64 Megapixels');
					}

					switch ($this->type) {

						case 'avif':
							$this->_data['type'] = 'avif';
							$this->_image = ImageCreateFromAVIF($this->_file);
							break;

						case 'gif':
							$this->_data['type'] = 'gif';
							$this->_image = ImageCreateFromGIF($this->_file);
							break;

						case 'jpg':
							$this->_data['type'] = 'jpg';
							$this->_image = ImageCreateFromJPEG($this->_file);
							break;

						case 'png':
							$this->_data['type'] = 'png';
							$this->_image = ImageCreateFromPNG($this->_file);
							break;

						case 'webp':
							$this->_data['type'] = 'webp';
							$this->_image = ImageCreateFromWebP($this->_file);
							break;

						case 'svg':
							$this->_data['type'] = 'svg';
							return false;

						default:
							throw new Exception("Cannot load unknown image type ($this->type)");
					}

					if (!$this->_image) {
						throw new Exception("Could not create resource from image ($this->_file)");
					}

					return true;
			}
		}

		public function load_from_string($binary) {

			$tmp_file = functions::file_create_tempfile($binary);

			$this->load($tmp_file);
		}

		public function resample($max_width=1024, $max_height=1024, $clipping='FIT_ONLY_BIGGER') {

			if ($max_width == 0 && $max_height == 0) return;

			if ($this->width == 0 || $this->height == 0) {
				throw new Exception('Error getting source image dimensions ('. $this->_file .').');
			}

			// Convert percentage dimensions to pixels
			if (strpos($max_width, '%')) $max_width = round($this->width * str_replace('%', '', $max_width) / 100);
			if (strpos($max_height, '%')) $max_height = round($this->height * str_replace('%', '', $max_height) / 100);

			// Calculate source proportion
			$ratio = $this->width / $this->height;

			// Complete missing target dimensions
			if ((int)$max_width == 0) $max_width = round($max_height * $ratio);
			if ((int)$max_height == 0) $max_height = round($max_width / $ratio);

			if (!$this->_image) {
				$this->load();
			}

			switch ($this->library) {

				case 'imagick':

					try {

						if ($this->type == 'svg') {
							$this->_image->scale($max_width, $max_height);
							return;
						}

						$this->_image->setImageBackgroundColor('rgba('.$this->_whitespace[0].','.$this->_whitespace[1].','.$this->_whitespace[2].',0)');

						switch (strtolower($clipping)) {

							case 'fit':
								//$result = $this->_image->scaleImage($max_width, $max_height, true);
								//return $this->_image->adaptiveResizeImage($max_width, $max_height, true);
								return $this->_image->thumbnailImage($max_width, $max_height, true);

							case 'fit_only_bigger':
								if ($this->width <= $max_width && $this->height <= $max_height) return true;
								return $this->_image->thumbnailImage($max_width, $max_height, true);

							case 'fit_use_whitespacing':
								return $this->_image->thumbnailImage($max_width, $max_height, true, true);

							case 'fit_only_bigger_use_whitespacing':
								if ($this->width <= $max_width && $this->height <= $max_height) {
									$_newimage = new Imagick();
									$_newimage->newImage($max_width, $max_height, 'rgba('.$this->_whitespace[0].','.$this->_whitespace[1].','.$this->_whitespace[2].',0)');
									$offset_x = round(($max_width - $this->width) / 2);
									$offset_y = round(($max_height - $this->height) / 2);
									$result = $_newimage->compositeImage($this->_image, Imagick::COMPOSITE_OVER, $offset_x, $offset_y);
									$this->_image = $_newimage;
									return $result;
								}

								return $this->_image->thumbnailImage($max_width, $max_height, true, true);

							case 'crop':

								return $this->_image->cropThumbnailImage($max_width, $max_height);

							case 'crop_only_bigger':
								if ($this->width <= $max_width && $this->height <= $max_height) return true;
								return $this->_image->cropThumbnailImage($max_width, $max_height);

							case 'stretch':
								return $this->_image->thumbnailImage($max_width, $max_height, false); // Stretch

							default:
								throw new Exception('Unknown clipping method ($clipping)');
						}

					} catch (Exception $e) {
						throw new Exception('Error: Could not resample image: '. $e->getMessage());
					}

					break;

				case 'gd':

					if ($this->type == 'svg') return false;

					switch (strtolower($clipping)) {

						case 'crop':
						case 'crop_only_bigger':

							// Calculate dimensions
							$new_width = $max_width;
							$new_height = $max_height;

							if (strtoupper($clipping) == 'CROP_ONLY_BIGGER') {
								if ($this->width < $new_width) {
									$new_width = $this->width;
								}
								if ($this->height < $new_height) {
									$new_height = $this->height;
								}
							}

							// Create output image container
							$_resized = ImageCreateTrueColor($new_width, $new_height);

							// Calculate destination dimensional ratio
							$destination_ratio = $new_width / $new_height;

							ImageAlphaBlending($_resized, true);
							ImageSaveAlpha($_resized, true);

							ImageFill($_resized, 0, 0, ImageColorAllocateAlpha($_resized, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2], 127));

							// Perform resample
							if (($this->width / $new_width) > ($this->height / $new_height)) {
								$result = ImageCopyResampledFixed($_resized, $this->_image, 0, 0, round(($this->width - $new_width * $this->height / $new_height) / 2), 0, $new_width, $new_height, round($this->height * $destination_ratio), $this->height, $this->_whitespace);
							} else {
								$result = ImageCopyResampledFixed($_resized, $this->_image, 0, 0, 0, round(($this->height - $new_height * $this->width / $new_width) / 2), $new_width, $new_height, $this->width, round($this->width / $destination_ratio), $this->_whitespace);
							}

							break;

						case 'stretch':

							// Calculate dimensions
							$new_width = ((int)$max_width == 0) ? $this->width : $max_width;
							$new_height = ((int)$max_height == 0) ? $this->height : $max_height;

							// Create output image container
							$_resized = ImageCreateTrueColor($new_width, $new_height);

							ImageAlphaBlending($_resized, true);
							ImageSaveAlpha($_resized, true);

							ImageFill($_resized, 0, 0, ImageColorAllocateAlpha($_resized, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2], 127));

							// Perform resample
							$result = ImageCopyResampledFixed($_resized, $this->_image, round(($max_width - $new_width) / 2), round(($max_height - $new_height) / 2), 0, 0, $new_width, $new_height, $this->width, $this->height, $this->_whitespace);

							break;

						case 'fit':
						case 'fit_use_whitespacing':
						case 'fit_only_bigger':
						case 'fit_only_bigger_use_whitespacing':

							// Calculate dimensions
							$new_width = $max_width;
							$new_height = round($new_width / $ratio);
							if ($new_height > $max_height) {
								$new_height = $max_height;
								$new_width = round($new_height * $ratio);
							}

							if (in_array(strtoupper($clipping), ['FIT_ONLY_BIGGER', 'FIT_ONLY_BIGGER_USE_WHITESPACING'])) {
								if ($new_width > $new_height) {
									if ($new_width > $this->width) {
										$new_width = $this->width;
										$new_height = round($new_width / $ratio);
									}
								} else {
									if ($new_height > $this->height) {
										$new_height = $this->height;
										$new_width = round($new_height * $ratio);
									}
								}
							}

							if (in_array(strtoupper($clipping), ['FIT_USE_WHITESPACING', 'FIT_ONLY_BIGGER_USE_WHITESPACING'])) {

								// Create output image container
								$_resized = ImageCreateTrueColor($max_width, $max_height);

								ImageAlphaBlending($_resized, true);
								ImageSaveAlpha($_resized, true);

								// Fill with whitespace color
								ImageFill($_resized, 0, 0, ImageColorAllocateAlpha($_resized, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2], 127));

								// Make whitespace color transparent
								//ImageColorTransparent($_resized, ImageColorAllocate($_resized, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2]));

								// Perform resample
								$result = ImageCopyResampledFixed($_resized, $this->_image, round(($max_width - $new_width) / 2), round(($max_height - $new_height) / 2), 0, 0, $new_width, $new_height, $this->width, $this->height, $this->_whitespace);

							} else {

								// Create output image container
								$_resized = ImageCreateTrueColor($new_width, $new_height);

								ImageAlphaBlending($_resized, true);
								ImageSaveAlpha($_resized, true);

								ImageFill($_resized, 0, 0, ImageColorAllocateAlpha($_resized, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2], 127));

								// Perform resample
								$result = ImageCopyResampledFixed($_resized, $this->_image, round(($max_width - $new_width) / 2), round(($max_height - $new_height) / 2), 0, 0, $new_width, $new_height, $this->width, $this->height, $this->_whitespace);
							}

							break;

						default:
							throw new Exception('Unknown clipping method');
					}

					$this->_data['width'] = $new_width;
					$this->_data['height'] = $new_height;
					unset($this->_data['aspect_ratio']);

					ImageDestroy($this->_image);
					$this->_image = $_resized;

					return $result;
			}
		}

		public function trim() {

			if (!$this->_image) {
				$this->load();
			}

			switch ($this->library) {

				case 'imagick':

					try {

						$result = $this->_image->trimImage(0);

						$this->_data['width'] = $this->_image->getImageWidth();
						$this->_data['height'] = $this->_image->getImageHeight();
						unset($this->_data['aspect_ratio']);

						return $result;

					} catch (\ImagickException $e) {
						throw new Exception("Error trimming image ($this->_file)");
					}

					break;

				case 'gd':

					if ($this->type == 'svg') return false;

					if (!function_exists('ImageCropAuto')) { // PHP 5.5
						trigger_error('Trimming images requires Imagick or PHP 5.5+', E_USER_WARNING);
					}

					//$result = ImageCropAuto($this->_image, IMG_CROP_THRESHOLD, 100, ImageColorAt($this->_image, 0, 0));
					$result = ImageCropAuto($this->_image, IMG_CROP_SIDES);

					$this->data['width'] = ImageSX($this->_image);
					$this->data['height'] = ImageSY($this->_image);
					unset($this->_data['aspect_ratio']);

					return $result;

			}
		}

		public function watermark($watermark, $align_x='RIGHT', $align_y='BOTTOM', $margin=5) {

			if (!is_file($watermark)) {
				throw new Exception("Cannot load watermark as file is missing ($watermark)");
			}

			if (!$this->_image) {
				$this->load();
			}

			switch ($this->library) {

				case 'imagick':

					try {

						$_watermark = new imagick();
						$_watermark->readImage($watermark);

						if ($_watermark->getImageWidth() > round($this->width/5) || $_watermark->getImageHeight() > round($this->height/5)) {
							$_watermark->thumbnailImage(round($this->width/5), round($this->height/5), true);
						}

						switch (strtoupper($align_x)) {
							case 'LEFT':
								$offset_x = $margin;
								break;
							case 'CENTER':
								$offset_x = round(($this->width - $_watermark->getImageWidth()) / 2);
								break;
							case 'RIGHT':
							default:
								$offset_x = $this->width - $_watermark->getImageWidth() - $margin;
								break;
						}

						switch (strtoupper($align_y)) {
							case 'TOP':
								$offset_y = $margin;
								break;
							case 'CENTER':
							case 'MIDDLE':
								$offset_y = round(($this->height - $_watermark->getImageHeight()) / 2);
								break;
							case 'BOTTOM':
							default:
								$offset_y = $this->height - $_watermark->getImageHeight() - $margin;
								break;
						}

						return $this->_image->compositeImage($_watermark, imagick::COMPOSITE_OVER, $offset_x, $offset_y);

					} catch (\ImagickException $e) {
						throw new Exception("Error applying watermark ($watermark)");
					}

					break;

				case 'gd':

					if ($this->type == 'svg') return false;

					$_watermark = new ent_image($watermark, $this->library);

					// Return false on no image
					if (!$_watermark->type) {
						throw new Exception("Watermark file is not a valid image ($watermark)");
					}

					// Load watermark
					$_watermark->load();

					// Check if watermark is a PNG file
					if ($_watermark->type != 'png') {
						trigger_error("Watermark file is not a PNG image ($watermark)", E_USER_NOTICE);
					}

					// Shrink a large watermark
					$_watermark->resample(round($this->width/5), round($this->height/5), 'FIT_ONLY_BIGGER');

					// Align watermark and set horizontal offset
					switch (strtoupper($align_x)) {
						case 'LEFT':
							$offset_x = $margin;
							break;
						case 'CENTER':
							$offset_x = round(($this->width - $_watermark->width) / 2);
							break;
						case 'RIGHT':
						default:
							$offset_x = $this->width - $_watermark->width - $margin;
							break;
					}

					// Align watermark and set vertical offset
					switch (strtoupper($align_y)) {
						case 'TOP':
							$offset_y = $margin;
							break;
						case 'MIDDLE':
							$offset_y = round(($this->height - $_watermark->height) / 2);
							break;
						case 'BOTTOM':
						default:
							$offset_y = $this->height - $_watermark->height - $margin;
							break;
					}

					// Create the watermarked image
					$result = ImageCopy($this->_image, $_watermark->_image, $offset_x, $offset_y, 0, 0, $_watermark->width, $_watermark->height);

					// Free some RAM memory
					ImageDestroy($_watermark->_image);

					return $result;
			}
		}

		public function write($destination, $quality=90, $interlaced=false) {
			trigger_error(__CLASS__.'->write() is deprecated. Instead, use '.__CLASS__.'->save()', E_USER_DEPRECATED);
			return $this->save($destination, $quality, $interlaced);
		}

		public function save($destination='', $quality=90, $interlaced=false) {

			if (!$destination) {
				$destination = $this->_file;
			}

			if (is_file($destination)) {
				throw new Exception("Destination already exists ($destination)");
			}

			if (is_dir($destination)) {
				throw new Exception("Destination is a folder ($destination)");
			}

			if (!is_writable(dirname($destination))) {
				throw new Exception("Destination is not writable ($destination)");
			}

			$type = strtolower(pathinfo($destination, PATHINFO_EXTENSION));

			if (!$type) {
				$type = $this->type;
			}

			if (!preg_match('#^(avif|gif|jpe?g|png|webp|svg)$#i', $type)) {
				throw new Exception("Unknown image format ($type)");
			}

			if (!$this->_image) {
				$this->load();
			}

			switch ($this->library) {

				case 'imagick':

					if ($this->_image->getImageDepth() > 16) {
						$this->_image->setImageDepth(16);
					}

					if (strtolower($type) == 'jpg') {
						 $this->_image->setImageCompression(Imagick::COMPRESSION_JPEG);
					} else {
						 $this->_image->setImageCompression(Imagick::COMPRESSION_ZIP);
					}

					$this->_image->setImageCompressionQuality((int)$quality);

					if ($interlaced) {
						$this->_image->setInterlaceScheme(Imagick::INTERLACE_PLANE);
					}

					return $this->_image->writeImage($type.':'.$destination);

					break;

				case 'gd':

					if ($this->type == 'svg') {
						if ($type != 'svg') {
							throw new Exception("GD2 does not support converting svg to $type. Enable Imagick for PHP instead");
						} else {
							return copy($this->_file, $destination);
						}
					}

					$new_image = ImageCreateTrueColor($this->width, $this->height);

					ImageFill($new_image, 0, 0, ImageColorAllocateAlpha($new_image, $this->_whitespace[0], $this->_whitespace[1], $this->_whitespace[2], 127));

					ImageCopy($new_image, $this->_image, 0, 0, 0, 0, $this->width, $this->height);

					if (in_array($type, ['avif', 'png', 'webp'])) {
						ImageSaveAlpha($this->_image, true);
					}

					switch (strtolower($type)) {
						case 'avif': $result = ImageAVIF($new_image, $destination); break;
						case 'gif': $result = ImageGIF($new_image, $destination); break;
						case 'jpg': $result = ImageJPEG($new_image, $destination, (int)$quality); break;
						case 'png': $result = ImagePNG($this->_image, $destination); break;
						case 'webp': $result = ImageWebP($this->_image, $destination, (int)$quality); break;
						default: throw new Exception('Unknown output format');
					}

					if ($interlaced) {
						ImageInterlace($this->_image, true);
					}

					ImageDestroy($new_image);
					return $result;
			}
		}
	}
