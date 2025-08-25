<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}">
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<?php echo functions::draw_style('app://frontend/template/css/variables.css'); ?>
<?php echo functions::draw_style('app://assets/litecore/css/framework.min.css'); ?>
<?php echo functions::draw_style('app://assets/litecore/css/printable.min.css'); ?>
{{head_tags}}
</head>
<body>

{{content}}

{{foot_tags}}
<?php echo functions::draw_script('app://frontend/template/js/app.min.js'); ?>

</body>
</html>