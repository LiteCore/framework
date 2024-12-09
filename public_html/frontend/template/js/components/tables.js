// Data-Table Toggle Checkboxes
$('body').on('click', '.data-table *[data-toggle="checkbox-toggle"], .data-table .checkbox-toggle', function() {
	$(this).closest('.data-table').find('tbody td:first-child :checkbox').each(function() {
		$(this).prop('checked', !$(this).prop('checked')).trigger('change')
	})
	return false
})

$('body').on('click', '.data-table tbody tr', function(e) {
	if ($(e.target).is('a') || $(e.target).closest('a').length) return
	if ($(e.target).is('.btn, :input, th')) return
	$(this).find(':checkbox, :radio').first().trigger('click')
})
