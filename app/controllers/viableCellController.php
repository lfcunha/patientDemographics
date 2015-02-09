<?php



class viableCellController extends baseController {

    public function __construct() {
        parent::__construct();

        $this->model="viableCellModel";
    }


    public function getViableCell($column, $order, $offset, $length, $ajax){
        return $this->getData($column, $order, $offset, $length, $ajax, $this->model, "viableCellView.twig");
    }

    public function fetchViableCell($column, $order, $offset, $length){
        return $this->model->getViableCell($column, $order, $offset, $length, 0, $this->model);
    }

    public function deleteViableCell(){

    }

    public function addViableCell(){

    }

    public function editViableCell($data){
        return $this->editCell($data,$this->model);
    }

    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/samples2/viableCell", "fixviablecell.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }


}