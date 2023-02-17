<?php
/*
$test->select('this, that')
     ->from('lc_table')
     ->left_join('lc_another_table on ')
     ->where('')
     ->exec();
*/

  class ent_database_query{
    public $data;

    function exec() {
      return database::query(
        (!empty($this->data['select']) ? implode(", ", $this->data['select']) : "") .
        (!empty($this->data['from']) ? implode(", ", $this->data['from']) : "") .
        (!empty($this->data['left_join']) ? implode(PHP_EOL, $this->data['left_join']) : "") .
        (!empty($this->data['where']) ? "where (" . implode(") and (", $this->data['where']) .")" : "") .
        (!empty($this->data['order_by']) ? "order by " . implode(PHP_EOL, $this->data['order_by']) : "") .
        (!empty($this->data['limit']) ? "limit " . implode(", ", database::input($this->data['limit'])) : "") .
      );
    }
  }
