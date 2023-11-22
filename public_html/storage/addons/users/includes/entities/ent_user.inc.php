<?php

	class ent_user {
		public $data;
		public $previous;

		public function __construct($user_id=null) {

			if (!empty($user_id)) {
				$this->load($user_id);
			} else {
				$this->reset();
			}
		}

		public function reset() {

			$this->data = [];

			$fields_query = database::query(
				"show fields from ". DB_TABLE_PREFIX ."users;"
			);

			while ($field = database::fetch($fields_query)) {
				$this->data[$field['Field']] = database::create_variable($field);
			}

			$this->data['status'] = 1;
			$this->data['newsletter'] = '';

			$this->previous = $this->data;
		}

		public function load($user_id) {

			if (!preg_match('#(^[0-9]+$|@)#', $user_id)) {
				throw new Exception('Invalid user (ID: '. $user_id .')');
			}

			$this->reset();

			$user = database::query(
				"select * from ". DB_TABLE_PREFIX ."users
				". (preg_match('#^[0-9]+$#', $user_id) ? "where id = '". (int)$user_id ."'" : "") ."
				". (preg_match('#@#', $user_id) ? "where lower(email) = '". database::input(strtolower($user_id)) ."'" : "") ."
				limit 1;"
			)->fetch();

			if ($user) {
				$this->data = array_replace($this->data, array_intersect_key($user, $this->data));
			} else {
				throw new Exception('Could not find user (ID: '. (int)$user_id .') in database.');
			}

			$newsletter_recipient_query = database::query(
				"select id from ". DB_TABLE_PREFIX ."newsletter_recipients
				where email = '". database::input($this->data['email']) ."'
				limit 1;"
			);

			if (database::num_rows($newsletter_recipient_query)) {
				$this->data['newsletter'] = 1;
			} else {
				$this->data['newsletter'] = 0;
			}

			$this->previous = $this->data;
		}

		public function save() {

			if (empty($this->data['id'])) {
				database::query(
					"insert into ". DB_TABLE_PREFIX ."users
					(email, date_created)
					values ('". database::input($this->data['email']) ."', '". ($this->data['date_created'] = date('Y-m-d H:i:s')) ."');"
				);

				$this->data['id'] = database::insert_id();
			}

			database::query(
				"update ". DB_TABLE_PREFIX ."users
				set status = '". (!empty($this->data['status']) ? '1' : '0') ."',
					email = '". database::input(strtolower($this->data['email'])) ."',
					tax_id = '". database::input($this->data['tax_id']) ."',
					company = '". database::input($this->data['company']) ."',
					firstname = '". database::input($this->data['firstname']) ."',
					lastname = '". database::input($this->data['lastname']) ."',
					address1 = '". database::input($this->data['address1']) ."',
					address2 = '". database::input($this->data['address2']) ."',
					postcode = '". database::input($this->data['postcode']) ."',
					city = '". database::input($this->data['city']) ."',
					country_code = '". database::input($this->data['country_code']) ."',
					zone_code = '". database::input($this->data['zone_code']) ."',
					phone = '". database::input($this->data['phone']) ."',
					notes = '". database::input($this->data['notes']) ."',
					password_reset_token = '". database::input($this->data['password_reset_token']) ."',
					date_blocked_until = ". (!empty($this->data['date_blocked_until']) ? "'". database::input($this->data['date_blocked_until']) ."'" : "NULL") .",
					date_expire_sessions = ". (!empty($this->data['date_expire_sessions']) ? "'". database::input($this->data['date_expire_sessions']) ."'" : "NULL") .",
					date_updated = '". ($this->data['date_updated'] = date('Y-m-d H:i:s')) ."'
				where id = ". (int)$this->data['id'] ."
				limit 1;"
			);

			if (!empty($this->previous['email']) && $this->previous['email'] != $this->data['email']) {
				database::query(
					"update ". DB_TABLE_PREFIX ."newsletter_recipients
					set email = '". database::input(strtolower($this->data['email'])) ."',
						firstname = '". database::input($this->data['firstname']) ."',
						lastname = '". database::input($this->data['lastname']) ."'
					where lower(email) = '". database::input(strtolower($this->previous['email'])) ."';"
				);
			}

			if (!empty($this->data['newsletter'])) {
				database::query(
					"insert ignore into ". DB_TABLE_PREFIX ."newsletter_recipients
					(email, firstname, lastname, client_ip, date_created)
					values ('". database::input(strtolower($this->data['email'])) ."', '". database::input($this->data['firstname']) ."', '". database::input($this->data['lastname']) ."', '". database::input($_SERVER['REMOTE_ADDR']) ."', '". date('Y-m-d H:i:s') ."');"
				);
			} else if (!empty($this->previous['id'])) {
				database::query(
					"delete from ". DB_TABLE_PREFIX ."newsletter_recipients
					where lower(email) = '". database::input(strtolower($this->data['email'])) ."';"
				);
			}

			$this->previous = $this->data;

			cache::clear_cache('users');
		}

		public function set_password($password) {

			if (empty($this->data['id'])) {
				$this->save();
			}

			database::query(
				"update ". DB_TABLE_PREFIX ."users
				set password_hash = '". database::input($this->data['password_hash'] = password_hash($password, PASSWORD_DEFAULT)) ."'
				where id = ". (int)$this->data['id'] ."
				limit 1;"
			);

			$this->previous['password_hash'] = $this->data['password_hash'];
		}

		public function delete() {

			database::query(
				"delete u, nr
				from ". DB_TABLE_PREFIX ."users u
				left join ". DB_TABLE_PREFIX ."newsletter_recipients nr on (nr.email = u.email)
				where u.id = ". (int)$this->data['id'] .";"
			);

			$this->reset();

			cache::clear_cache('users');
		}
	}
