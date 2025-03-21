<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/error_document.inc.php
	 */

	document::$layout = 'blank';

	if (!empty($_GET['code'])) {
		http_response_code($_GET['code']);
	}

	if (preg_match('#\.(a?png|avif|gif|jpg|png|svg|webp)$#', route::$request)) {
		echo file_get_contents('images/no_image.png');
		exit;
	}

	$_page = new ent_view('app://frontend/template/pages/error_document.inc.php');

	// Place your snippets here
	// ...

	switch (http_response_code()) {

		case 400:
			$_page->snippets['title'] = 'Bad Request';
			$_page->snippets['description'] = language::translate('error_400_bad_request', 'The server cannot or will not process the request due to a client error.');
			break;

		case 401:
			$_page->snippets['title'] = 'Unauthorized';
			$_page->snippets['description'] = language::translate('error_401_unauthorized', 'You are not authorized to view the requested file.');
			break;

		case 403:
			$_page->snippets['title'] = 'Forbidden';
			$_page->snippets['description'] = language::translate('error_403_forbidden', 'Access to the requested file is forbidden.');
			break;

		case 404:
			$_page->snippets['title'] = 'Not Found';
			$_page->snippets['description'] = language::translate('error_404_not_found', 'The requested file could not be found.');
			break;

		case 410:
			$_page->snippets['title'] = 'Gone';
			$_page->snippets['description'] = language::translate('error_410_gone', 'The requested page is no longer available.');
			break;

		default:
			http_response_code(500);
			$_page->snippets['title'] = 'Internal Server Error';
			$_page->snippets['description'] = language::translate('error_500_internal_server_error', 'That was not meant to happen.');
			break;
	}

	$_page->snippets['status_code'] = http_response_code();

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>
<style>
#box-error-document .code {
	font-size: 64px;
	font-weight: bold;
}
#box-error-document .title {
	font-size: 36px;
}
#box-error-document .description {
	font-size: 18px;
}
</style>

<div class="container" style="flex-grow: 1; display: flex; ">
	<main id="content" style="margin: auto;">
		{{notices}}

		<article id="box-error-document" class="text-center">
			<div class="code">HTTP <?php echo $status_code; ?></div>
			<div class="title"><?php echo $title; ?></div>
			<p class="description"><?php echo $description; ?></p>
		</article>
	</main>
</div>
