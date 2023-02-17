<?php

  class ent_database_table_row {
    public $data;
    public $previous;
    public $_table_name;
    public $_primary_column;

    public function __construct($table_name, $this->_primary_column_value='') {

      $this->_table_name = $table_name;
      $this->_primary_column = reference::database_table($table_name)->primary_key;

      if (!empty($this->_primary_column_value)) {
        $this->load($table_name, $this->_primary_column_value);
      } else {
        $this->reset();
      }
    }

    public function reset() {

      $this->data = [];

      $row_query = database::query(
        "show fields from `". database::input($this->_table_name) ."`;"
      );

      while ($field = database::fetch($row_query)) {
        $this->data[$field['Field']] = database::create_variable($field);
      }

      $this->previous = $this->data;
    }

    public function load($primary_column_value) {

      $this->reset();

      $row = database::query(
        "select * from `". database::input($this->_table_name) ."`
        where `". database::input($this->_primary_column_name) ."` = '". database::input($primary_column_value) ."'
        limit 1;"
      )->fetch();

      if ($row) {
        $this->data = array_replace($this->data, array_intersect_key($row, $this->data));
      } else {
        throw new Exception('Could not find row ('. $this->_primary_column .': '. $primary_column_value .') in database.');
      }

      $this->previous = $this->data;
    }

    public function save() {

      if (empty($this->data[$this->_primary_column])) {
        database::query(
          "insert into `". database::input($this->_table_name) ."`
          (`". implode("`, `", database::input(array_keys($row))) ."`)
          values ('". implode("', '", database::input($row)) ."');"
        );
      } else {
        database::query(
          "update `". database::input($this->_table_name) ."`
          set ". implode(", ", array_walk($row, function($value, $key){ return "`". database::input($key) ."` = '". database::input($value) ."'"; })) ."
          where `". database::input($this->_primary_column) ."` = '". database::input($this->data[$this->_primary_column]) ."'
          limit 1;"
        );
      }

      $this->previous = $this->data;

      cache::clear_cache();
    }

    public function delete() {

      if (empty($this->data['id'])) return;

      database::query(
        "delete from `". database::input($this->_table_name) ."`
        where `". database::input($this->_primary_column) ."` = '". database::input($this->data[$this->_primary_column]) ."'
        limit 1;"
      );

      $this->reset();

      cache::clear_cache();
    }
  }
