<?php


class patientController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="patientModel";
        $this->view = "patientsView.twig";
        $this->dbh = $this->app->db;
        $this->table=$this->env["dbtables"];
    }


    public function getPatients($column, $order, $offset, $length, $ajax){
        $data = $this->select($column, $order, $offset, $length, $this->model);

        foreach ($data['data'] as $key =>$value){
            $rel = $data['data'][$key]['relationship'];
            switch ($rel) {
                case "01":
                    $data['data'][$key]['relationship'] = "proband";
                    break;
                case "02":
                    $data['data'][$key]['relationship'] = "mother";
                    break;
                case "03":
                    $data['data'][$key]['relationship'] = "father";
                    break;
                case "04":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "05":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "06":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "07":
                    $data['data'][$key]['relationship'] = "other";
                    break;
            }

            if (count($data['data'][$key]['medicalChartId']) > 1) $data['data'][$key]['patientId'] = $data['data'][$key]['medicalChartId'];
            else {
                $dob = new Datetime($data['data'][$key]["dateOfBirth"]);
                $data['data'][$key]['patientId'] = $data['data'][$key]["lastName"][0].$data['data'][$key]["firstName"][0].$dob->format("ymd") ;
            }

            $created = new DateTime($data['data'][$key]['created']);
            $data['data'][$key]['familyId'] = "F".$created->format("y").$data['data'][$key]['familyId'];
            $data['data'][$key]['familyIndividualId'] =  $data['data'][$key]['familyId']."-" . $rel;
        }


        if($ajax == 1 || $ajax == "1"){
            return json_encode($data);
        }
        else {
            $this->app->render($this->view, array("data"=>$data));
        }

    }

    public function fetchPatient($column, $order, $offset, $length){
        return $this->select($column, $order, $offset, $length, 0, $this->model);
    }

    public function deletePatient(){

    }

    public function addPatient(){

    }

    public function editPatient($data){
        return $this->update($data,$this->model);
    }


    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/demographicsDB/patients", "fixPatient.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }


    private function saveToSamplesDB_($data){

        try{
            $sth=$this->dbh->prepare('select `individualID` from `individuals` ORDER BY `id` DESC LIMIT 1' );
            $sth->execute();
            $res=$sth->fetch(PDO::FETCH_ASSOC);
            $individualID=explode("P",$res["individualID"]);
            $individualID=intval(substr($individualID[1],2));
        }
        catch(PDOException $e) {
            return array(2, $e->getMessage());
        }

        for ($i=0; $i<count($data["data"]); $i++){
            $individualID++;
            $individualID=str_pad(intval($individualID), 4 , "0", STR_PAD_LEFT) ;
            $date = date_create($data["data"][$i]["arrivalDate"]);
            $year = date_format($date, 'y');
            $individualID_= "P" . $year . $individualID;
            $query="INSERT INTO `cunhal01_a`.`".$data["table"]."` (";

            foreach ($this->table[$data["table"]] as $key) {
                $query.="`";
                $query.=$key;
                $query .= "`,";
            }
            $query = rtrim($query, ",");
            $query .=") VALUES ('','";
            $query .=$individualID_;
            $query .="',";
            foreach (array_slice($this->table[$data["table"]],2,count($this->table[$data["table"]])-4) as $key) {
                $query.="'";
                $query.= $data["data"][$i][$key];
                $query.= "',";
            }

            //$query = rtrim($query, ",");
            $query.="NOW(),NOW())";

          //  var_dump($query);
          //  die();
            try {
                $stmt=$this->dbh->prepare($query);
                //return array(2,$stmt, $res);
                $res=$stmt->execute();
                if(!$res) return array("error", $stmt);
            }
            catch(PDOException $e)
            {
                return array(2, $e->getMessage());
            }
        }
        return 1;
    }

    public function generatePnumber($data){


        $count=0;
        $dataArray = array();
        $dataArray["data"]=array();
        $dataArray["table"]="individuals";

        $data=explode(",", $data);
        foreach ($data as $id){


            $sth=$this->dbh->prepare("SELECT * FROM  `patient_doc` WHERE  `id` = ".$id." LIMIT 0 , 30");
            $sth->execute();
            $res=$sth->fetch(PDO::FETCH_ASSOC);
            $created = new DateTime($res["created"]);
            $familyID="F" .$created->format("y"). $res["familyId"] . "-"  . $res["relationship"];

            $dataForDb=array("arrivalDate"=>date("Y-m-d"), "project"=>"", "clinician" =>"", "originalLabel"=>"", "familyID"=>$familyID, "gender"=>"", "dateofbirth"=>null, "relationship"=>"", "phenotype"=>"", "cyrilic"=>"", "intExt"=>"", "institute"=>"", "country"=>"", "receiverName"=>"", "alternativeID"=>"", "notes"=>"", "patientId"=>$id);
            $dataArray["data"][$count]=$dataForDb;
            $count++;
        }
        //var_dump($dataArray);
        //die();
        $result = $this->saveToSamplesDB_($dataArray);
        if ($result == 1) {
            $this->app->redirect('/samples2/individuals#');
        }
        //return $this->processAjax_($data, $this->model);
    }




    public function exportPatients($data){
        $this->export($data, $this->model, "patients");


    }




}