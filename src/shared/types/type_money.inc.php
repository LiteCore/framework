<?php

	class type_money {

		public $value;
		public $currency_code;

		function __construct($amount, $currency_code=null, $currency_value=null) {

			if (!$currency_code) {
				$currency_code = settings::get('site_currency_code');
			}

			if ($currency_value === null) {

				if (empty(currency::$currencies[$currency_code])) {
					trigger_error('Cannot instantiate money object as currency code '. $currency_code .' is not defined', E_USER_WARNING);
					$currency_value = 1;
				}

				$currency_value = currency::$currencies[$currency_code]['value'];
			}

			$this->value = (float)$amount;
			$this->currency_code = $currency_code;
		}

		function __toString(){
			return $this->format_raw();
		}

		function convert($currency_code = null) {

			if (!$currency_code) {
				$currency_code = $this->currency_code;
			}

			return currency::convert($this->value, $this->currency_code, $currency_code);
		}

		function format($currency_code = null, $decimals='auto') {

			if (!$currency_code) {
				$currency_code = currency::$selected['code'];
			}

			$amount = $this->convert($currency_code);

			return currency::format($amount, $decimals, $currency_code);
		}

		function format_html($currency_code = null, $decimals='auto') {

			if (!$currency_code) {
				$currency_code = currency::$selected['code'];
			}

			$amount = $this->convert($currency_code);

			return currency::format_html($amount, $decimals, $currency_code);
		}

		function format_raw($currency_code=null) {

			if (!$currency_code) {
				$currency_code = currency::$selected['code'];
			}

			$amount = $this->convert($currency_code);

			return currency::format_raw($amount, $currency_code);
		}
	}
