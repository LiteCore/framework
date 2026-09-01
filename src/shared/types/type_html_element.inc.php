<?php

	/* Example usage:
		new datatype::html_element('div', [
			'class' => 'card',
			'title' => 'Tooltip',
			'_content' => '...',
		], $content);
	*/

	class typ_html_element {
		public $element = '';
		public $attributes = [];
		public $data = [];
		public $content = null;

		public function __contruct($html) {

			$html = trim($html);

			$element = '';
			$attributes = '';
			$content = '';

			$characters = mb_str_split($html);
			$length = count($html);

			$current_buffer = '';

			// Step through characters
			for ($i=0; $i < $length; $i++) {

				switch ($current_buffer) {

					case '':
						if ($string[$i] == '<') {
							$current_buffer = 'element';
							continue;
						} else {
							throw new Exception("Unexpected character $c at position $i");
						}
						break;

					case 'element':
						if ($string[$i] == ' ') {
							$current_buffer = 'attributes';
							continue;
						} else if ($string[$i] == '>') {
							$current_buffer = 'content';
							continue;
						}
						break;

					case 'attributes':
						if ($string[$i] == '>') {
							$current_buffer = 'attributes';
							continue;
						} else if ($string[$i] == '>') {
							$current_buffer = 'content';
							continue;
						}
						break;
				}

				$current_buffer .= $string[$i];
				continue;
			}

/*
			foreach ($characters as $i => $c) {

				switch ($c) {
					case '<':

						if (preg_match('')

						$resume_position = $i + mb_strlen($this->element);

						break;

					default:
						throw new Exception("Unexpected character $c at position $i");
				}

			}
*/

			if (preg_match('#<([a-z0-9\-]+)(.*?)>(.*)</$1>#', $html, $matches)) {
				$this->element = $matches[1];
				$this->content = $matches[3];
			}

			// Extract element
			if (preg_match('#\s*<([a-z]+.*?>)#', $html, $matches)) {
				$this->element = $matches[1];
			} else {
				throw new Exception('Could not extract element');
			}

			if (preg_match('#\s</>\s#', $html, $matches)) {
				$this->element = $matches[1];
			} else {
				throw new Exception('Could not extract element');
			}

/*
			$in = '';
			for ($i=0; $i < mb_strlen($html); $i++) {

				switch ($in) {
					case 'element':

						if ($html[$i] == ' ') {
							$in = 'attribute';
						} else if ($html[$i] == '>')) {
							$in = '';
						} else {
							$this->element .= $html[$i];
						}

						break;

					default:

						if ($html[$i] == '<') {
							$in = 'element';
						} else if ($this->element) {
							$in = 'conent';
						} else {
							throw new Exception('Unknown character '. $html[$i] .' at position '. $i);
						}

						break;
				}
			}
*/
		}

		public function __toString() {

			$attributes = implode(' ', array_map(function($key, $value){
				return $key .'="'. f::escape_attr($value) .'"';
			}, array_keys($this->attributes), $this->attributes));

			$data = implode(' ', array_map(function($key, $value){
				return 'data-'. $key .'="'. f::escape_attr($value) .'"';
			}, array_keys($this->data), $this->data));

			$output = '<'.$this->element . ($attributes ? ' ' . $attributes : '') . ($data ? ' ' . $data : '') . '>'. $this->content .'</'.$this->element.'>';

			return $output;
		}

		public function append_to($element) {
			$element->append($content);
			return $this;
		}
	}
