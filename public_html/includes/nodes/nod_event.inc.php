<?php

	class event {

		private static $_callbacks = [];
		private static $_fired_events = [];
		private static $_ongoing = null;

		public static function register($event, $callback) {

			if (in_array($event, self::$_fired_events) || self::$_ongoing === $event) {
				call_user_func_array($callback, array_slice(func_get_args(), 2));
				return;
			}

			self::$_callbacks[$event][] = $callback;
		}

		public static function fire($event) {

			if (empty(self::$_callbacks[$event])) return;

			if (in_array($event, self::$_fired_events)) {
				trigger_error("Event already fired ($event)", E_USER_WARNING);
				return;
			}

			self::$_ongoing = $event;
			self::$_fired_events[] = $event;

			$args = array_slice(func_get_args(), 1);

			foreach (self::$_callbacks[$event] as $callback) {
				call_user_func_array($callback, $args);
			}

			unset(self::$_callbacks[$event]); // Free some ram

			self::$_ongoing = null;
		}
	}
