<?php



class cellLineController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="cellLineModel";
    }


    public function getCellLine($column, $order, $offset, $length, $ajax=0){
        return $this->getData($column, $order, $offset, $length, $ajax,$this->model, "cellLineView.twig");
    }

    public function fetchCellLine($column, $order, $offset, $length){
        return $this->getData($column, $order, $offset, $length, 0, $this->model);
    }

    public function deleteCellLine(){

    }

    public function addCellLine(){

    }

    public function editCellLine($data){
        return $this->editCell($data,"cellLineModel");
    }

    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/samples2/cellline", "fixcellline.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }
}




