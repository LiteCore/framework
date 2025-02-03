<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}">
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://frontend/template/css/variables.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://assets/litecore/framework.min.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink('app://assets/litecore/printable.min.css'); ?>">
{{head_tags}}
</head>
<body>

{{content}}

{{foot_tags}}
</body>
</html>