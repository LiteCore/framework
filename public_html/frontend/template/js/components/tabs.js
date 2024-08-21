
// Tabs (data-toggle="tab")
$('.nav-tabs').each(function(){
	if (!$(this).find('.active').length) {
		$(this).find('[data-toggle="tab"]:first').addClass('active');
	}

	$(this).on('select', '[data-toggle="tab"]', function() {
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
		$($(this).attr('href')).show().siblings().hide();
	});

	$(this).on('click', '[data-toggle="tab"]', function(e) {
		e.preventDefault();
		$(this).trigger('select');
		history.replaceState({}, '', location.toString().replace(/#.*$/, '') + $(this).attr('href'));
	});

	$(this).find('.active').trigger('select');
});

if (document.location.hash != '') {
	$('a[data-toggle="tab"][href="' + document.location.hash + '"]').click();
}
