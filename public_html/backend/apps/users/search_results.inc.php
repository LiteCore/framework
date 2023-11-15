<?php

	$result = [
		'name' => language::translate('title_users', 'Users'),
		'results' => [],
	];

	$users_query = database::query(
		"select id, concat(firstname, ' ', lastname) as name, email,
		(
			if(id = '". database::input($query) ."', 10, 0)
			+ if(email like '%". database::input($query) ."%', 5, 0)
			+ if(tax_id like '%". database::input($query) ."%', 5, 0)
			+ if(concat(company, ' ', firstname, ' ', lastname, ' ', address1, ' ', address2, ' ', postcode, ' ', city) like '%". database::input($query) ."%', 5, 0)
		) as relevance
		from ". DB_TABLE_PREFIX ."users
		having relevance > 0
		order by relevance desc, id desc
		limit 5;"
	);

	if (!database::num_rows($users_query)) return;

	while ($user = database::fetch($users_query)) {
		$result['results'][] = [
			'id' => $user['id'],
			'title' => $user['name'],
			'description' => $user['email'],
			'link' => document::ilink('users/edit_user', ['user_id' => $user['id']]),
		];
	}

	return [$result];
