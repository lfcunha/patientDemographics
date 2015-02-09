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

    function getData($column, $order, $offset, $length){
        $table = $this->table;
        $data = $this->select($table, $column, $order, $offset, $length);
        return $data;
    }

    public function saveToDB($data){
        return $this->saveToDB_(array("data" => $data, "table" => $this->table));
    }

    public function saveEditToDb($data){
        return $this->saveEditToDb_($data, $this->table);
    }


    public function search($table, $length, $offset, $search, $column, $order){
        return $this->search_($table, $length, $offset, $search, $column, $order);
    }

}