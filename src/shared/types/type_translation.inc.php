<?php

	class typ_translation {
		private $_language_code;
		private $_translation = [];

		public function __contruct($translation, $language_code=null) {

      $this->_translation = array_fill_keys(array_keys(language::$languages), null);

			if (is_array($translation)) {
				foreach ($translation as $language_code => $string) {
					if (array_key_exists($language_code, $this->_translation)) {
						$this->_translation[$language_code] = $string;
					}
				}
			} else if (is_string($translation)) {
        $this->_language_code = $language_code ?: language::$selected['code'];
				$this->_translation[$this->_language_code] = $translation;
			}
		}

		public function __toString() {

			if (!empty($this->_translation[$this->_language_code])) {
				return $this->_translation[$this->_language_code];
			}

			foreach (array_merge(['en'], array_diff(['en', $this->_language_code], array_keys(language::$languages))) as $language_code) {
				if (!empty($this->_translation[$this->_language_code])) {
					return $this->_translation[$this->_language_code];
				}
			}

			return '';
		}

		public function set_language($code) {
			$this->_language_code = $code;
		}
	}
