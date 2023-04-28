<?php
	breadcrumbs::reset();
	breadcrumbs::add(language::translate('title_dashboard', 'Dashboard'), WS_DIR_ADMIN);
	breadcrumbs::add(language::translate('title_about', 'About'));

	$_page = new ent_view(FS_DIR_TEMPLATE . 'pages/about.inc.php');
	echo $_page;