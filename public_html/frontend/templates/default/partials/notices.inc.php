<div id="notices">
<?php
	foreach (array_keys($notices) as $type) {
		foreach ($notices[$type] as $notice) {
			switch ($type) {

				case 'errors':
					echo '<div class="alert alert-danger">'
					   . '  '. functions::draw_fonticon('fa-exclamation-triangle') . ' ' . $notice
					   . '  <a href="#" class="close" data-dismiss="alert">&times;</a>'
					   . '</div>' . PHP_EOL;
					break;

				case 'warnings':
					echo '<div class="alert alert-warning">'
					   . '  '. functions::draw_fonticon('fa-exclamation-triangle') . ' ' . $notice
					   . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
					   . '</div>' . PHP_EOL;
					break;

				case 'notices':
					echo '<div class="alert alert-info">'
					   . '  '. functions::draw_fonticon('fa-info-circle') . ' ' . $notice
					   . '  <a href="#" class="close" data-dismiss="alert">&times;</a>'
					   . '</div>' . PHP_EOL;
					break;

				case 'success':
					echo '<div class="alert alert-success">'
					   . '  '. functions::draw_fonticon('fa-check-circle') . ' ' . $notice
					   . '  <a href="#" class="close" data-dismiss="alert">&times;</a>'
					   . '</div>' . PHP_EOL;
					break;
			}
		}
	}
?>
</div>

<script>
	setTimeout(function(){$('#notices').fadeOut();}, 2e4);
</script>