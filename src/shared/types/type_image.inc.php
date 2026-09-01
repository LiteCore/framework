<?php

	class type_image {
		private $_data;

		public function __construct(string $src) {
			$this->_data['src'] = $src;
		}

		public function __toString(): string {
			return 'img';
		}
	}
