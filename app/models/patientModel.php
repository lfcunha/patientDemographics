<?php
/**
 * Created by luis Cunha
 * Date: 10/17/14
 * Time: 12:27 AM
 */


class patientModel extends \baseModel {

    function __construct() {
        parent::__construct();
        $this->table = "patient";
    }

    public function getRules(){
        return $this->env["rules"][$this->table];
    }

    //public function select
    function select($column, $order, $offset, $length){
        $table = $this->table;
        $data = $this->select_($table, $column, $order, $offset, $length);
        return $data;
    }

    //public function insert($data){
    public function insert($data){
        return $this->insert_(array("data" => $data, "table" => $this->table));
    }

    //public function update($data){
    public function update($data){
        return $this->update_($data, $this->table);
    }

    //public function select
    public function search($table, $length, $offset, $search, $column, $order){
        return $this->search_($table, $length, $offset, $search, $column, $order);
    }

    public function name(){
        return "patientModel";
    }
}