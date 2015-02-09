<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 10/17/14
 * Time: 12:27 AM
 */


class familyModel extends \baseModel {

    function __construct() {
        parent::__construct();
    }

    public function getRules(){
        return $this->env["rules"]["family"];
    }

    function getData($column, $order, $offset, $length){
        $table="family";
        return $this->select($table, $column, $order, $offset, $length);
    }

    public function saveToDB($data){
        return $this->saveToDB_(array("data"=>$data, "table"=>"family"));
    }

    public function saveEditToDb($data){
        return $this->saveEditToDb_($data, "family");
    }

    public function search($table, $length, $offset, $search, $column, $order){
        return $this->search_($table, $length, $offset, $search, $column, $order);
    }
}