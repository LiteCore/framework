<?php

/*
  $currencies_cache = new ent_cache('currencies', 'file', 86400);
  if (!$currencies = $currencies_cache->get()) {
    $client = new http_client();

    $currencies = ...

    $currencies_cache->set($currencies);
  }
*/

  class ent_cache {

    public $data = true;

    public function __construct($keyword, $dependencies=[], $storage='memory', $ttl=900) {

      if (!in_array($storage, ['file', 'memory', 'session'])) {
        trigger_error('The storage type is not supported ('. $storage .')', E_USER_WARNING);
        return;
      }

      $hash_string = $keyword;

      if (!is_array($dependencies)) {
        $dependencies = [$dependencies];
      }

      $dependencies[] = 'site';

      if (settings::get('webp_enabled') && isset($_SERVER['HTTP_ACCEPT']) && preg_match('#image/webp#', $_SERVER['HTTP_ACCEPT'])) {
        $dependencies[] = 'webp';
      }

      $dependencies = array_unique($dependencies);
      sort($dependencies);

      foreach ($dependencies as $dependency) {
        switch ($dependency) {

          case 'domain':
          case 'host':
            $hash_string .= $_SERVER['HTTP_HOST'];
            break;

          case 'endpoint':
            $hash_string .= preg_match('#^'. preg_quote(WS_DIR_APP .'/'. BACKEND_ALIAS .'/', '#') .'.*#', route::$request) ? 'backend' : 'frontend';
            break;

          case 'get':
            $hash_string .= json_encode($_GET, JSON_UNESCAPED_SLASHES);
            break;

          case 'language':
            $hash_string .= language::$selected['code'];
            break;

          case 'layout':
            $hash_string .= document::$layout;
            break;

          case 'post':
            $hash_string .= json_encode($_POST, JSON_UNESCAPED_SLASHES);
            break;

          case 'site':
            $hash_string .= document::link(WS_DIR_APP);
            break;

          case 'template':
            $hash_string .= document::$template;
            break;

          case 'uri':
          case 'url':
            $hash_string .= document::link();
            break;

          case 'user':
            $hash_string .= user::$data['id'];
            break;

          case 'webp':
            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('#image/webp#', $_SERVER['HTTP_ACCEPT'])) {
              $hash_string .= 'webp';
            }
            break;

          case 'webpath':
            $hash_string .= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            break;

          default:
            $hash_string .= is_array($dependency) ? implode(',', $dependency) : $dependency;
            break;
        }
      }

      return [
        'id' => md5($hash_string) .'_'. $keyword,
        'storage' => $storage,
        'ttl' => $ttl,
      ];
    }

    public static function get($max_age=900, $force_cache=false) {

      if (empty($force_cache)) {
        if (empty(settings::get('cache_enabled'))) return;
        if (isset($_SERVER['HTTP_CACHE_CONTROL']) && preg_match('#no-cache#i', $_SERVER['HTTP_CACHE_CONTROL'])) return;
      }

      switch ($this->data['token']['storage']) {

        case 'file':

          $cache_file = FS_DIR_STORAGE .'cache/'. substr($this->data['token']['id'], 0, 2) .'/'. $this->data['token']['id'] .'.cache';

          if (file_exists($cache_file) && filemtime($cache_file) > strtotime('-'.$max_age .' seconds')) {

            if (!$data = file_get_contents($cache_file)) return;
            if (!$data = json_decode($data, true)) return;

            return $data;
          }
          return;

        case 'memory':

          switch (true) {
            case (function_exists('apcu_fetch')):
              return apcu_fetch($_SERVER['HTTP_HOST'].':'.$this->data['token']['id']);

            case (function_exists('apc_fetch')):
              return apc_fetch($_SERVER['HTTP_HOST'].':'.$this->data['token']['id']);

            default:
              $this->data['token']['storage'] = 'file';
              return self::get($this->data['token'], $max_age, $force_cache);
          }

        case 'session':

          if (isset(self::$_data[$this->data['token']['id']]['mtime']) && self::$_data[$this->data['token']['id']]['mtime'] > strtotime('-'.$max_age .' seconds')) {
            return self::$_data[$this->data['token']['id']]['data'];
          }

          return;

        default:

          trigger_error('Invalid cache storage ('. $this->data['token']['storage'] .')', E_USER_WARNING);

          return;
      }
    }

    public static function set($data) {

      if (empty($data)) return;

      switch ($this->data['token']['storage']) {

        case 'file':

          $cache_file = FS_DIR_STORAGE .'cache/'. substr($this->data['token']['id'], 0, 2) .'/'. $this->data['token']['id'] .'.cache';

          if (!is_dir(dirname($cache_file))) {
            if (!mkdir(dirname($cache_file))) {
              trigger_error('Could not create cache subfolder', E_USER_WARNING);
              return false;
            }
          }

          return file_put_contents($cache_file, json_encode($data, JSON_UNESCAPED_SLASHES));

        case 'memory':

          switch (true) {
            case (function_exists('apcu_store')):
              return apcu_store($_SERVER['HTTP_HOST'].':'.$this->data['token']['id'], $data, $this->data['token']['ttl']);

            case (function_exists('apc_store')):
              return apc_store($_SERVER['HTTP_HOST'].':'.$this->data['token']['id'], $data, $this->data['token']['ttl']);

            default:
              $this->data['token']['storage'] = 'file';
              return self::set($this->data['token'], $data);
          }

        case 'session':

          self::$_data[$this->data['token']['id']] = [
            'mtime' => time(),
            'data' => $data,
          ];


          return true;

        default:
          trigger_error('Invalid cache type ('. $storage .')', E_USER_WARNING);
          return;
      }
    }

    // Output recorder (This option is not affected by $enabled as fresh data is always recorded)
    public static function capture($max_age=900, $force_cache=false) {

      if (isset(cache::$_recorders[$this->data['token']['id']])) trigger_error('Cache recorder already initiated ('. $this->data['token']['id'] .')', E_USER_ERROR);

      $_data = self::get($this->data['token'], $max_age, $force_cache);

      if (!empty($_data)) {
        echo $_data;
        return false;
      }

      cache::$_recorders[$this->data['token']['id']] = [
        'id' => $this->data['token']['id'],
        'storage' => $this->data['token']['storage'],
      ];

      ob_start();

      return true;
    }

    public static function end_capture() {

      if (empty($this->data['token']['id'])) $this->data['token']['id'] = current(array_reverse(cache::$_recorders));

      if (!isset(cache::$_recorders[$this->data['token']['id']])) {
        trigger_error('Could not end buffer recording as token id doesn\'t exist', E_USER_WARNING);
        return false;
      }

      $_data = ob_get_clean();

      if ($_data === false) {
        trigger_error('No active recording while trying to end buffer recorder', E_USER_WARNING);
        return false;
      }

      echo $_data;

      self::set($this->data['token'], $_data);

      unset(cache::$_recorders[$this->data['token']['id']]);

      return true;
    }
  }
