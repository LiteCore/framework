<div id="site-menu" class="navbar hidden-print">

  <header class="navbar-header">

    <a class="logotype" href="<?php echo document::href_ilink(''); ?>">
      <img src="<?php echo document::href_link(WS_DIR_STORAGE . 'images/logotype.png'); ?>" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>" />
    </a>

    <div class="text-center hidden-xs">
      x
    </div>

    <div class="text-right">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#default-menu">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
  </header>

  <nav id="default-menu" class="navbar-collapse collapse">

    <ul class="nav navbar-nav">
      <li class="hidden-xs">
        <a href="<?php echo document::ilink(''); ?>" title="<?php echo language::translate('title_home', 'Home'); ?>"><?php echo functions::draw_fonticon('fa-home'); ?></a>
      </li>

      <?php if ($categories) { ?>
      <li class="categories dropdown">
        <a href="#" data-toggle="dropdown"><?php echo language::translate('title_products', 'Products'); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <?php foreach ($categories as $item) { ?>
          <li><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php echo $item['title']; ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if ($brands) { ?>
      <li class="brands dropdown">
        <a href="#" data-toggle="dropdown"><?php echo language::translate('title_brands', 'Manufacturers'); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <?php foreach ($brands as $item) { ?>
          <li><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php echo $item['title']; ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if ($pages) { ?>
      <li class="information dropdown">
        <a href="#" data-toggle="dropdown"><?php echo language::translate('title_information', 'Information'); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <?php foreach ($pages as $item) { ?>
          <li><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php echo $item['title']; ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>
    </ul>

    <ul class="nav navbar-nav navbar-right">
    </ul>
  </nav>
</div>
