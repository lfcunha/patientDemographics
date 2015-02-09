<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 10/17/14
 * Time: 12:27 AM
 */

ini_set('memory_limit', '-1');

class doctorModel extends \baseModel {

    function __construct() {
        parent::__construct();
        //$this->dbh = $this->getPDO();
    }

    public function getRules(){
        return $this->env["rules"]["doctor"];
    }

    function getData($column, $order, $offset, $length){
        $table="doctor";
        $data = $this->select($table, $column, $order, $offset, $length);
        return $data;
    }

    public function saveToDB($data){
        return $this->saveToDB(array("data"=>$data, "table"=>"doctor"));
    }

    public function saveEditToDb($data){
        return $this->saveEditToDb_($data, "doctor");
    }

    public function search($table, $length, $offset, $search, $column, $order){
        return $this->search_($table, $length, $offset, $search, $column, $order);
    }

}