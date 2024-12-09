<div id="notices">
<?php
	foreach (array_keys($notices) as $type) {
		foreach ($notices[$type] as $notice) {
			switch ($type) {

				case 'errors':
					echo implode(PHP_EOL, [
						'<div class="alert alert-danger">',
						'  '. functions::draw_fonticon('icon-exclamation-triangle') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="alert">&times;</a>',
						'</div>',
					]);
					break;

				case 'warnings':
					echo implode(PHP_EOL, [
						'<div class="alert alert-warning">'
						 . '  '. functions::draw_fonticon('icon-exclamation-triangle') . ' ' . $notice
						 . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
						 . '</div>',
					]);
					break;

				case 'notices':
					echo implode(PHP_EOL, [
						'<div class="alert alert-info">',
						'  '. functions::draw_fonticon('icon-info-circle') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="alert">&times;</a>',
						'</div>',
					]);
					break;

				case 'success':
					echo implode(PHP_EOL, ['<div class="alert alert-success">',
						'  '. functions::draw_fonticon('icon-check-circle') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="alert">&times;</a>',
						'</div>',
					]);
					break;
			}
		}
	}
?>
</div>

<script>
	setTimeout(function(){
		$('#notices').fadeOut();
	}, 20e3);
	$('.alert [data-dismiss="alert"]').click(function(){
		$(this).parent().slideUp();
	});
</script>