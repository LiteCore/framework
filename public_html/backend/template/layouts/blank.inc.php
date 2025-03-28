<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}">
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://backend/template/css/variables.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://assets/litecore/css/framework.min.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://backend/template/css/app.min.css'); ?>">
{{head_tags}}
{{style}}
</head>
<body>

{{content}}

{{foot_tags}}
<script src="<?php echo document::href_rlink('app://backend/template/js/app.min.js'); ?>"></script>
{{javascript}}

</body>
</html>