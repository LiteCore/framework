<!DOCTYPE html>
<html lang="{{language}}" dir="{{text_direction}}">
<head>
<title>{{title}}</title>
<meta charset="{{charset}}">
<link rel="stylesheet" href="<?php echo document::href_rlink(FS_DIR_TEMPLATE . 'css/variables.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink(FS_DIR_TEMPLATE . 'css/framework.min.css'); ?>">
<link rel="stylesheet" href="<?php echo document::href_rlink(FS_DIR_TEMPLATE . 'css/printable.min.css'); ?>">
{{head_tags}}
{{style}}
</head>
<body>

{{content}}

{{foot_tags}}
{{javascript}}

</body>
</html>
