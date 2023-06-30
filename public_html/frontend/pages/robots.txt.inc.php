<?php
	ob_clean();
	header('Content-Type: text/plain;charset='. mb_http_output());
?>
User-agent: *
Allow: /
Disallow: */cache/*
Sitemap: <?php echo document::ilink('sitemap.xml'); ?>
<?php

	exit; // As we don't need app_footer to process this with a template
