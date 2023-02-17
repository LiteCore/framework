<?php
	
	header('Content-Type: text/plain;charset='. language::$selected['charset']);
?>
User-agent: *
Allow: /
Disallow: */cache/*
Sitemap: <?php echo document::ilink('feeds/sitemap.xml'); ?>
<?php
	exit;
