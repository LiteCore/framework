<header id="header" class="container">
	<div id="navigation" class="navbar navbar-sticky">

		<div class="navbar-header">
			<a class="logotype" href="<?php echo document::href_ilink(''); ?>">
				<img src="<?php echo document::href_rlink('storage://images/logotype.png'); ?>" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>" />
			</a>

			<button type="button" class="btn btn-default navbar-toggler hidden-md hidden-lg hidden-xl hidden-xxl" data-toggle="offcanvas" data-target="#offcanvas">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div id="offcanvas" class="offcanvas">
			<div class="offcanvas-header">
				<div class="offcanvas-title"><?php echo settings::get('site_name'); ?></div>
				<button type="button" class="btn btn-default" data-toggle="dismiss"><?php echo functions::draw_fonticon('fa-times'); ?></button>
			</div>

			<div class="offcanvas-body">
				<ul class="navbar-nav">

					<li class="nav-item">
						<a class="nav-link" href="<?php echo document::href_ilink(''); ?>">
							<?php echo functions::draw_fonticon('fa-home hidden-xs hidden-sm'); ?> <span class="hidden-md hidden-lg hidden-xl hidden-xxl"><?php echo language::translate('title_home', 'Home'); ?></span>
						</a>
					</li>

					<?php foreach ($items as $item) { ?>
					<li class="nav-item" data-id="<?php echo functions::escape_html($item['id']); ?>">
						<a class="nav-link" href="<?php echo functions::escape_html($item['link']); ?>">
							<?php echo $item['title']; ?>
						</a>
					</li>
					<?php } ?>

					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Dropdown</a>
						<ul class="dropdown-menu">
							<li class="dropdown-item">
								<a class="nav-link href="#">Item 1</a>
							</li>
							<li class="dropdown-item">
								<a class="nav-link href="#">Item 2</a>
							</li>
							<li class="dropdown-item">
								<a class="nav-link href="#">Item 3</a>
							</li>
						</ul>
					</li>
				</ul>

				<ul class="navbar-nav">


				</ul>
			</div>
		</div>
	</div>
</header>