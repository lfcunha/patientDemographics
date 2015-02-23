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

    function select($column, $order, $offset, $length){
        $table="family";
        return $this->select_($table, $column, $order, $offset, $length);
    }

    public function insert($data){
        return $this->insert_(array("data"=>$data, "table"=>"family"));
    }

    public function update($data){
        return $this->update_($data, "family");
    }

    public function search($table, $length, $offset, $search, $column, $order){
        return $this->search_($table, $length, $offset, $search, $column, $order);
    }

    public function name(){
        return "familyModel";
    }

}