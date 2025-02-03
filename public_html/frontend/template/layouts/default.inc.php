<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}">
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<meta name="description" content="{{description}}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://frontend/template/css/variables.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://assets/litecore/framework.min.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://frontend/template/css/app.min.css'); ?>">
{{head_tags}}
</head>
<body>

<div id="page">

	<header>
		<?php if ($important_notice = settings::get('important_notice')) { ?>
		<div id="important-message">
			<?php echo $important_notice; ?>
		</div>
		<?php } ?>

		<?php include 'app://frontend/partials/site_navigation.inc.php'; ?>
	</header>

	<main>

		{{content}}

	</main>

	<footer>
		<?php include 'app://frontend/partials/site_footer.inc.php'; ?>
	</footer>

</div>

<?php include 'app://frontend/partials/site_cookie_notice.inc.php'; ?>

{{foot_tags}}
<script src="<?php echo document::href_rlink('app://frontend/template/js/app.min.js'); ?>"></script>

</body>
</html>