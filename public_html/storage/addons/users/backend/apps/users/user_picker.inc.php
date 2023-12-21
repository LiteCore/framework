<?php
	document::$layout = 'ajax';
?>
<style>
#modal-user-picker tbody > tr {
	cursor: pointer;
}
</style>

<div id="modal-user-picker" class="modal fade" style="max-width: 720px; display: none;">

	<button class="set-guest btn btn-default float-end" type="button"><?php echo language::translate('text_set_as_guest', 'Set As Guest'); ?></button>

	<h2 style="margin-top: 0;"><?php echo language::translate('title_users', 'Users'); ?></h2>

	<div class="modal-body">
		<div class="form-group">
			<?php echo functions::form_input_search('query', true, 'placeholder="'. functions::escape_html(language::translate('title_search', 'Search')) .'" autocomplete="off"'); ?>
		</div>

		<div class="form-group results table-responsive">
			<table class="table table-striped table-hover data-table">
				<thead>
					<tr>
						<th><?php echo language::translate('title_id', 'ID'); ?></th>
						<th><?php echo language::translate('title_name', 'Name'); ?></th>
						<th class="main"><?php echo language::translate('title_email', 'Email'); ?></th>
						<th><?php echo language::translate('title_date_registered', 'Date Registered'); ?></th>
					</tr>
				</thead>
				<tbody>
			</table>
		</div>
	</div>

</div>

<script>
	$('#modal-user-picker input[name="query"]').focus();

	let xhr_user_picker = null;
	$('#modal-user-picker input[name="query"]').on('input', function(){
		if ($(this).val() == '') {
			$('#modal-user-picker .results tbody').html('');
			xhr_user_picker = null;
			return;
		}
		xhr_user_picker = $.ajax({
			type: 'get',
			async: true,
			cache: false,
			url: '<?php echo document::ilink('users/users.json'); ?>?query=' + $(this).val(),
			dataType: 'json',
			beforeSend: function(jqXHR) {
				jqXHR.overrideMimeType('text/html;charset=' + $('html meta[charset]').attr('charset'));
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error(textStatus + ': ' + errorThrown);
			},
			success: function(json) {
				$('#modal-user-picker .results tbody').html('');
				$.each(json, function(i, row){
					if (row) {
						$('#modal-user-picker .results tbody').append(
							'<tr>' +
							'  <td class="id">' + row.id + '</td>' +
							'  <td class="name">' + row.name + '</td>' +
							'  <td class="email">' + row.email + '</td>' +
							'  <td class="date-created">' + row.date_created + '</td>' +
							'  <td></td>' +
							'</tr>'
						);
					}
				});
				if ($('#modal-user-picker .results tbody').html() == '') {
					$('#modal-user-picker .results tbody').html('<tr><td colspan="4"><em><?php echo functions::escape_js(language::translate('text_no_results', 'No results')); ?></em></td></tr>');
				}
			},
		});
	});

	$('#modal-user-picker tbody').on('click', 'td', function() {
		let row = $(this).closest('tr');

		let id = $(row).find('.id').text();
		let name = $(row).find('.name').text();

		if (!id) {
			id = 0;
			name = '(<?php echo functions::escape_js(language::translate('title_guest', 'Guest')); ?>)';
		}

		let field = $.featherlight.current().$currentTarget.closest('.form-input');

		$(field).find(':input').val(id).trigger('change');
		$(field).find('.id').text(id);
		$(field).find('.name').text(name);
		$.featherlight.close();
	});

	$('#modal-user-picker .set-guest').click(function(){

		let field = $.featherlight.current().$currentTarget.closest('.form-input');

		$(field).find(':input').val('0').trigger('change');
		$(field).find('.id').text('0');
		$(field).find('.name').text('(<?php echo functions::escape_js(language::translate('title_guest', 'Guest')); ?>)');
		$.featherlight.close();
	});
</script>

<?php
	require_once 'app://includes/app_footer.inc.php';
	exit;
