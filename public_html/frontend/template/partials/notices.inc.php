<div id="notices">
<?php
	foreach (array_keys($notices) as $type) {
		foreach ($notices[$type] as $notice) {
			switch ($type) {

				case 'errors':
					echo implode(PHP_EOL, [
						'<div class="notice notice-danger">',
						'  '. functions::draw_fonticon('icon-exclamation-triangle') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="notice">&times;</a>',
						'</div>',
					]);
					break;

				case 'warnings':
					echo implode(PHP_EOL, [
						'<div class="notice notice-warning">'
						 . '  '. functions::draw_fonticon('icon-exclamation-triangle') . ' ' . $notice
						 . '<a href="#" class="close" data-dismiss="notice">&times;</a>'
						 . '</div>',
					]);
					break;

				case 'notices':
					echo implode(PHP_EOL, [
						'<div class="notice notice-info">',
						'  '. functions::draw_fonticon('icon-info') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="notice">&times;</a>',
						'</div>',
					]);
					break;

				case 'success':
					echo implode(PHP_EOL, ['<div class="notice notice-success">',
						'  '. functions::draw_fonticon('icon-check') . ' ' . $notice,
						'  <a href="#" class="close" data-dismiss="notice">&times;</a>',
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
	$('.notice [data-dismiss="notice"]').click(function(){
		$(this).parent().slideUp();
	});
</script>