<?php

  return [
    '' => [
      'pattern' => '#^(?:index\.php)?$#',
      'controller' => 'index',
      'params' => '',
      'endpoint' => 'frontend',
      'options' => [
        'redirect' => true,
      ],
      'rewrite' => function(ent_link $link, $language_code) {
        $link->path = ''; // Remove index file for site root
        return $link;
      }
    ],
  ];
