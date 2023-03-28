<?php

  class ent_addon {
    public $data;
    public $previous;

    public function __construct($folder_name=null) {

      if (!empty($folder_name)) {
        $folder_name = basename($folder_name);
        $this->load($folder_name);
      } else {
        $this->reset();
      }
    }

    public function reset() {

      $this->data = [
        'id' => '',
        'status' => true,
        'folder' => '',
        'location' => '',
        'name' => '',
        'description' => '',
        'version' => '',
        'author' => '',
        'settings' => [],
        'aliases' => [],
        'install' => '',
        'uninstall' => '',
        'upgrades' => [],
        'files' => [],
        'installed' => false,
        'date_updated' => null,
        'date_created' => date('Y-m-d H:i:s'),
      ];

      $this->previous = $this->data;
    }

    public function load($folder_name) {

      $this->reset();

      $this->data['folder'] = $folder_name;

      if (!is_dir($this->data['location'] = 'storage://addons/'. $folder_name .'/')) {
        if (!is_dir($this->data['location'] = 'storage://addons/'. $folder_name .'.disabled/')) {
          throw new Exception('Invalid vMod ('. $folder_name .')');
        }
      }

      $this->data['status'] = !preg_match('#\.disabled$#', $this->data['folder']);

      if (!is_file($this->data['location'] .'vmod.xml')) {
        throw new Exception('Could not find '. $this->data['location'] .'vmod.xml');
      }

      $xml = file_get_contents($this->data['location'] .'vmod.xml');
      $dom = new \DOMDocument('1.0', 'UTF-8');
      $dom->preserveWhiteSpace = false;

      if (!$dom->loadXml($xml)) {
        throw new Exception(libxml_get_errors());
      }

      $this->data['id'] = preg_replace('#\.disabled$#', '', $this->data['folder']);
      $this->data['name'] = fallback($dom->getElementsByTagName('name')->item(0)->textContent, '');
      $this->data['version'] = fallback($dom->getElementsByTagName('version')->item(0)->textContent, date('Y-m-d', filemtime($this->data['location'] .'vmod.xml')));
      $this->data['description'] = fallback($dom->getElementsByTagName('description')->item(0)->textContent, '');
      $this->data['author'] = fallback($dom->getElementsByTagName('author')->item(0)->textContent, '');
      $this->data['date_created'] = date('Y-m-d H:i:s', filectime($this->data['location'] .'vmod.xml'));
      $this->data['date_updated'] = date('Y-m-d H:i:s', filemtime($this->data['location'] .'vmod.xml'));

      foreach ($dom->getElementsByTagName('alias') as $alias_node) {
        $this->data['aliases'][$alias_node->getAttribute('key')] = $alias_node->getAttribute('value');
      }

      foreach ($dom->getElementsByTagName('setting') as $setting_node) {
        $this->data['settings'][] = [
          'title' => $setting_node->getElementsByTagName('title')->item(0)->textContent,
          'description' => $setting_node->getElementsByTagName('description')->item(0)->textContent,
          'key' => $setting_node->getElementsByTagName('key')->item(0)->textContent,
          'function' => $setting_node->getElementsByTagName('function')->item(0)->textContent,
          'default_value' => $setting_node->getElementsByTagName('default_value')->item(0)->textContent,
        ];
      }

      if ($install_node = $dom->getElementsByTagName('install')->item(0)) {
        $this->data['install'] = rtrim($install_node->textContent);
      }

      if ($uninstall_node = $dom->getElementsByTagName('uninstall')->item(0)) {
        $this->data['uninstall'] = rtrim($uninstall_node->textContent);
      }

      foreach ($dom->getElementsByTagName('upgrade') as $upgrade_node) {
        $this->data['upgrades'][] = [
          'version' => $upgrade_node->getAttribute('version'),
          'script' => rtrim($upgrade_node->textContent),
        ];
      }

      $f = 0;
      foreach ($dom->getElementsByTagName('file') as $file_node) {

        $this->data['files'][$f] = [
          'name' => $file_node->getAttribute('name'),
          'operations' => [],
        ];

        $o = 0;
        foreach ($file_node->getElementsByTagName('operation') as $operation_node) {

          $this->data['files'][$f]['operations'][$o] = [
            'type' => $operation_node->getAttribute('type'),
            'method' => $operation_node->getAttribute('method'),
            'find' => [],
            'insert' => [],
            'onerror' => $operation_node->getAttribute('onerror'),
          ];

          if ($find_node = $operation_node->getElementsByTagName('find')->item(0)) {

            if (in_array($operation_node->getAttribute('type'), ['inline', 'regex'])) {
              $find_node->textContent = trim($find_node->textContent);

            } else if (in_array($operation_node->getAttribute('type'), ['multiline', ''])) {
              $find_node->textContent = preg_replace('#^(\r\n?|\n)?#s', '', $find_node->textContent); // Trim beginning of CDATA
              $find_node->textContent = preg_replace('#(\r\n?|\n)[\t ]*$#s', '', $find_node->textContent); // Trim end of CDATA
            }

            $this->data['files'][$f]['operations'][$o]['find'] = [
              'content' => $find_node->textContent,
              'index' => $find_node->getAttribute('index'),
              'offset-before' => $find_node->getAttribute('offset-before'),
              'offset-after' => $find_node->getAttribute('offset-after'),
            ];
          }

          if ($insert_node = $operation_node->getElementsByTagName('insert')->item(0)) {

            if (in_array($operation_node->getAttribute('type'), ['inline', 'regex'])) {
              $insert_node->textContent = trim($insert_node->textContent);

            } else if (in_array($operation_node->getAttribute('type'), ['multiline', ''])) {
              $insert_node->textContent = preg_replace('#^(\r\n?|\n)#s', '', $insert_node->textContent); // Trim beginning of CDATA
              $insert_node->textContent = preg_replace('#(\r\n?|\n)[\t ]*$#s', '', $insert_node->textContent); // Trim end of CDATA
            }

            $this->data['files'][$f]['operations'][$o]['insert'] = [
              'content' => $insert_node->textContent,
            ];
          }

          $o++;
        }

        $f++;
      }

      $installed_addons = preg_split('#[\r\n]+#', file_get_contents('storage://addons/.installed'), -1, PREG_SPLIT_NO_EMPTY);
      $this->data['installed'] = in_array($this->data['id'], $installed_addons) ? true : false;

      $this->previous = $this->data;
    }

    public function save() {

      if (empty($this->data['id'])) {
        throw new Exception('vMod ID cannot be empty');
      }

      $this->data['folder'] = $this->data['id'] . (empty($this->data['status']) ? '.disabled' : '');
      $this->data['location'] = 'storage://addons/'.$this->data['folder'] .'/';

      if (empty($this->previous['folder'])) {
        mkdir($this->data['location']);
      } else if ($this->data['folder'] != $this->previous['folder']) {
        rename($this->previous['location'], $this->data['location']);
      }

      $xml = (string)$this;

      file_put_contents($this->data['location'] .'/vmod.xml', $xml);

      $this->previous = $this->data;

      cache::clear_cache('vmods');
    }

    public function __toString() {

      $dom = new DomDocument('1.0', 'UTF-8');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;

      $vmod_node = $dom->createElement('vmod');

      $vmod_node->appendChild( $dom->createElement('id', $this->data['id']) );
      $vmod_node->appendChild( $dom->createElement('name', $this->data['name']) );
      $vmod_node->appendChild( $dom->createElement('version', $this->data['version']) );
      $vmod_node->appendChild( $dom->createElement('description', $this->data['description']) );
      $vmod_node->appendChild( $dom->createElement('author', $this->data['author']) );

    // Settings
      foreach ($this->data['settings'] as $setting) {
        $setting_node = $dom->createElement('setting');
        $setting_node->appendChild( $dom->createElement('title', $setting['title']) );
        $setting_node->appendChild( $dom->createElement('description', $setting['description']) );
        $setting_node->appendChild( $dom->createElement('key', $setting['key']) );
        $setting_node->appendChild( $dom->createElement('default_value', $setting['default_value']) );
        $setting_node->appendChild( $dom->createElement('function', $setting['function']) );
        $vmod_node->appendChild( $setting_node );
      }

    // Aliases
      foreach ($this->data['aliases'] as $alias) {
        $vmod_node->appendChild( $dom->createElement('alias', $alias) );
      }

    // Install
      if (!empty($this->data['install'])) {
        $install_node = $dom->createElement('install');
        $install_node->appendChild( $dom->createCDATASection(PHP_EOL . rtrim($this->data['install']) . PHP_EOL . str_repeat(' ', 2)) );
        $vmod_node->appendChild( $install_node );
      }

    // Uninstall
     if (!empty($this->data['uninstall'])) {
       $uninstall_node = $dom->createElement('uninstall');
       $uninstall_node->appendChild( $dom->createCDATASection(PHP_EOL . rtrim($this->data['uninstall']) . PHP_EOL . str_repeat(' ', 2)) );
       $vmod_node->appendChild( $uninstall_node );
     }

   // Upgrade
     foreach ($this->data['upgrades'] as $upgrade) {
       $upgrade_node = $dom->createElement('upgrade');
       $attribute = $dom->createAttribute('version');
       $attribute->value = $upgrade['version'];
       $upgrade_node->appendChild( $attribute );
       $upgrade_node->appendChild( $dom->createCDATASection(PHP_EOL . rtrim($upgrade['script']) . PHP_EOL . str_repeat(' ', 4)) );
       $vmod_node->appendChild( $upgrade_node );
     }

    // Files
      foreach ($this->data['files'] as $file) {
        $file_node = $dom->createElement('file');

        foreach (['path', 'name'] as $attribute_name) {
          if (!empty($file[$attribute_name])) {
            $attribute = $dom->createAttribute($attribute_name);
            $attribute->value = $file[$attribute_name];
            $file_node->appendChild($attribute);
          }
        }

        foreach ($file['operations'] as $operation) {
          $operation_node = $dom->createElement('operation');

          foreach (['onerror'] as $attribute_name) {
            if (!empty($operation[$attribute_name])) {
              $attribute = $dom->createAttribute($attribute_name);
              $attribute->value = $operation[$attribute_name];
              $operation_node->appendChild($attribute);
            }
          }

        // Find
          $find_node = $dom->createElement('find');

          foreach (['regex', 'trim'] as $attribute_name) {
            if (!empty($operation['find'][$attribute_name])) {
              $attribute = $dom->createAttribute($attribute_name);
              $attribute->value = !empty($operation['find'][$attribute_name]) ? 'true' : 'false';
              $find_node->appendChild($attribute);
            }
          }

          foreach (['offset-before', 'offset-after', 'index'] as $attribute_name) {
            if (!empty($operation['find'][$attribute_name])) {
              $attribute = $dom->createAttribute($attribute_name);
              $attribute->value = $operation['find'][$attribute_name];
              $find_node->appendChild($attribute);
            }
          }

          if ($operation['type'] == 'regex') {
            $find_node->appendChild( $dom->createCDATASection($operation['find']['content']) );
          } else {
            $find_node->appendChild( $dom->createCDATASection(PHP_EOL . $operation['find']['content'] . PHP_EOL . str_repeat(' ', 6)) );
          }

          $operation_node->appendChild( $find_node );

        // Insert
          $insert_node = $dom->createElement('insert');

          foreach (['regex', 'trim', 'position'] as $attribute_name) {
            if (!empty($operation['insert'][$attribute_name])) {
              $attribute = $dom->createAttribute($attribute_name);
              $attribute->value = $operation['insert'][$attribute_name];
              $insert_node->appendChild($attribute);
            }
          }

          if ($operation['type'] == 'regex') {
            $insert_node->appendChild( $dom->createCDATASection(@$operation['insert']['content']) );
          } else {
            $insert_node->appendChild( $dom->createCDATASection(PHP_EOL . $operation['insert']['content'] . PHP_EOL . str_repeat(' ', 6)) );
          }

          $operation_node->appendChild( $insert_node );

        // Ignore If
          if (!empty($operation['ignoreif']['content'])) {

            $ignoreif_node = $dom->createElement('ignoreif');

            foreach (['regex', 'trim'] as $attribute_name) {
              if (!empty($operation['ignoreif'][$attribute_name])) {
                $attribute = $dom->createAttribute($attribute_name);
                $attribute->value = $operation['ignoreif'][$attribute_name];
                $ignoreif_node->appendChild($attribute);
              }
            }

            if (@$operation['ignoreif']['regex'] == 'true') {
              $ignoreif_node->appendChild( $dom->createCDATASection($operation['ignoreif']['content']) );
            } else {
              $ignoreif_node->appendChild( $dom->createCDATASection(PHP_EOL . $operation['ignoreif']['content'] . PHP_EOL . str_repeat(' ', 6)) );
            }

            $operation_node->appendChild( $ignoreif_node );
          }

          $file_node->appendChild($operation_node);
        }

        $vmod_node->appendChild( $file_node );
      }

      $dom->appendChild( $vmod_node );

      $xml = $dom->saveXML();

    // Pretty print
      $xml = preg_replace('#^( +<(alias|setting|install|uninstall|upgrade|file)[^>]*>)#m', PHP_EOL . '$1', $xml);
      $xml = preg_replace('#^(\n|\r\n?){2,}#m', PHP_EOL, $xml);

      return $xml;
    }

    public function test() {

      $results = [];

      $tmp_file = functions::file_create_tempfile();
      file_put_contents($tmp_file, (string)$this);

      vmod::parse_xml($tmp_file, $this->data['location'] .'vmod.xml');

      foreach ($this->data['files'] as $file) {
        foreach (explode(',', $file['name']) as $pattern) {
          $path_and_file = $file['path'].$pattern;

          if (!empty(vmod::$aliases)) {
            $path_and_file = preg_replace(array_keys(vmod::$aliases), array_values(vmod::$aliases), $path_and_file);
          }

          if (!is_file(FS_DIR_APP . $path_and_file)) {
            $results[$path_and_file] = 'File does not exist';
            continue;
          }

          $buffer = file_get_contents(FS_DIR_APP . $path_and_file);

          foreach ($file['operations'] as $i => $operation) {
            $results[$path_and_file][$i] = null;

            if (!empty($operation['ignoreif']) && preg_match($operation['ignoreif'], $buffer)) {
              continue;
            }

            $found = preg_match_all($operation['find']['pattern'], $buffer, $matches, PREG_OFFSET_CAPTURE);

            if (!$found) {
              switch ($operation['onerror']) {
                case 'ignore':
                  continue 2;
                case 'abort':
                case 'warning':
                default:
                  $results[$path_and_file][$i] = 'Search not found';
                  continue 2;
              }
            }

            if (!empty($operation['find']['indexes'])) {
              rsort($operation['find']['indexes']);

              foreach ($operation['find']['indexes'] as $index) {
                $index = $index - 1; // [0] is the 1st in match count

                if ($found > $index) {
                  $buffer = substr_replace($buffer, preg_replace($operation['find']['pattern'], $operation['insert'], $matches[0][$index][0]), $matches[0][$index][1], strlen($matches[0][$index][0]));
                }
              }

            } else {
              $buffer = preg_replace($operation['find']['pattern'], $operation['insert'], $buffer, -1, $count);

              if (!$count && $operation['onerror'] != 'skip') {
                $results[$path_and_file][$i] = 'Failed to perform insert';
                continue;
              }
            }

            $results[$path_and_file][$i] = true;
          }
        }
      }
      return $results;
    }

    public function check() {

      $errors = [];

      $vmod = vmod::parse_xml((string)$this, $this->data['location'] .'vmod.xml');

      foreach (array_keys($vmod['files']) as $key) {
        $patterns = explode(',', $vmod['files'][$key]['name']);

        foreach ($patterns as $pattern) {
          $path_and_file = $vmod['files'][$key]['path'].$pattern;

        // Apply path aliases
          if (!empty(vmod::$aliases)) {
            $path_and_file = preg_replace(array_keys(vmod::$aliases), array_values(vmod::$aliases), $path_and_file);
          }

          if (!is_file(FS_DIR_APP . $path_and_file) && (empty($vmod['files'][$key]['onerror']) || strtolower($vmod['files'][$key]['onerror']) != 'skip')) {
            $errors[] = 'File does not exist: ' . $path_and_file;
            continue 2;
          }

          $buffer = file_get_contents(FS_DIR_APP . $path_and_file);

          foreach ($vmod['files'][$key]['operations'] as $i => $operation) {

            if (!empty($operation['ignoreif']) && preg_match($operation['ignoreif'], $buffer)) {
              continue;
            }

            $found = preg_match_all($operation['find']['pattern'], $buffer, $matches, PREG_OFFSET_CAPTURE);

            if (!$found) {
              switch ($operation['onerror']) {
                case 'ignore':
                  continue 2;
                case 'abort':
                case 'warning':
                default:
                  $errors[] = "Search not found in operation $i ($path_and_file)";
                  continue 2;
              }
            }

            if (!empty($operation['find']['indexes'])) {
              rsort($operation['find']['indexes']);

              foreach ($operation['find']['indexes'] as $index) {
                $index = $index - 1; // [0] is the 1st in match count

                if ($found > $index) {
                  $buffer = substr_replace($buffer, preg_replace($operation['find']['pattern'], $operation['insert'], $matches[0][$index][0]), $matches[0][$index][1], strlen($matches[0][$index][0]));
                }
              }

            } else {
              $buffer = preg_replace($operation['find']['pattern'], $operation['insert'], $buffer, -1, $count);

              if (!$count && $operation['onerror'] != 'skip') {
                $errors = "Failed to perform insert for operation $i ($path_and_file)";
                continue;
              }
            }
          }
        }
      }

      return $errors;
    }

    public function delete($cleanup=false) {

      if (empty($this->previous['folder'])) return;

      $dom = new DOMDocument('1.0', 'UTF-8');

      if (!@$dom->loadXML($this->previous['location'] . 'vmod.xml') || !$dom->getElementsByTagName('vmod')) {
        throw new Exception(language::translate('error_xml_file_is_not_valid_vmod', 'XML file is not a valid vMod file'));
      }

      if (!empty($dom->getElementsByTagName('uninstall'))) {
        $tmp_file = functions::file_create_tempfile();
        file_put_contents($tmp_file, "<?php\r\n" . $dom->getElementsByTagName('uninstall')->textContent);

        (function() {
          include func_get_arg(0);
        })($tmp_file);
      }

      functions::file_delete($this->previous['location']);

      $this->reset();

      cache::clear_cache('vmods');
    }
  }
