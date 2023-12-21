<?php
	breadcrumbs::reset();
	breadcrumbs::add(language::translate('title_dashboard', 'Dashboard'), WS_DIR_ADMIN);
	breadcrumbs::add(language::translate('title_about', 'About'));

	$_page = new ent_view('app://backend/template/pages/about.inc.php');
	echo $_page;