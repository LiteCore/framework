
// Offcanvas
$('[data-toggle="offcanvas"]').click(function(e){
	e.preventDefault();
	var target = $(this).data('target');
	if ($(target).hasClass('show')) {
		$(target).removeClass('show');
		$(this).removeClass('toggled');
		$('body').removeClass('has-offcanvas');
	} else {
		$(target).addClass('show');
		$(this).addClass('toggled');
		$('body').addClass('has-offcanvas');
	}
});

$('.offcanvas [data-toggle="dismiss"]').click(function(e){
	$('.offcanvas').removeClass('show');
	$('[data-toggle="offcanvas"]').removeClass('toggled');
	$('body').removeClass('has-offcanvas');
});
