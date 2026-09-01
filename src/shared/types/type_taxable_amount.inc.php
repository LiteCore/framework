<?php

	class datatype_taxable_amount {

		public $amount;
		public $tax_class_id;
		public $currency_code;

		public function __construct ($amount, $tax_class_id = null, $currency_code = null) {
			$this->amount = $amount;
			$this->tax_class_id = $tax_class_id;
			$this->currency_code = $currency_code;
		}

		function convert($currency_code) {
			$this->amount = $this->amount * currency::$currencies[$this->currency_code] / currency::$currencies[$currency_code];
		}

		function net($customer = null) {

			if (!$customer) {
				$customer = customer::$data;
			}

			return customer::format_raw($this->amount, $currency_code);
		}

		function gross($customer = null) {

			if (!$customer) {
				$customer = customer::$data;
			}

			return customer::format_raw($this->amount, $currency_code);
		}
	}
