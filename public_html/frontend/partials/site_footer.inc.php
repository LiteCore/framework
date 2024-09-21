<?php

	$site_footer = new ent_view('app://frontend/template/partials/site_footer.inc.php');

	//$site_footer_cache_token = cache::token('site_footer', ['language', 'login', 'region']);
	//if (!$site_footer->snippets = cache::get($site_footer_cache_token)) {

		$site_footer->snippets = [
			'pages' => [],
			'modules' => [],
			'social' => [],
		];

		$site_footer->snippets['social'] = [
			[
				'type' => 'facebook',
				'title' => 'Facebook',
				'icon' => 'fa-facebook',
				'link' => 'https://www.facebook.com/',
			],
			[
				'type' => 'twitter',
				'title' => 'Twitter',
				'icon' => 'fa-twitter',
				'link' => 'https://www.twitter.com/',
			],
			[
				'type' => 'linkedin',
				'title' => 'LinkedIn',
				'icon' => 'fa-linkedin',
				'link' => 'https://www.linkedin.com/',
			],
		];

	//	cache::set($site_footer_cache_token, $site_footer->snippets);
	//}

	//echo $site_footer;
	extract($site_footer->snippets);
?>
	<footer id="footer" class="hidden-print">
	<div class="container content">
		<div class="row" style="margin-bottom: 0;">

			<div class="col-md-8">
				<div class="row" style="margin-bottom: 0;">

					<section class="store-info col-sm-4">
						<h3 class="title"><?php echo language::translate('title_contact', 'Contact'); ?></h3>

						<?php if (settings::get('site_phone')) { ?>
						<p class="phone">
							<?php echo functions::draw_fonticon('fa-phone'); ?> <a href="tel:<?php echo settings::get('site_phone'); ?>"><?php echo settings::get('site_phone'); ?></a>
						<p>
						<?php } ?>

						<p class="email">
							<?php echo functions::draw_fonticon('fa-envelope'); ?> <a href="mailto:<?php echo settings::get('site_email'); ?>"><?php echo settings::get('site_email'); ?></a>
						</p>
					</section>

				</div>
			</div>

			<section class="hidden-xs hidden-sm col-md-4" style="align-self: center;">
				<div class="logotype">
					<img src="<?php echo document::href_rlink('storage://images/logotype.png'); ?>" class="img-responsive" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>">
				</div>

				<ul class="social-bookmarks flex flex-inline flex-gap text-center">
					<?php foreach ($social as $bookmark) { ?>
					<li><a href="<?php echo htmlspecialchars($bookmark['link']); ?>" class="thumbnail"><?php echo functions::draw_fonticon($bookmark['icon'] .' fa-fw', 'title="'. htmlspecialchars($bookmark['title']) .'"'); ?></a></li>
					<?php } ?>
				</ul>
			</section>

		</div>
	</div>
</footer>

<section id="copyright">
	<div class="container notice content">
		<!-- LiteCore is provided free under license CC BY-ND 4.0 - https://creativecommons.org/licenses/by-nd/4.0/. Removing the link back to the official website without permission is a violation. -->
		Copyright &copy; <?php echo date('Y'); ?> <?php echo settings::get('site_name'); ?>. All rights reserved &middot; Powered by <a href="https://www.github.com/litecart/litecore" target="_blank" title="Website Core Framework">LiteCore™</a>
	</div>
</section>
