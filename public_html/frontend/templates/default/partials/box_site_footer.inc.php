<footer id="footer" class="hidden-print">
  <div class="container">
    <div class="row" style="margin-bottom: 0;">

      <div class="col-md-8">
        <div class="row" style="margin-bottom: 0;">

          <section class="information col-xs-6 col-sm-3">
            <h3 class="title"><?php echo language::translate('title_information', 'Information'); ?></h3>
            <ul class="list-unstyled">
              <?php foreach ($pages as $page) echo '<li><a href="'. htmlspecialchars($page['link']) .'">'. $page['title'] .'</a></li>' . PHP_EOL; ?>
            </ul>
          </section>

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
          <img src="<?php echo document::href_link(WS_DIR_STORAGE . 'images/logotype.png'); ?>" class="img-responsive" alt="<?php echo settings::get('site_name'); ?>" title="<?php echo settings::get('site_name'); ?>" />
        </div>

        <ul class="modules list-inline text-center">
          <?php foreach ($modules as $module) { ?>
          <li class="thumbnail"><img src="<?php echo document::href_link($module['icon']); ?>" class="img-responsive" alt="" /></li>
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
  <div class="container notice">
    <!-- LiteCart is provided free under license CC BY-ND 4.0 - https://creativecommons.org/licenses/by-nd/4.0/. Removing the link back to litecart.net without permission is a violation - https://www.litecart.net/addons/172/removal-of-attribution-link -->
    Copyright &copy; <?php echo date('Y'); ?> <?php echo settings::get('site_name'); ?>. All rights reserved &middot; Powered by <a href="https://www.litecart.net" target="_blank" title="Free e-commerce platform">LiteCart®</a>
  </div>
</section>
