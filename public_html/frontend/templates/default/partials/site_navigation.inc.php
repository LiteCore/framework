<header id="header" class="container">
	<div id="navigation" class="navbar navbar-sticky">

		<div class="navbar-header">
			<a class="logotype" href="<?php echo document::href_ilink(''); ?>">
				<img src="<?php echo document::href_link('images/logotype.png'); ?>" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>" />
			</a>

			<?php echo functions::form_begin('search_form', 'get', document::ilink('search'), false, 'class="navbar-search"'); ?>
				<?php echo functions::form_search_field('query', true, 'placeholder="'. language::translate('text_search_products', 'Search products') .' &hellip;"'); ?>
			<?php echo functions::form_end(); ?>

			<a class="regional-setting text-center" href="<?php echo document::href_ilink('regional_settings', ['redirect_url' => document::link()]); ?>#box-regional-settings" data-toggle="lightbox" data-seamless="true">
				<div class="navbar-icon"><?php echo functions::draw_fonticon('fa-globe'); ?></div>
				<small class="hidden-xs"><?php echo language::$selected['code']; ?></small>
			</a>

			<button type="button" class="btn btn-default navbar-toggler hidden-md hidden-lg hidden-xl hidden-xxl" data-toggle="offcanvas" data-target="#offcanvas">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div id="offcanvas" class="offcanvas">
			<div class="offcanvas-header">
				<div class="offcanvas-title"><?php echo settings::get('store_name'); ?></div>
				<button type="button" class="btn btn-default" data-toggle="dismiss"><?php echo functions::draw_fonticon('fa-times'); ?></button>
			</div>

			<div class="offcanvas-body">
				<ul class="navbar-nav">

					<li class="nav-item">
						<a class="nav-link" href="<?php echo document::href_ilink(''); ?>"><?php echo functions::draw_fonticon('fa-home hidden-xs hidden-sm'); ?> <span class="hidden-md hidden-lg hidden-xl hidden-xxl"><?php echo language::translate('title_home', 'Home'); ?></span></a>
					</li>

					<?php if ($pages) foreach ($pages as $item) { ?>
					<li class="nav-item"><a class="nav-link" href="<?php echo functions::escape_html($item['link']); ?>"><?php echo $item['title']; ?></a></li>
					<?php } ?>
				</ul>

				<ul class="navbar-nav">


				</ul>
			</div>
		</div>
	</div>
</header>