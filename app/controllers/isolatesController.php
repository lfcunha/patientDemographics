<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 7/9/14
 * Time: 5:43 PM
 */


use \isolates;

require_once __DIR__."/../models/auxiliaryTablesModel.php";
require_once ROOT.'/app/models/isolatesModel.php';

class isolatesController extends \Slim\slim {

    public function __construct(){
        $this->atm=new \auxiliary\auxiliaryTablesModel();
        $this->isolatesModel=new isolatesModel(array(), $this->atm);
        $this->app = \Slim\Slim::getInstance();
        $this->env=$this->app->environment();
    }

    public function processAjax($data){
        $this->validate=new isolatesModel($data, $this->atm);
        return json_encode($this->validate->validateRow($data,1));
    }

    public function saveEditToDB($data){
        $this->model=new isolatesModel($data, $this->atm);
        $result = $this->model->validateRow($data,1);
        if ($result==1){   //passed validation
            $res=$this->model->saveEditToDB($data);
            if($res[0]) {
                return 1;
            }
            else return 2;  //database failed to save
        }
        else
            return $result;
    }

    private function processResult($result){
        switch ($result[0]){
            case 1:
                //var_dump("1");
                #render "set message as OK";
                //var_dump($result);
                //it's just send the page through ajax, it doesn't actually render the page:
                //$this->app->render("fixIsolatesTemplate.twig", array("data"=>$result[1], "auxiliary"=>$atm));
                //use location.redirect withing the ajax function to render a result page
                $message='<div style="padding: 5px;"><div id="inner-message" class="alert alert-success" style="margin: 0 auto"><button type="button" class="close" data-dismiss="alert">&times;</button>Successfully submitted data!</div><div>';


                $this->app->flashnow('success', $message);
                $this->renderTableAjax("condensed");
                //header("Location: /isolates");
                break;
            case 2:
                #render "Error Saving to database. Try again or contact admin if problem persists";
                //var_dump("2");
                //var_dump($result);
                //$this->app->render("fixIsolatesTemplate.twig", array("data"=>$result[1], "auxiliary"=>$atm));
                return 2;
                break;
            case 3:
                #render (fixExcel_template, $result[1])
                //var_dump($result[1]);
                $atm=$this->atm->getModel();
                //var_dump($result[1]);

                $message='<div style="padding: 5px;"><div id="inner-message" class="alert alert-danger" style="margin: 0 auto"><button type="button" class="close" data-dismiss="alert">&times;</button>Please fix the fields in red. Hover the cell for more info </div><div>';

                $this->app->flashnow('success', $message);
                $this->app->render("fixIsolatesTemplate.twig", array("data"=>$result[1], "auxiliary"=>$atm)); #auxiliary is required
                break;
        }
    }

    public function processExcel($data){
        /* dependency injection of auxiliaryTablesModel
        *  into the constructor of the isolatesModel
        */
        $this->validateAndSaveExcel=new isolatesModel($data, $this->atm);
        $result=$this->validateAndSaveExcel->validateExcelData();   #call validation on data
        //return $result;
        $this->processResult($result);
    }




    public function saveTable(){

    }


    #$isolatesModel->fetchTable();

    public function renderTable($version){
        $tableData=$this->isolatesModel->fetchTable();
        $this->app->flashKeep();
        if ($version=="condensed"){
            //$this->app->flashnow('success', 'Successfully submitted data!');
            $this->app->render("isolatesTableEdit.twig", array("data"=>$tableData, "user"=>$_SESSION['user'] ));
        }
        else {
            $this->app->render("isolatesFullEdit.twig", array("data"=>$tableData, "user"=>$_SESSION['user'], "admin"=>$_SESSION['admin']));
        }
        return 0;
    }

    public function renderTableAjax($version){
        $this->app->flashKeep();
        $c=new isolatesAjax();
        $tableData=$c->fetchTablePagination(10,0, "idIsolates", "desc"); //(#rows to show, offset)
        //var_dump($tableData);

        if ($version=="condensed"){
            //$this->app->flashnow('success', 'Successfully submitted data!');
            $this->app->render('isolatesAjax.twig', array("data"=>$tableData['data'], 'totalRecords'=>$tableData['total'], 'records'=>$tableData['size'], "user"=>$_SESSION['user'], "admin"=>$_SESSION['admin'] ));
        }
        else {
            $this->app->render('isolatesFullAjax.twig', array("data"=>$tableData['data'], 'totalRecords'=>$tableData['total'], 'records'=>$tableData['size'], "user"=>$_SESSION['user'], "admin"=>$_SESSION['admin'] ));
        }
        return 0;
    }




    public function fetchRow($sid){
        $model=$this->isolatesModel->fetchRow($sid);
        //$atm=$this->atm->getModel();
        return $model;
        //$this->app->render("isolatesTable.twig", array("data"=>$tableData));
    }


    public function test(){
        return "test passed";
    }



    public function exportForSampleIds($data){
        //var_dump(explode(",",$data));
        $model= $this->isolatesModel;
        $result=$model->exportIsolates(explode(",",$data));

        //var_dump($result);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Isolates.xls"');
        header('Cache-Control: max-age=0');

        $objPHPExcel = new PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator($_SESSION['user'])
            ->setLastModifiedBy($_SESSION['user'])
            ->setTitle("Isolates")
            ->setSubject("Isolates")
            ->setDescription("Subset of isolates submitted for sequencing at Mount Sinai/CRIP")
            ->setKeywords("isolates crip influenza sequencing virus")
            ->setCategory("Isolates");
        $objPHPExcel->getActiveSheet()->setTitle('Isolates');



        $alpha = 'A';
        foreach($result[0] as $key=>$val){
            //var_dump($key);
            // $objPHPExcel->setActiveSheetIndex(0)
            //             ->setCellValueByColumnAndRow($i, 1, $key);
            if($key=="User") continue;
            $objPHPExcel->getActiveSheet()->getCell($alpha.'1')->setValueExplicit($key, PHPExcel_Cell_DataType::TYPE_STRING);
            $alpha++;
        }

        foreach($result as $row_=>$data) {
            $row=$row_+2;
            $i=0;
            foreach($data as $key=>$val){
                //var_dump($key);
                if($key=="User") continue;
                if(($key=="Isolation_Month" || $key=="Isolation_Day") && $val=="0") $val="";
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, $row, $val);
                $i++;
            }


        }

        $objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:AE1")->applyFromArray(array("font" => array( "bold" => true)));



        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');  //Excel2007
        // If you want to output e.g. a PDF file, simply do:
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
        //$objWriter->save('/data/www/crip/influenzadb/public/excel_files/MyExcel.xslx');

        $objWriter->save('php://output');

    }










}