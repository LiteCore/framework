<?php

  return [
    '' => [
      'pattern' => '#^(?:index\.php)?$#',
      'endpoint' => 'frontend',
      'controller' => 'app://frontend/pages/index.inc.php',
      'params' => '',
      'options' => [
        'redirect' => true,
      ],
      'rewrite' => function(ent_link $link, $language_code) {
        $link->path = ''; // Remove index file for site root
        return $link;
      }
    ],
  ];
