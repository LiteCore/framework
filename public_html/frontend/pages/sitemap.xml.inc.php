<?php

  @set_time_limit(300);

  language::set(settings::get('site_language_code'));

  $output = '<?xml version="1.0" encoding="'. mb_http_output() .'"?>' . PHP_EOL
          . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

  $hreflangs = '';
  foreach (language::$languages as $language) {
    if ($language['url_type'] == 'none') continue;
    $hreflangs .= '    <xhtml:link rel="alternate" hreflang="'. $language['code'] .'" href="'. document::href_ilink('', [], false, [], $language['code']) .'" />' . PHP_EOL;
  }

  $output .= '  <url>' . PHP_EOL
           . '    <loc>'. document::ilink('') .'</loc>' . PHP_EOL
           . $hreflangs
           . '    <lastmod>'. date('Y-m-d') .'</lastmod>' . PHP_EOL
           . '    <changefreq>daily</changefreq>' . PHP_EOL
           . '    <priority>1.0</priority>' . PHP_EOL
           . '  </url>' . PHP_EOL;

  $output .= '</urlset>';

  ob_clean();
  header('Content-type: application/xml; charset='. mb_http_output());
  echo $output;
  exit;
