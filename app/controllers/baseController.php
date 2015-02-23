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


    protected function select($column, $order, $offset, $length, $table=""){
        $model = $this->getModel($table);
        $data = $model->select($column, $order, $offset, $length);
        return $data;
    }

    protected function insert($dataArray=null, $ajax=0, $table="", $view=""){
        $model=$this->getModel($table);
        $data=$model->insert($dataArray);
        if($ajax==1){
            return $data;
        }
        else $this->app->render($view, array("data"=>$data));
    }

    protected function update($data, $table){
        return $this->getModel($table)->update($data);
    }


    protected function processExcel_($data, $table, $redirect, $view){
        $model=$this->getModel($table);
        $this->validate=new validationController($data, $model->getRules());
        $result=$this->validate->validateExcelData();   #call validation on data

        if ($result[0]==1){

            $result = $model->insert($data);
            if ($result[0]==1 || $result==1){
                $message='<div style="padding: 5px;"><div id="inner-message" class="alert alert-success" style="margin: 0 auto"><button type="button" class="close" data-dismiss="alert">&times;</button>Successfully submitted data!</div><div>';
                $this->app->flashnow('success', $message);
                $this->app->redirect($redirect);
            }
            else {
                //handle database error
            }
        }
        else if ($result[0]==3) {
            //var_dump($result[1]);
            //exit;
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
        $translate=array("individuals"=>"individualsModel", "dna"=>"DNAModel", "rna"=>"RNAModel", "viablecell"=>"viableCellModel", "cellline"=>"cellLineModel", "individuals"=>"individualsModel", "patients" => "patientModel");
        $model=$this->getModel($translate[$table]);

        ChromePhp::log( $table . " / " . $length . " / " . $offset . " / " . $search . " / " . $column . " / " . $order );
        //return $model->name();
        return $model->search($table, $length, $offset, $search, $column, $order[0]);

    }
}