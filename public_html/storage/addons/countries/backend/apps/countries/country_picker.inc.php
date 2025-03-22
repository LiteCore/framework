<?php

	document::$layout = 'ajax';

?>
<style>
#modal-country-picker tbody > tr {
	cursor: pointer;
}
</style>

<div id="modal-country-picker" class="modal fade" style="max-width: 640px; display: none;">

	<button class="set-guest btn btn-default float-end" type="button"><?php echo language::translate('text_set_as_guest', 'Set As Guest'); ?></button>

	<h2 style="margin-top: 0;"><?php echo language::translate('title_country', 'Country'); ?></h2>

	<div class="modal-body">
		<label class="form-group">
			<div class="form-label"><?php echo language::translate('title_search', 'Search'); ?></div>
			<?php echo functions::form_input_search('query', true, 'placeholder="'. functions::escape_attr(language::translate('title_search', 'Search')) .'"'); ?>
		</label>

		<div class="form-group results table-responsive">
			<table class="table data-table">
				<thead>
					<tr>
						<th class="main"><?php echo language::translate('title_name', 'Name'); ?></th>
						<th><?php echo language::translate('title_code', 'Code'); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

</div>

<script>
	var xhr_country_picker = null
	$('#modal-country-picker input[name="query"]').on('input', function() {
		if ($(this).val() == '') {
			$('#modal-country-picker .results tbody').html('')
			xhr_country_picker = null
			return
		}
		xhr_country_picker = $.ajax({
			type: 'get',
			async: true,
			cache: false,
			url: '<?php echo document::ilink(__APP__.'/countries.json'); ?>?query=' + $(this).val(),
			dataType: 'json',
			beforeSend: function(jqXHR) {
				jqXHR.overrideMimeType('text/html;charset=' + $('html meta[charset]').attr('charset'))
			},
			success: function(json) {
				$('#modal-country-picker .results tbody').html('')
				$.each(json, function(i, row) {
					if (row) {
						$('#modal-country-picker .results tbody').append(
							'<tr>' +
							'  <td class="name">' + row.name + '</td>' +
							'  <td class="code">' + row.code + '</td>' +
							'  <td></td>' +
							'</tr>'
						)
					}
				})
				if ($('#modal-country-picker .results tbody').html() == '') {
					$('#modal-country-picker .results tbody').html('<tr><td colspan="4"><em><?php echo functions::escape_js(language::translate('text_no_results', 'No results')); ?></em></td></tr>')
				}
			},
		})
	})

	$('#modal-country-picker tbody').on('click', 'td', function() {

		let row = $(this).closest('tr'),
			code = $(row).find('.code').text(),
			name = $(row).find('.name').text(),
			$field = $.litebox.current().$currentTarget.closest('.form-input')

		$field.find(':input').val(code).trigger('change')
		$field.find('.code').text(code)
		$field.find('.name').text(name)
		$.litebox.close()
	})

	$('#modal-country-picker .set-guest').on('click', function() {

		let field = $.litebox.current().$currentTarget.closest('.form-input')

		$(field).find(':input').val('0').trigger('change')
		$(field).find('.code').text('')
		$(field).find('.name').text('(<?php echo functions::escape_js(language::translate('title_guest', 'Guest')); ?>)')
		$.litebox.close()
	})
</script>
