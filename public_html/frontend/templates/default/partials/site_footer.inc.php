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
					<img src="<?php echo document::href_link(WS_DIR_STORAGE . 'images/logotype.png'); ?>" class="img-responsive" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>">
				</div>

				<ul class="modules list-inline text-center">
					<?php foreach ($modules as $module) { ?>
					<li class="thumbnail"><img src="<?php echo document::href_link($module['icon']); ?>" class="img-responsive" alt=""></li>
					<?php } ?>
				</ul>

				<ul class="social-bookmarks list-inline text-center">
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
		Copyright &copy; <?php echo date('Y'); ?> <?php echo settings::get('site_name'); ?>. All rights reserved &middot; Powered by <a href="https://www.github.com/litecart/litebase" target="_blank" title="Website Core Framework">LiteCore™</a>
	</div>
</section>
