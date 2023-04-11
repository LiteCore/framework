<?php
	header('Content-Type: text/plain;charset='. mb_http_output());
?>
User-agent: *
Allow: /
Disallow: */cache/*
Sitemap: <?php echo document::ilink('sitemap.xml'); ?>
<?php
	exit;
