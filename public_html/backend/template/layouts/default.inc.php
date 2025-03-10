<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}"<?php echo !empty($_COOKIE['dark_mode']) ? ' class="dark-mode"' : ''; ?>>
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=1600">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://backend/template/css/variables.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://assets/litecore/css/framework.min.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://backend/template/css/app.min.css'); ?>">
{{head_tags}}
{{style}}
<style>
:root {
	--default-text-size: <?php echo !empty($_COOKIE['font_size']) ? $_COOKIE['font_size'] : '14'; ?>px;
}
</style>
</head>
<body>

<div id="backend-wrapper">
	<input id="sidebar-compressed" type="checkbox" hidden>

	<div id="sidebar" class="hidden-print">

		<a class="logotype" href="<?php echo document::href_ilink(''); ?>">
			<img class="center-block responsive" src="<?php echo document::href_rlink('storage://images/logotype.svg'); ?>" alt="<?php echo settings::get('site_name'); ?>">
		</a>

		<div class="filter">
			<?php echo functions::form_input_search('filter', false, 'placeholder="'. functions::escape_html(language::translate('title_filter', 'Filter')) .'&hellip;" autocomplete="off"'); ?>
		</div>

		<?php include 'app://backend/partials/box_apps_menu.inc.php'; ?>

		<div class="text-center">
			<a class="platform" href="<?php echo document::href_ilink('about'); ?>">
				<span class="name"><?php echo PLATFORM_NAME; ?>®</span>
				<span class="version"><?php echo PLATFORM_VERSION; ?></span>
			</a>
		</div>

		<div class="copyright" class="text-center">Copyright &copy; <?php echo date('2023-Y'); ?><br>
			<a href="https://litecore.dev" target="_blank">litecore.dev</a>
		</div>
	</div>

	<main id="main">

		<?php include 'app://backend/partials/site_top_navigation.inc.php'; ?>

		<div id="content">

			{{notices}}

			{{breadcrumbs}}

			{{content}}

		</div>
	</main>
</div>

{{foot_tags}}
{{javascript}}

<script src="<?php echo document::href_rlink('app://backend/template/js/app.min.js'); ?>"></script>

<script>
	$('button[name="font_size"]').on('click', function(){
		let new_size = parseInt($(':root').css('--default-text-size').split('px')[0]) + (($(this).val() == 'increase') ? 1 : -1);
		$(':root').css('--default-text-size', new_size + 'px');
		document.cookie = 'font_size='+ new_size +';Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
	});

	$('input[name="dark_mode"]').click(function(){
		if ($(this).val() == 1) {
			document.cookie = 'dark_mode=1;Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
			$('html').addClass('dark-mode');
		} else {
			document.cookie = 'dark_mode=0;Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
			$('html').removeClass('dark-mode');
		}
	});
</script>
</body>
</html>