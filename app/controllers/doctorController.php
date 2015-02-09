<?php



class doctorController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="doctorModel";
        $this->view="doctorView.twig";
    }


    public function getDoctor($column, $order, $offset, $length, $ajax){
        return $this->getData($column, $order, $offset, $length, $ajax, $this->model, $this->view);
    }

    public function fetchDoctor($column, $order, $offset, $length){
        return $this->getData($column, $order, $offset, $length, 0, $this->model);
    }

    public function deleteDoctor(){

    }

    public function addDoctor($data){
        return $this->saveData($data, 1, $this->model, $this->view);
    }

    public function editDoctor($data){
        return $this->editCell($data,$this->model);
    }

    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/samples2/DNA", "fixDNA.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }

}











