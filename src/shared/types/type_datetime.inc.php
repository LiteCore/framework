<?php

	class type_datetime implements JsonSerializable {
		private $_language_code;
		private $_timestamp;

		public function __construct(int|string|\DateTime|\DateTimeImmutable $datetime, string $timezone) {
			$this->_timestamp = is_int($datetime) ? $datetime : strtotime($datetime);
		}

		public function ago(): string {
			return f::datetime_ago($this->_timestamp);
		}

		public function adjust(string $adjustment): self {
			$this->_timestamp = strtotime($adjustment, $this->_timestamp);
			return $this;
		}

		public function format(string $format): string {
			return f::datetime($format, $this->_timestamp);
		}

		public function jsonSerialize(): mixed {
			return $this->__toString();
		}

		public function __toString(): string {
			return date('Y-m-d H:i:s', $this->_timestamp);
		}
	}
