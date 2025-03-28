<?php

	#[AllowDynamicProperties]
	class job_email_deliverer extends abs_module {
		public $id = __CLASS__;
		public $name = 'Email Deliverer';
		public $description = 'Deliver emails scheduled for delivery.';
		public $author = 'LiteCore Dev Team';
		public $version = '1.0';
		public $website = 'https://litecore.dev/';
		public $priority = 0;

		public function process($force, $last_run) {

			if (!$force) {
				if (!$this->settings['status']) return;

				if ($this->settings['working_hours']) {
					list($from_time, $to_time) = explode('-', $this->settings['working_hours']);
					if (time() < strtotime("Today $from_time") || time() > strtotime("Today $to_time")) return;
				}

				if (strtotime($last_run) > functions::datetime_last_by_interval($this->settings['frequency'], $last_run)) return;
			}

			$emails = database::query(
				"select * from ". DB_TABLE_PREFIX ."emails
				where status = 'scheduled'
				and date_scheduled < '". date('Y-m-d H:i:s') ."'
				order by date_scheduled, id
				limit ". (int)$this->settings['delivery_limit'] .";"
			)->fetch_all();

			if (!$emails) {
				echo 'No queued emails ready to send' . PHP_EOL;
				return;
			}

			foreach ($emails as $email) {
				$email = new ent_email($email['id']);

				echo 'Delivering email to '. implode(', ', array_column($email->data['recipients'], 'email'));

				if ($email->send()) {
					echo ' [OK]' . PHP_EOL;
				} else {
					echo ' [Failed]' . PHP_EOL;
					continue;
				}
			}
		}

		function settings() {

			return [
				[
					'key' => 'status',
					'default_value' => '1',
					'title' => language::translate(__CLASS__.':title_status', 'Status'),
					'description' => language::translate(__CLASS__.':description_status', 'Enables or disables the module.'),
					'function' => 'toggle("e/d")',
				],
				[
					'key' => 'frequency',
					'default_value' => 'Weekly',
					'title' => language::translate(__CLASS__.':title_frequency', 'Frequency'),
					'description' => language::translate(__CLASS__.':description_frequency', 'How often the job should be executed.'),
					'function' => 'radio("5 Min")',
				],
				[
					'key' => 'working_hours',
					'default_value' => '07:00-21:00',
					'title' => language::translate(__CLASS__.':title_working_hours', 'Working Hours'),
					'description' => language::translate(__CLASS__.':description_working_hours', 'During what hours of the day the job would operate e.g. 07:00-21:00.'),
					'function' => 'text()',
				],
				[
					'key' => 'delivery_limit',
					'default_value' => '100',
					'title' => language::translate(__CLASS__.':title_delivery_limit', 'Delivery Limit'),
					'description' => language::translate(__CLASS__.':description_delivery_limit', 'The maximum amount of emails to be delivered at each launch of the process.'),
					'function' => 'number()',
				],
				[
					'key' => 'priority',
					'default_value' => '0',
					'title' => language::translate(__CLASS__.':title_priority', 'Priority'),
					'description' => language::translate(__CLASS__.':description_priority', 'Process this module in the given priority order.'),
					'function' => 'number()',
				],
			];
		}

		public function install() {
		}

		public function uninstall() {
		}
	}
