<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 12/9/14
 * Time: 3:24 PM
 */


class baseController extends \Slim\slim {

    public function __construct() {
        parent::__construct();
        $this->app = \Slim\Slim::getInstance();
        $this->env=$this->app->environment();
    }


    private function getModel($table){
        switch($table) {
            case "patientModel":
                $model=new patientModel();
                return $model;
                break;
            case "familyModel":
                $model=new familyModel();
                return  $model;
                break;
            case "doctorModel":
                $model = new doctorModel();
                return $model;
                break;
        }
    }


    protected function getData($column, $order, $offset, $length, $table=""){
        $model=$this->getModel($table);
        $data=$model->getData($column, $order, $offset, $length);
        ChromePhp::log("data: " . $data['count']);
        return $data;
    }

    protected function saveData($dataArray=null, $ajax=0, $table="", $view=""){
        $model=$this->getModel($table);
        $data=$model->saveToDB($dataArray);
        if($ajax==1){
            return json_encode($data);
        }
        else $this->app->render($view, array("data"=>$data));
    }

    protected function editCell($data, $table){
        return $this->getModel($table)->saveEditToDb($data);
    }


    protected function processExcel_($data, $table, $redirect, $view){
        $model=$this->getModel($table);
        $this->validate=new validationController($data, $model->getRules());
        $result=$this->validate->validateExcelData();   #call validation on data
        //var_dump($result);
        //return 0;
        ChromePhp::log($result);

        if ($result[0]==1){

            $result = $model->saveToDB($data);
            if ($result[0]==1){
                $message='<div style="padding: 5px;"><div id="inner-message" class="alert alert-success" style="margin: 0 auto"><button type="button" class="close" data-dismiss="alert">&times;</button>Successfully submitted data!</div><div>';
                $this->app->flashnow('success', $message);
                $this->app->redirect($redirect);
            }
            else {
                //handle database error
            }

        }
        else if ($result[0]==3) {
             $message='<div style="padding: 5px;"><div id="inner-message" class="alert alert-danger" style="margin: 0 auto"><button type="button" class="close" data-dismiss="alert">&times;</button>Please fix the fields in red. Hover the cell for more info </div><div>';
            $this->app->flashnow('success', $message);
            $this->app->render($view, array("data"=>$result[1]));
        }
    }


    public function processAjax_($data, $table){
        $model=$this->getModel($table);
        $this->validate=new validationController($data, $model->getRules());
        return json_encode($this->validate->validateRow($data,1));
    }


    public function search($table, $length, $offset, $search, $column, $order){
        $table=strtolower($table);

        $translate=array("individuals"=>"individualsModel", "dna"=>"DNAModel", "rna"=>"RNAModel", "viablecell"=>"viableCellModel", "cellline"=>"cellLineModel", "individuals"=>"individualsModel");

        //return array($translate[$table], $table, $length, $offset, $search, $column, $order[0]);
        $model=$this->getModel($translate[$table]);
        //return $model;
        return $model->search($table, $length, $offset, $search, $column, $order[0]);

    }
}