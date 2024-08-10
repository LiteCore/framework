<?php

	class ref_module extends abs_reference_entity {

		protected $_data = [];
		protected $_object = null;

		function __construct($module_id) {

			$this->_data['module_id'] = (int)$module_id;

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
				$object->settings[$setting['key']] = fallback($settings[$setting['key']], $setting['default_value']);
			}

			$object->status = (isset($object->settings['status']) && in_array(strtolower($object->settings['status']), ['1', 'active', 'enabled', 'on', 'true', 'yes'])) ? 1 : 0;
			$object->priority = isset($object->settings['priority']) ? (int)$object->settings['priority'] : 0;

			if ($type == 'jobs') {
				$object->date_pushed = $module['date_pushed'];
				$object->date_processed = $module['date_processed'];
			}

			$this->_object = $object;
		}

		public function __call($name, $args) {
			return method_exists($name, $this->_object) ? call_user_func_array([$this->_object, $name], array_slice($args, 1)) : null;
		}

		protected function _load($field) {

			switch($field) {

				default:

					break;
			}
		}
	}
