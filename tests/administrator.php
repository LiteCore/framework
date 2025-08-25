<?php

	include_once '../../includes/app_header.inc.php';

	try {

		// Start a MySQL transaction so we can rollback the test
		database::query(
			"start transaction;"
		);

		// Prepare some example data
		$data = [
			'status' => 1,
			'username' => 'test',
			'email' => 'test@example.com',
			'two_factor_auth' => 1,
			'valid_from' => '2023-01-01 00:00:00',
			'valid_to' => '2023-12-31 23:59:59',
		];

		$password = '123456';

		########################################################################
		## Creating a new administrator
		########################################################################

		// Create a new entity
		$administrator = new ent_administrator();

		// Set data
		foreach ($data as $key => $value) {
			$administrator->data[$key] = $value;
		}

		$administrator->set_password('123456');

		// Save changes to database
		$administrator->save();

		// Check if the entity was created
		if (!$administrator_id = $administrator->data['id']) {
			throw new Exception('Failed to create administrator');
		}

		########################################################################
		## Load and update the administrator
		########################################################################

		// Load the entity
		$administrator = new ent_administrator($administrator_id);

		// Check if the administrator was loaded
		if ($administrator->data['id'] != $administrator_id) {
			throw new Exception('Failed to load administrator');
		}

		// Check if data was set correctly
		foreach ($data as $key => $value) {
			if ($administrator->data[$key] != $value) {
				throw new Exception('The administrator data was not stored correctly ('. $key .')');
			}
		}

		// Check if the password was stored correctly
		if (!password_verify($password, $administrator->password_hash)) {
			throw new Exception('The administrator data was not stored correctly');
		}

		########################################################################
		## Updating the administrator
		########################################################################

		// Prepare some new data
		$data = [
			'status' => 0,
			'username' => 'test2',
			'email' => 'test2@example.com',
			'two_factor_auth' => 0,
			'valid_from' => '2024-01-01 00:00:00',
			'valid_to' => '2024-12-31 23:59:59',
		];

		// Update some data
		foreach ($data as $key => $value) {
			$administrator->data[$key] = $value;
		}

		// Set a new password
		$administrator->set_password($password = '654321');

		// Save changes to database
		$administrator->save();

		// Check if data was set correctly
		foreach ($data as $key => $value) {
			if ($administrator->data[$key] != $value) {
				throw new Exception('The administrator data was not updated correctly ('. $key .')');
			}
		}

		// Check if the password was stored correctly
		if (!password_verify($password, $administrator->password_hash)) {
			throw new Exception('The administrator data was not updated correctly');
		}

		########################################################################
		## Deleting the administrator
		########################################################################

		// Delete the entity
		$administrator->delete();

		database::query(
			"rollback;"
		);

		return true;

	} catch (Exception $e) {
		database::query('rollback;');
		return false;
	}

