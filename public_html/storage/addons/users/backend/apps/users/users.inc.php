<?php

	if (empty($_GET['page']) || !is_numeric($_GET['page'])) {
		$_GET['page'] = 1;
	}

	if (empty($_GET['sort'])) {
		$_GET['sort'] = 'date_created';
	}

	document::$title[] = language::translate('title_users', 'Users');

	breadcrumbs::add(language::translate('title_users', 'Users'));

	if (isset($_POST['enable']) || isset($_POST['disable'])) {

		try {

			if (empty($_POST['users'])) {
				throw new Exception(language::translate('error_must_select_users', 'You must select users'));
			}

			foreach ($_POST['users'] as $user_id) {
				$user = new ent_user($user_id);
				$user->data['status'] = !empty($_POST['enable']) ? 1 : 0;
				$user->save();
			}

			notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
			header('Location: '. document::ilink());
			exit;

		} catch (Exception $e) {
			notices::add('errors', $e->getMessage());
		}
	}

	if (!empty($_POST['delete'])) {

		if (!empty($_POST['users'])) {
			foreach ($_POST['users'] as $user_id) {
				$user = new ent_user($user_id);
				$user->delete();
			}
		}

		notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
		header('Location: '. document::link());
		exit;
	}

	if (!empty($_GET['query'])) {
		$sql_find = [
			"c.id = '". database::input($_GET['query']) ."'",
			"c.email like '%". database::input($_GET['query']) ."%'",
			"c.tax_id like '%". database::input($_GET['query']) ."%'",
			"c.company like '%". database::input($_GET['query']) ."%'",
			"concat(c.firstname, ' ', c.lastname) like '%". database::input($_GET['query']) ."%'",
		];
	}

	switch($_GET['sort']) {
		case 'id':
			$sql_sort = "c.id desc";
			break;
		case 'email':
			$sql_sort = "c.email";
			break;
		case 'name':
			$sql_sort = "c.firstname, c.lastname";
			break;
		case 'company':
			$sql_sort = "c.firstname, c.lastname";
			break;
		default:
			$sql_sort = "c.date_created desc, c.id desc";
			break;
	}

// Table Rows, Total Number of Rows, Total Number of Pages
	$users = database::query(
		"select c.* from ". DB_TABLE_PREFIX ."users c
		where c.id
		". (!empty($sql_find) ? "and (". implode(" or ", $sql_find) .")" : "") ."
		order by $sql_sort;"
	)->fetch_page($_GET['page'], null, $num_rows, $num_pages);

?>
<div class="card card-app">
	<div class="card-header">
		<div class="card-title">
			<?php echo $app_icon; ?> <?php echo language::translate('title_users', 'Users'); ?>
		</div>
	</div>

	<div class="card-action">
		<?php echo functions::form_button_link(document::ilink('users/edit_user'), language::translate('title_create_new_user', 'Create New User'), '', 'add'); ?>
	</div>

	<?php echo functions::form_begin('search_form', 'get'); ?>
		<div class="card-filter">
			<div class="expandable"><?php echo functions::form_input_search('query', true, 'placeholder="'. language::translate('text_search_phrase_or_keyword', 'Search phrase or keyword') .'"'); ?></div>
			<?php echo functions::form_button('filter', language::translate('title_search', 'Search'), 'submit'); ?>
		</div>
	<?php echo functions::form_end(); ?>

	<?php echo functions::form_begin('users_form', 'post'); ?>

		<table class="table table-striped table-hover table-sortable data-table">
			<thead>
				<tr>
					<th><?php echo functions::draw_fonticon('fa-check-square-o fa-fw', 'data-toggle="checkbox-toggle"'); ?></th>
					<th></th>
					<th data-sort="id"><?php echo language::translate('title_id', 'ID'); ?></th>
					<th data-sort="email"><?php echo language::translate('title_email', 'Email'); ?></th>
					<th data-sort="name"><?php echo language::translate('title_name', 'Name'); ?></th>
					<th data-sort="company" class="main"><?php echo language::translate('title_company_name', 'Company Name'); ?></th>
					<th data-sort="date_created" class="text-center"><?php echo language::translate('title_date_registered', 'Date Registered'); ?></th>
					<th></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($users as $user) { ?>
				<tr class="<?php echo empty($user['status']) ? 'semi-transparent' : ''; ?>">
					<td><?php echo functions::form_checkbox('users[]', $user['id']); ?></td>
					<td><?php echo functions::draw_fonticon($user['status'] ? 'on' : 'off'); ?></td>
					<td><?php echo $user['id']; ?></td>
					<td><a class="link" href="<?php echo document::href_ilink(__APP__.'/edit_user', ['user_id' => $user['id']]); ?>"><?php echo $user['email']; ?></a></td>
					<td><?php echo $user['firstname'] .' '. $user['lastname']; ?></td>
					<td><?php echo $user['company']; ?></td>
					<td class="text-end"><?php echo language::strftime(language::$selected['format_datetime'], strtotime($user['date_created'])); ?></td>
					<td class="text-end"><a class="btn btn-default btn-sm" href="<?php echo document::href_ilink(__APP__.'/edit_user', ['user_id' => $user['id']]); ?>" title="<?php echo language::translate('title_edit', 'Edit'); ?>"><?php echo functions::draw_fonticon('edit'); ?></a></td>
				</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8"><?php echo language::translate('title_users', 'Users'); ?>: <?php echo language::number_format($num_rows); ?></td>
				</tr>
			</tfoot>
		</table>

		<div class="card-body">
			<fieldset id="actions">
				<legend><?php echo language::translate('text_with_selected', 'With selected'); ?>:</legend>

				<ul class="flex flex-columns flex-gap">
					<li>
					<div class="btn-group">
						<?php echo functions::form_button('enable', language::translate('title_enable', 'Enable'), 'submit', '', 'on'); ?>
						<?php echo functions::form_button('disable', language::translate('title_disable', 'Disable'), 'submit', '', 'off'); ?>
					</div>
					</li>
					<li><?php echo functions::form_button('delete', language::translate('title_delete', 'Delete'), 'submit', 'formnovalidate class="btn btn-danger" onclick="if (!confirm(\''. language::translate('text_are_you_sure', 'Are you sure?') .'\')) return false;"', 'delete'); ?></li>
				</ul>
			</fieldset>
		</div>

	<?php echo functions::form_end(); ?>

	<?php if ($num_pages > 1) { ?>
	<div class="card-footer">
		<?php echo functions::draw_pagination($num_pages); ?>
	</div>
	<?php } ?>
</div>

<script>
	$('.data-table :checkbox').change(function() {
		$('#actions').prop('disabled', !$('.data-table :checked').length);
	}).first().trigger('change');
</script>
