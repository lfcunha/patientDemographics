<?php
error_reporting(E_ALL ^ E_NOTICE);

$app = \Slim\Slim::getInstance();
require "./config.php";

require ROOT.'/vendor/pimple/pimple/src/Pimple/Container.php';
use Pimple\Container;
$container = new Container();

require_once ROOT."/vendor/autoload.php";

//require_once __DIR__."/../models/auxiliaryTablesModel.php";
//require_once ROOT.'/vendor/php-excel-reader-2.21/excel_reader2.php';
//require_once ROOT.'/app/models/isolatesModel.php';
require_once ROOT."/vendor/predis/predis/examples/shared.php";
//require_once ROOT.'/app/php-excel-reader-2.21/excel_reader2.php';



use \isolates;


class chunkReadFilter implements PHPExcel_Reader_IReadFilter
{
    //put your code here
    private $_startRow = 0;
    private $_endRow   = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize) {
        $this->_startRow = $startRow;
        $this->_endRow   = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        //  Only read the heading row, and the configured rows
        //echo $column." ".$row."\n";
        if (($row == 1) ||
            ($row >= $this->_startRow && $row < $this->_endRow)) {
            //echo $row."\n";
            return true;
        }
        return false;
    }


}


class readExcelController {

    public function __construct($file) {
        $this->excelFile=$file; #file posted from the upload html form
        $this->spreadsheetExcelReader = PHPExcel_IOFactory::createReaderForFile($file);
        $this->spreadsheetExcelReader->setReadDataOnly();
        $chunkFilter = new chunkReadFilter();
        $chunkFilter->setRows(0,30);
        $this->spreadsheetExcelReader->setReadFilter($chunkFilter);
        $loadSheets = array('data');
        $this->spreadsheetExcelReader->setLoadSheetsOnly($loadSheets);
        $this->excelObj=$this->spreadsheetExcelReader->load($file);
        $this->excelDataSheet =$this->excelObj->getActiveSheet()->toArray(null, true,true,true);
        $this->patient  = array("referralDate","referralComments","firstName","lastName","relationship","referralPatientPhone","referralPatientEmail","medicalChartId","medicalChart","address","dateOfBirth","selfGender","selfEthnicity","familySelfComments","noteIfFamilyRelatives","diagnosisReferral","diagnosisSelf","diagnosisInDb","preGeneticScreening","brainSurgery","brainSampleInBank","phoneConsented","fullConsented","notes","familyId","fatherId","motherId");
        $this->doctor          = array("id","firstName","lastName","office","institution","city","country","created","modified");
        $this->family          = array("id", "doctorId","created", "modified");
        $this->ExcelHeaders = array("A", "B", "C", "D", "E","F","G","H","I","J","K","L","M","N","O","P", "Q","R", "S", "T", "U", "V", "W","X","Y","Z", "AA", "BB");
    }

    function parseExcelToArray($table){
        //var_dump($table);
        $formData=false;
        $rows=count($this->excelDataSheet);
        //excel rows indexed 1 based, so upper limit must be #rows+1
        for ($i=0; $i<$rows+1;$i++){
            if (($this->excelDataSheet[$i]["A"]=="" || $this->excelDataSheet[$i]["A"]==null) and ($this->excelDataSheet[$i]["B"]=="" || $this->excelDataSheet[$i]["B"]==null)and($this->excelDataSheet[$i]["C"]=="" || $this->excelDataSheet[$i]["C"]==null)) continue;
            $formData[$i]=array();

            switch($table) {
                case "patient":
                    $j=0;
                    foreach($this->patient as $column){
                        $formData[$i][$column]=$this->excelDataSheet[$i][$this->ExcelHeaders[$j]];
                        $j++;
                    }
                    break;
                case "doctor":
                    $j=0;
                    foreach($this->DNA as $column){
                        $formData[$i][$column]=$this->excelDataSheet[$i][$this->ExcelHeaders[$j]];
                        $j++;
                    }
                    break;
                case "family":
                    $j=0;
                    foreach($this->RNA as $column){
                        $formData[$i][$column]=$this->excelDataSheet[$i][$this->ExcelHeaders[$j]];
                        $j++;
                    }
                    break;
            }


        }
        return $formData;
    }


    public function init($table){
        ChromePhp::log("Chrome logger is working");
        /*
         * build array of the data in the excel spreadsheet
         */
        $formData=$this->parseExcelToArray($table);

        foreach ($formData as $index=>$row) {
            foreach($formData[$index] as $key=>$value){
                if ( $value=="") {
                    $value=null;
                    $formData[$index][$key]=$value;
                }
            }
        }

        switch($table) {
            case "patient":
                $this->processExcel=new patientController();
                break;
        }
        #var_dump(array_slice($formData, 1));
        ChromePhp::log($formData[2]);
        echo $this->processExcel->processExcel(array_slice($formData, 1));   #call validation on data

    }




    public static function revalidate($data){

        switch($data["table"]){
            case "individuals":
                $headers=array("a", "individualID","arrivalDate","project", "clinician", "originalLabel", "familyID", "gender", "dateofbirth", "relationship", "phenotype", "cyrilic","intExt","institute","country","receiverName","alternativeID","notes");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("individualID"=>"individualID","arrivalDate"=>"arrivalDate","project"=>"project", "clinician"=>"clinician", "originalLabel" => "originalLabel", "familyID"=>"familyID", "gender"=>"gender", "dateofbirth"=>"dateofbirth", "relationship"=>"relationship", "phenotype"=>"phenotype", "cyrilic"=>"cyrilic","intExt"=>"intExt","institute"=>"institute","country"=>"country","receiverName"=>"receiverName","alternativeID"=>"alternativeID","notes"=>"notes");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new individualsController();
                break;

            case "dna":
                $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("individualID"=>"individualID","id"=>"id","arrivalSource"=>"arrivalSource", "biologicalSource"=>"biologicalSource", "concentration"=>"concentration", "volume"=>"volume", "location"=>"location", "date"=>"date", "notes"=>"notes", "status"=>"status");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new DNAController();
                break;
            case "rna":
                $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "RIN", "location", "date", "notes", "status");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("individualID"=>"individualID","id"=>"id","arrivalSource"=>"arrivalSource", "biologicalSource"=>"biologicalSource", "concentration"=>"concentration", "volume"=>"volume", "Rin"=>"RIN", "location"=>"location",  "date"=>"date", "notes"=>"notes", "status"=>"status");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new RNAController();
                break;
            case "cellline":
                $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("individualID"=>"individualID","id"=>"id","arrivalSource"=>"arrivalSource", "biologicalSource"=>"biologicalSource", "concentration"=>"concentration", "volume"=>"volume", "location"=>"location", "date"=>"date", "notes"=>"notes", "status"=>"status");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new cellLineController();
                break;
            case "viablecell":
                $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("individualID"=>"individualID","id"=>"id","arrivalSource"=>"arrivalSource", "biologicalSource"=>"biologicalSource", "concentration"=>"concentration", "volume"=>"volume", "location"=>"location", "date"=>"date", "notes"=>"notes", "status"=>"status");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new viableCellController();
                break;
            case "patient":
                $headers=array("a", "referralDate","referralComments","firstName","lastName","relationship","referralPatientPhone","referralPatientEmail","medicalChartId","medicalChart","address","dateOfBirth","selfGender","selfEthnicity","familySelfComments","noteIfFamilyRelatives","diagnosisReferral","diagnosisSelf","diagnosisInDb","preGeneticScreening","brainSurgery","brainSampleInBank","phoneConsented","fullConsented","notes","familyId","fatherId","motherId");
                $formData=array();
                foreach ($data as $key=>$value) {
                    $row_col = explode("_", $key);
                    if ($value=="") $value=null;
                    $formData[1]=  array();
                    $formData[1]=  array("referralDate"=>"referralDate","referralComments"=>"referralComments","firstName"=>"firstName","lastName"=>"lastName","relationship"=>"relationship","referralPatientPhone"=>"referralPatientPhone","referralPatientEmail"=>"referralPatientEmail","medicalChartId"=>"medicalChartId","medicalChart"=>"medicalChart","address"=>"address","dateOfBirth"=>"dateOfBirth","selfGender"=>"selfGender","selfEthnicity"=>"selfEthnicity","familySelfComments"=>"familySelfComments","noteIfFamilyRelatives"=>"noteIfFamilyRelatives","diagnosisReferral"=>"diagnosisReferral","diagnosisSelf"=>"diagnosisSelf","diagnosisInDb"=>"diagnosisInDb","preGeneticScreening"=>"preGeneticScreening","brainSurgery"=>"brainSurgery","brainSampleInBank"=>"brainSampleInBank","phoneConsented"=>"phoneConsented","fullConsented"=>"fullConsented","notes"=>"notes","familyId"=>"familyId","fatherId"=>"fatherId","motherId"=>"fatherId");
                    if (!$formData[$row_col[0]+1]) $formData[$row_col[0]+1]=array();
                    $formData[$row_col[0]+1][$headers[$row_col[1]]]=$value;
                }
                $processExcel=new patientController();
                break;

        }


        $result=$processExcel->processExcel(array_slice($formData, 1));
        return $result;
    }


}

