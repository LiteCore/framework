// Data-Table Toggle Checkboxes
$('body').on('click', '.data-table *[data-toggle="checkbox-toggle"]', function() {
	$(this).closest('.data-table').find('tbody :checkbox').each(function() {
		$(this).prop('checked', !$(this).prop('checked'));
	});
	return false;
});

$('.data-table tbody tr').click(function(e) {
	if ($(e.target).is(':input')) return;
	if ($(e.target).is('a, a *')) return;
	if ($(e.target).is('th')) return;
	$(this).find('input:checkbox').trigger('click');
});
