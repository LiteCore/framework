<?php

  class ent_database_table {
    public $data;
    public $previous;

    public function __construct($name='') {

      if (!empty($name)) {
        $this->load($name);
      } else {
        $this->reset();
      }
    }

    public function reset() {

      $this->data = [
        'name' => '',
        'columns' => [],
        'indexes' => [],
        'auto_increment' => '',
        'collation' => '',
        'engine' => '',
        'comment' => '',
        'date_created' => null,
      ];

      $this->previous = $this->data;
    }

    public function load($name) {

      $this->reset();

      $table = database::query(
        "select table_name as name, auto_increment, engine, table_collation as collation, create_time as date_created, table_comment as comment
        from information_schema.tables
        where TABLE_SCHEMA = '". database::input(DB_DATABASE) ."'
        and TABLE_NAME = '". database::input($name) ."'
        limit 1;"
      )->fetch();

      if ($table) {
        $this->data = array_replace($this->data, array_intersect_key($table, $this->data));
      } else {
        throw new Exception('Could not find table (Name: '. $name .') in database.');
      }


    // Columns
      $this->data['columns'] = [];

      $columns_query = database::query(
        "show full columns from `". database::input($name) ."`;"
      );

      while ($column = database::fetch($columns_query)) {
        $this->data['columns'][$column['Field']] = [
          'name' => $column['Field'],
          'type' => strtolower(strtok($column['Type'], '(')),
          'length' => preg_match('#\((.*?)\)#', $column['Type'], $matches) ? $matches[1] : '',
          'null' => preg_match('#^yes$#i', $column['Null']) ? true : false,
          'unsigned' => preg_match('#unsigned$#i', $column['Type']) ? true : false,
          'zerofill' => preg_match('#zerofill$#i', $column['Type']) ? true : false,
          'primary' => preg_match('#^pri$#i', $column['Key']) ? true : false,
          'key' => strtr($column['Key'], ['PRI' => 'primary', 'UNI' => 'unique', 'MUL' => 'multiple']),
          'default' => $column['Default'],
          'auto_increment' => preg_match('#auto_increment#i', $column['Extra']) ? true : false,
          'collation' => $column['Collation'],
          'comment' => $column['Comment'],
        ];
      }

    // Indexes
      $this->data['indexes'] = [];

      $index_query = database::query(
        "show index from `". database::input($name) ."`;"
      );

      while ($index = database::fetch($index_query)) {
        if (!isset($this->data['indexes'][$index['Key_name']])) {
          $this->data['indexes'][$index['Key_name']] = [
            'name' => $index['Key_name'],
            'kind' => ($index['Key_name'] == 'PRIMARY') ? 'primary' : (!$index['Non_unique'] ? 'unique' : 'key'),
            'type' => $index['Index_type'],
            'columns' => [$index['Column_name']],
            'cardinality' => $index['Cardinality'],
            'comment' => $index['Index_comment'],
          ];
        } else {
          $this->data['indexes'][$index['Key_name']]['columns'][] = $index['Column_name'];
        }
      }

      $this->previous = $this->data;
    }

    public function save() {

      $alterations = [];

      foreach ($this->previous['columns'] as $column) {
        if (!in_array($column['name'], array_keys($this->data['columns']))) {
          $alterations[] = "drop column `". $column['name'] ."`";
        }
      }

      foreach ($this->previous['indexes'] as $index) {
        $alterations[] = "drop key `". $index['name'] ."`";
      }

      foreach ($this->data['columns'] as $old_name => $column) {

        if (empty($column['default']) && in_array($column['type'], ['int', 'float', 'double', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
          $column['default'] = 0;
        }

        $alterations[] = implode(' ', array_filter([
          (!empty($this->previous['name']) ? (is_numeric($old_name) ? 'add column ' : 'change column `'. database::input($old_name) .'` ') : '') . '`'. database::input($column['name']) .'`',
          database::input($column['type']) . (isset($column['length']) ? ' ('. database::input($column['length']) .')' : ''),
          !empty($column['unsigned']) ? 'unsigned' : '',
          !empty($column['null']) ? 'null' : 'not null',
          !empty($column['auto_increment']) ? 'auto_increment' : '',
          (isset($column['default']) || !empty($column['null'])) ? 'default ' . (isset($column['default']) ? "'". database::input($column['default']) ."'" : 'null') : '',
          !empty($column['collate']) ? 'collate '. database::input($column['collate']) : '',
          !empty($this->previous['name']) ? (!empty($last_column) ? 'after `'. database::input($last_column) .'`' : 'first') : '',
        ]));
        $last_column = $column['name'];
      }

      foreach ($this->data['indexes'] as $index) {
        return implode(' ', array_filter([
          !empty($column['primary']) ? 'primary key' : (!empty($column['unique']) ? 'unique key' : 'key'),
          '`'. database::input($index['name']) .'`',
          "(`". implode("`, `", $index['columns']) ."`)"
        ]));
      }

      if (empty($this->previous['name'])) {
        database::query(
          "create table `". database::input($this->data['name']) ."` (
          ". implode(','.PHP_EOL, $alterations) ."
          ) ". (!empty($this->data['engine']) ? "engine=". $this->data['engine'] : "") ." collate='". database::input($this->data['collation']) ."';"
        );
        $this->data['id'] = database::insert_id();
      } else {
        database::query(
          "alter table `". database::input($this->data['name']) ."` (
          ". implode(','.PHP_EOL, $alterations) ."
          ) engine=". $this->data['engine'] ." convert to character set ". database::input(strtok($this->data['collation'], '_')) ." collate ". database::input($this->data['collation']) .";"
        );
      }

      $this->previous = $this->data;

      cache::clear_cache();
    }

    public function delete() {

      if (empty($this->previous['name'])) return;

      database::query(
        "drop table `". database::input($this->data['name']) ."`;"
      );

      $this->reset();

      cache::clear_cache();
    }
  }
