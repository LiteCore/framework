<?php

	class ent_country {
		public $data;
		public $previous;

		public function __construct($country_code=null) {

			if (!empty($country_code)) {
				$this->load($country_code);
			} else {
				$this->reset();
			}
		}

		public function reset() {

			$this->data = [];

			database::query(
				"show fields from ". DB_TABLE_PREFIX ."countries;"
			)->each(function($field) {
				$this->data[$field['Field']] = database::create_variable($field);
			});

			$this->data['zones'] = [];

			$this->previous = $this->data;
		}

		public function load($country_code) {

			if (!preg_match('#^([0-9]+|[A-Z]{2,3}|[a-z ]{4,})$#i', $country_code)) {
				throw new Exception('Invalid country ('. $country_code .')');
			}

			$this->reset();

			$country = database::query(
				"select * from ". DB_TABLE_PREFIX ."countries
				". (preg_match('#^[0-9]+$#', $country_code) ? "where id = ". (int)$country_code : "") ."
				". (preg_match('#^[A-Z]{2}$#i', $country_code) ? "where iso_code_2 = '". database::input(strtoupper($country_code)) ."'" : "") ."
				". (preg_match('#^[A-Z]{3}$#i', $country_code) ? "where iso_code_3 = '". database::input(strtoupper($country_code)) ."'" : "") ."
				". (preg_match('#^[a-z ]{4,}$#i', $country_code) ? "where (lower(name) = '". database::input(mb_strtolower($country_code)) ."' or lower(domestic_name) = '". database::input(mb_strtolower($country_code)) ."')" : "") ."
				limit 1;"
			)->fetch();

			if ($country) {
				$this->data = array_replace($this->data, array_intersect_key($country, $this->data));
			} else {
				throw new Exception('Could not find country ('. functions::escape_html($country_code) .') in database.');
			}

			// Zones
			$this->data['zones'] = database::query(
				"select * from ". DB_TABLE_PREFIX ."zones
				where country_code = '". database::input(strtoupper($this->data['iso_code_2'])) ."'
				order by name;"
			)->fetch_all();

			$this->previous = $this->data;
		}

		public function save() {

			if (empty($this->data['status']) && $this->data['iso_code_2'] == settings::get('site_country_code')) {
				throw new Exception(language::translate('error_cannot_disable_site_country', 'You must change the site country before disabling it.'));
			}

			if (empty($this->data['status']) && $this->data['iso_code_2'] == settings::get('default_country_code')) {
				throw new Exception(language::translate('error_cannot_disable_default_country', 'You must change the default country before disabling it.'));
			}

			if (database::query(
				"select id from ". DB_TABLE_PREFIX ."countries
				where id != ". (int)$this->data['id'] ."
				and (
					iso_code_1 = '". database::input($this->data['iso_code_1']) ."'
					or iso_code_2 = '". database::input(strtoupper($this->data['iso_code_2'])) ."'
					or iso_code_2 = '". database::input(strtoupper($this->data['iso_code_3'])) ."'
				)
				limit 1;"
			)->num_rows) {
				throw new Exception(language::translate('error_language_conflict', 'The country conflicts another country in the database'));
			}

			if (!$this->data['id']) {
				database::query(
					"insert into ". DB_TABLE_PREFIX ."countries
					(date_created)
					values ('". ($this->data['date_created'] = date('Y-m-d H:i:s')) ."');"
				);
				$this->data['id'] = database::insert_id();
			}

			database::query(
				"update ". DB_TABLE_PREFIX ."countries
				set status = ". (int)$this->data['status'] .",
					iso_code_1 = '". database::input($this->data['iso_code_1']) ."',
					iso_code_2 = '". database::input(strtoupper($this->data['iso_code_2'])) ."',
					iso_code_3 = '". database::input(strtoupper($this->data['iso_code_3'])) ."',
					name = '". database::input($this->data['name']) ."',
					domestic_name = '". database::input($this->data['domestic_name']) ."',
					tax_id_format = '". database::input($this->data['tax_id_format']) ."',
					address_format = '". database::input($this->data['address_format']) ."',
					postcode_format = '". database::input($this->data['postcode_format']) ."',
					language_code = '". database::input($this->data['language_code']) ."',
					currency_code = '". database::input($this->data['currency_code']) ."',
					phone_code = '". database::input($this->data['phone_code']) ."',
					date_updated = '". ($this->data['date_updated'] = date('Y-m-d H:i:s')) ."'
				where id = ". (int)$this->data['id'] ."
				limit 1;"
			);

			// Zones

			database::query(
				"delete from ". DB_TABLE_PREFIX ."zones
				where country_code = '". database::input($this->data['iso_code_2']) ."'
				and id not in ('". implode("', '", array_column($this->data['zones'], 'id')) ."');"
			);

			if (!empty($this->data['zones'])) {
				foreach ($this->data['zones'] as $key => $zone) {
					if (empty($zone['id'])) {
						database::query(
							"insert into ". DB_TABLE_PREFIX ."zones
							(country_code, date_created)
							values ('". database::input($this->data['iso_code_2']) ."', '". date('Y-m-d H:i:s') ."');"
						);
						$zone['id'] = $this->data['zones'][$key]['id'] = database::insert_id();
					}

					database::query(
						"update ". DB_TABLE_PREFIX ."zones
						set code = '". database::input($zone['code']) ."',
							name = '". database::input($zone['name']) ."',
							date_updated = '". ($this->data['zones'][$key]['date_updated'] = date('Y-m-d H:i:s')) ."'
						where country_code = '". database::input(strtoupper($this->data['iso_code_2'])) ."'
						and id = ". (int)$zone['id'] ."
						limit 1;"
					);
				}
			}

			$this->previous = $this->data;

			cache::clear_cache('countries');
		}

		public function delete() {

			if ($this->data['code'] == settings::get('site_country_code')) {
				throw new Exception(language::translate('error_cannot_delete_site_country', 'You must change the site country before it can be deleted.'));
			}

			if ($this->data['iso_code_2'] == settings::get('default_country_code')) {
				throw new Exception(language::translate('error_cannot_delete_default_country', 'You must change the default country before it can be deleted.'));
			}

			database::query(
				"delete c, z
				from ". DB_TABLE_PREFIX ."countries c
				left join ". DB_TABLE_PREFIX ."zones z on (z.country_code = c.iso_code_2)
				where c.id = ". (int)$this->data['id'] .";"
			);

			$this->reset();

			cache::clear_cache('countries');
		}
	}
