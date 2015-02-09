<?php



class familyController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="familyModel";
    }


    public function getFamily($column, $order, $offset, $length, $ajax){
        return $this->getData($column, $order, $offset, $length, $ajax, $this->model, "familyView.twig");
    }

    public function fetchFamily($column, $order, $offset, $length){
        return $this->getData($column, $order, $offset, $length);
    }

    public function deleteFamily(){

    }

    public function addFamily(){

    }

    public function editFamily($data){
        return $this->editCell($data,$this->model);
    }


    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/samples2/RNA", "fixRNA.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }

}