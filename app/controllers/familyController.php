<?php



class familyController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="familyModel";
        $this->view = "familyView.twig";
    }


    public function getFamily($column, $order, $offset, $length, $ajax){
        $data = $this->select($column, $order, $offset, $length, $this->model);
        if($ajax == 1 || $ajax == "1"){
            return json_encode($data);
        }
        else {
            $this->app->render($this->view, array("data"=>$data));
        }
    }

    public function fetchFamily($column, $order, $offset, $length){
        return $this->select($column, $order, $offset, $length);
    }

    public function deleteFamily(){

    }

    public function addFamily($data){
        return $this->insert($data, 1, $this->model, $this->view);
    }

    public function editFamily($data){
        return $this->update($data,$this->model);
    }


    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/samples2/RNA", "fixRNA.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }

}