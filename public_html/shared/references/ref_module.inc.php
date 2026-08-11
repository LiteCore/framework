<?php

	// This class provides a reference wrapper for modules
	class ref_module extends abs_reference_entity {

		protected $_data = [];
		protected $_object = null;

		// Constructor initializes the module with it's ID and loads it's settings
		function __construct(string $module_id) {

			$this->_data['module_id'] = $module_id;

			$module = database::query(
				"select * from ". DB_TABLE_PREFIX ."modules
				where module_id = '". database::input($module_id) ."';"
			)->fetch();

			if (!$module) return;

			// Create object
			$object = new $module['module_id'];
			$object->id = $module['module_id']; // Set ID

			// Decode settings
			$settings = json_decode($module['settings'], true);

			// Set settings to object
			$object->settings = [];
			foreach ($object->settings() as $setting) {
				$setting['key'] = rtrim($setting['key'], '[]');
				$object->settings[$setting['key']] = $settings[$setting['key']] ?? $setting['default_value'];
			}

			$object->status = (isset($object->settings['status']) && in_array(strtolower($object->settings['status']), ['1', 'active', 'enabled', 'on', 'true', 'yes'])) ? 1 : 0;
			$object->priority = (int)($object->settings['priority'] ?? 0);

			if ($type == 'jobs') {
				$object->last_pushed = $module['last_pushed'];
				$object->last_processed = $module['last_processed'];
			}

			$this->_object = $object;
		}

		// Magic method to call methods dynamically on the module object.
		public function __call(string $name, array $args) {
			return method_exists($this->_object, $name) ? call_user_func_array([$this->_object, $name], $args) : null;
		}

		// Protected method to load specific fields dynamically.
		protected function _load(string $field): void {

			switch($field) {

				default:

					break;
			}
		}
	}
